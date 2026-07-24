<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Models\AuditAssignment;
use App\Models\AuditCycle;
use App\Models\AuditFinding;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LhamiReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $routePath = 'lhami';

    protected static ?string $navigationLabel = 'LHAMI';

    protected static ?string $title = 'LHAMI — Laporan Hasil Audit Mutu Internal';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'AMI';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.super-admin.pages.lhami-report';

    /**
     * Currently selected Audit Cycle (from the table filter), or null for
     * "semua cycle".
     */
    public function currentCycleId(): ?int
    {
        $value = $this->getTableFilterState('audit_cycle_id')['value'] ?? null;

        return $value !== null && $value !== '' ? (int) $value : null;
    }

    public function summary(): array
    {
        $cycleId = $this->currentCycleId();

        $assignmentsTotal = AuditAssignment::query()
            ->when($cycleId, fn (Builder $q, int $id) => $q->where('audit_cycle_id', $id))
            ->count();

        $assignmentsByStatus = DB::table('audit_assignments')
            ->join('workflow_instances', function ($join): void {
                $join->on('workflow_instances.entity_id', '=', 'audit_assignments.id')
                    ->where('workflow_instances.entity_type', '=', 'audit_assignment');
            })
            ->when($cycleId, fn ($q, int $id) => $q->where('audit_assignments.audit_cycle_id', $id))
            ->selectRaw('workflow_instances.current_status as status, count(*) as total')
            ->groupBy('workflow_instances.current_status')
            ->pluck('total', 'status');

        $findingsBase = fn (): Builder => AuditFinding::query()
            ->whereHas('auditAssignment', fn (Builder $q) => $q->when(
                $cycleId,
                fn (Builder $q, int $id) => $q->where('audit_cycle_id', $id),
            ))->with('workflowInstance');

        $findingsTotal = $findingsBase()->count();

        $findingsByCategory = (clone $findingsBase())
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $findingsBySeverity = (clone $findingsBase())
            ->selectRaw('severity, count(*) as total')
            ->groupBy('severity')
            ->pluck('total', 'severity');

        $findingsByStatus = (clone $findingsBase())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $findingsByWorkflowStatus = (clone $findingsBase())
            ->get()
            ->groupBy(fn (AuditFinding $finding) => $finding->workflowInstance?->current_status ?? 'none')
            ->map(fn ($group) => $group->count());

        $closed = (int) ($findingsByStatus['closed'] ?? 0);

        return [
            'assignments_total' => $assignmentsTotal,
            'assignments_by_status' => $assignmentsByStatus,
            'findings_total' => $findingsTotal,
            'findings_by_category' => $findingsByCategory,
            'findings_by_severity' => $findingsBySeverity,
            'findings_by_status' => $findingsByStatus,
            'findings_by_workflow_status' => $findingsByWorkflowStatus,
            'completion_percentage' => $findingsTotal > 0
                ? round(($closed / $findingsTotal) * 100, 1)
                : 0.0,
        ];
    }

    public static function workflowStatusLabels(): array
    {
        return [
            'open' => 'Terbuka',
            'assigned' => 'Ditugaskan',
            'corrective_action' => 'Tindak Lanjut',
            'verification' => 'Verifikasi',
            'closed' => 'Selesai',
        ];
    }

    public static function categoryLabels(): array
    {
        return [
            'kts' => 'KTS',
            'ofi' => 'OFI',
            'observation' => 'Observation',
        ];
    }

    public static function severityLabels(): array
    {
        return [
            'major' => 'Major',
            'minor' => 'Minor',
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AuditFinding::query()->with([
                    'auditAssignment.organizationUnit',
                    'auditAssignment.auditorPosition.user',
                    'workflowInstance',
                ])
            )
            ->heading('Rekap Temuan')
            ->description('Rekapitulasi seluruh temuan Audit Mutu Internal (LHAMI)')
            ->columns([
                TextColumn::make('auditAssignment.organizationUnit.name')
                    ->label('Unit Auditee')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('auditAssignment.auditorPosition.user.name')
                    ->label('Auditor')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Judul Temuan')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::categoryLabels()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'kts' => 'danger',
                        'ofi' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('severity')
                    ->label('Severity')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'major' ? 'Major' : 'Minor')
                    ->color(fn (string $state): string => $state === 'major' ? 'danger' : 'warning'),
                TextColumn::make('workflowInstance.current_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state !== null
                        ? (self::workflowStatusLabels()[$state] ?? $state)
                        : '-')
                    ->color(fn (?string $state): string => $state !== null
                        ? (['open' => 'gray', 'assigned' => 'info', 'corrective_action' => 'warning', 'verification' => 'primary', 'closed' => 'success'][$state] ?? 'gray')
                        : 'gray'),
                TextColumn::make('due_date')
                    ->label('Batas Waktu')
                    ->date()
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->groups([
                Group::make('auditAssignment.organizationUnit.name')->label('Unit Auditee'),
            ])
            ->filters([
                SelectFilter::make('audit_cycle_id')
                    ->label('Audit Cycle')
                    ->options(fn (): array => AuditCycle::query()
                        ->with(['qualityPeriod', 'checklistTemplate'])
                        ->get()
                        ->mapWithKeys(fn (AuditCycle $cycle): array => [$cycle->id => $cycle->displayTitle()])
                        ->all())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $q, $value) => $q->whereHas(
                            'auditAssignment',
                            fn (Builder $inner) => $inner->where('audit_cycle_id', $value),
                        ),
                    )),
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(self::categoryLabels()),
                SelectFilter::make('severity')
                    ->label('Severity')
                    ->options(self::severityLabels()),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Terbuka',
                        'closed' => 'Ditutup',
                    ]),
            ])
            ->defaultSort('due_date');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label('Export CSV')
                ->icon(Heroicon::ArrowDownTray)
                ->color('gray')
                ->action(fn (): StreamedResponse => $this->exportRekapTemuanToCsv()),
        ];
    }

    private function exportRekapTemuanToCsv(): StreamedResponse
    {
        $findings = $this->getFilteredTableQuery()
            ->with([
                'auditAssignment.organizationUnit',
                'auditAssignment.auditorPosition.user',
                'workflowInstance',
            ])
            ->get();

        $filename = 'lhami-rekap-temuan-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($findings): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Unit Auditee',
                'Auditor',
                'Judul Temuan',
                'Kategori',
                'Severity',
                'Status Temuan',
                'Batas Waktu',
                'Deskripsi',
            ]);

            foreach ($findings as $finding) {
                fputcsv($handle, [
                    $finding->auditAssignment?->organizationUnit?->name ?? '-',
                    $finding->auditAssignment?->auditorPosition?->user?->name ?? '-',
                    $finding->title,
                    self::categoryLabels()[$finding->category] ?? $finding->category,
                    self::severityLabels()[$finding->severity] ?? $finding->severity,
                    self::workflowStatusLabels()[$finding->workflowInstance?->current_status ?? ''] ?? '-',
                    $finding->due_date?->format('Y-m-d') ?? '-',
                    (string) Str::of($finding->description)->squish(),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
