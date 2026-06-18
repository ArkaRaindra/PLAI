<?php

namespace App\Filament\Prodi\Widgets;

use App\Models\AuditEvidence;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Widgets\Concerns\CanPoll;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class StatusKeberhasilanWidget extends Widget implements HasSchemas
{
    use CanPoll;
    use InteractsWithSchemas;

    protected ?string $heading = 'Status Keberhasilan';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.prodi.widgets.status-keberhasilan-widget';

    /**
     * @var Collection<int, float>|null
     */
    protected ?Collection $averageScoreByEvidence = null;

    public function statusKeberhasilanInfolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Status Kecukupan')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('nilai_assesmen_kecukupan')
                            ->label('Nilai assesmen Kecukupan')
                            ->state($this->formatScore($this->getAdequacyAssessmentScore())),
                        TextEntry::make('syarat_terakreditasi')
                            ->label('Syarat perlu terakreditasi')
                            ->state($this->formatStatusState($this->isTerakreditasiMet(), $this->formatScore($this->getAdequacyAssessmentScore()), $this->formatScore($this->getMaximumScoreForScoredEvidence())))
                            ->badge()
                            ->color(fn (string $state): string => str($state)->startsWith('Terpenuhi') ? 'success' : 'danger'),
                        TextEntry::make('syarat_peringkat_unggul')
                            ->label('Syarat perlu Peringkat Unggul')
                            ->state($this->formatStatusState($this->isUnggulMet(), $this->formatScore($this->getUnggulRequiredScore()), $this->formatScore($this->getMaximumScore())))
                            ->badge()
                            ->color(fn (string $state): string => str($state)->startsWith('Terpenuhi') ? 'success' : 'danger'),
                        TextEntry::make('syarat_peringkat_baik_sekali')
                            ->label('Syarat perlu peringkat Baik Sekali')
                            ->state($this->formatStatusState($this->isBaikSekaliMet(), $this->formatScore($this->getBaikSekaliRequiredScore()), $this->formatScore($this->getMaximumScore())))
                            ->badge()
                            ->color(fn (string $state): string => str($state)->startsWith('Terpenuhi') ? 'success' : 'danger'),
                    ]),
                Section::make('Element Pertimbangan dan Berkas')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('element_pertimbangan')
                            ->label('Element pertimbangan')
                            ->state($this->getElementConsideredCount().' Element'),
                        TextEntry::make('ketersediaan_berkas')
                            ->label('Ketersediaan Berkas')
                            ->state($this->getFileAvailabilityCount().' Berkas'),
                    ]),
            ]);
    }

    private function getAdequacyAssessmentScore(): float
    {
        return round($this->getMaximumScoreForScoredEvidence() * 0.5, 2);
    }

    private function getMaximumScore(): float
    {
        return $this->getEvidenceCount() * 4;
    }

    private function getMaximumScoreForScoredEvidence(): float
    {
        return $this->getScoredEvidenceCount() * 4;
    }

    private function getUnggulRequiredScore(): float
    {
        return round($this->getMaximumScore() * 0.90, 2);
    }

    private function getBaikSekaliRequiredScore(): float
    {
        return round($this->getMaximumScore() * 0.75, 2);
    }

    private function isTerakreditasiMet(): bool
    {
        return $this->getCurrentScore() >= $this->getAdequacyAssessmentScore();
    }

    private function isUnggulMet(): bool
    {
        return $this->getCurrentScore() >= $this->getUnggulRequiredScore();
    }

    private function isBaikSekaliMet(): bool
    {
        return $this->getCurrentScore() >= $this->getBaikSekaliRequiredScore();
    }

    private function getCurrentScore(): float
    {
        return round($this->getAverageScoreByEvidence()->sum(), 2);
    }

    private function getEvidenceCount(): int
    {
        return AuditEvidence::query()
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->when(auth()->user()->period_id, fn ($query) => $query->where('period_id', auth()->user()->period_id))
            ->count();
    }

    private function getScoredEvidenceCount(): int
    {
        return $this->getAverageScoreByEvidence()->count();
    }

    private function getElementConsideredCount(): int
    {
        return AuditEvidence::query()
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->when(auth()->user()->period_id, fn ($query) => $query->where('period_id', auth()->user()->period_id))
            ->with('subStandards')
            ->get()
            ->flatMap(fn (AuditEvidence $evidence): Collection => $evidence->subStandards->pluck('id'))
            ->unique()
            ->count();
    }

    private function getFileAvailabilityCount(): int
    {
        return AuditEvidence::query()
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->where(fn ($query) => $query
                ->whereNotNull('file_path')
                ->orWhereNotNull('google_drive_link'))
            ->when(auth()->user()->period_id, fn ($query) => $query->where('period_id', auth()->user()->period_id))
            ->count();
    }

    /**
     * @return Collection<int, float>
     */
    private function getAverageScoreByEvidence(): Collection
    {
        return $this->averageScoreByEvidence ??= AuditEvidence::query()
            ->join('audit_scores', 'audit_evidences.id', '=', 'audit_scores.audit_evidence_id')
            ->selectRaw('audit_evidences.id, AVG(audit_scores.score) as average_score')
            ->where('audit_evidences.user_id', auth()->id())
            ->where('audit_evidences.status', 'approved')
            ->when(auth()->user()->period_id, fn ($query) => $query->where('audit_evidences.period_id', auth()->user()->period_id))
            ->groupBy('audit_evidences.id')
            ->get()
            ->mapWithKeys(fn ($row): array => [$row->id => (float) $row->average_score]);
    }

    private function formatScore(float $score): string
    {
        return number_format($score, 2, ',', '.');
    }

    private function formatStatusState(bool $isMet, string $requiredScore, string $maximumScore): string
    {
        return ($isMet ? 'Terpenuhi - ' : 'Belum Terpenuhi - ').
            "Diperlukan {$requiredScore} dari {$maximumScore}";
    }

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['prodi', 'unit-penunjang', 'fakultas', 'super-admin']);
    }
}
