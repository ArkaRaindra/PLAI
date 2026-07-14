<?php

namespace App\Filament\SuperAdmin\Resources\EvidenceReviews\Schemas;

use App\Models\EvidenceReview;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class EvidenceReviewInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Evidence')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('evidence.title')->label('Judul'),
                            TextEntry::make('evidence.organizationUnit.name')->label('Unit Organisasi'),
                            TextEntry::make('evidence.type')
                                ->label('Tipe')
                                ->badge()
                                ->placeholder('-')
                                ->formatStateUsing(fn (?string $state): string => match ($state) {
                                    'file' => 'File',
                                    'url' => 'URL',
                                    default => $state ?? '-',
                                }),
                            TextEntry::make('evidence.file_path')
                                ->label('File')
                                ->placeholder('-')
                                ->visible(fn (EvidenceReview $record): bool => $record->evidence?->type === 'file')
                                ->formatStateUsing(function (?string $state, EvidenceReview $record): HtmlString {
                                    if (blank($state)) {
                                        return new HtmlString('-');
                                    }

                                    return new HtmlString(
                                        '<a class="text-primary-600 underline" href="'
                                        .e(route('evidences.download', $record->evidence))
                                        .'" target="_blank" rel="noopener">Unduh file</a>'
                                    );
                                }),
                            TextEntry::make('evidence.url_path')->label('URL')->placeholder('-'),
                            TextEntry::make('evidence.current_version')->label('Versi Saat Ini')->placeholder('-'),
                            TextEntry::make('evidence.createdBy.name')->label('Dibuat Oleh')->placeholder('-'),
                            TextEntry::make('evidence.description')->label('Deskripsi')->placeholder('-')->columnSpanFull(),
                        ]),
                    ]),
                Section::make('Review')
                    ->headerActions([
                        Action::make('updateNotes')
                            ->label('Catatan Review')
                            ->icon(Heroicon::DocumentText)
                            ->color('gray')
                            ->modalHeading('Update Catatan Review')
                            ->modalSubmitActionLabel('Simpan')
                            ->form([
                                Textarea::make('notes')->label('Catatan Review')->columnSpanFull()->default(fn (EvidenceReview $record): ?string => $record->review_notes),
                            ])
                            ->authorize(fn (): bool => Auth::user()?->can('evidence.review') ?? false)
                            ->action(function (EvidenceReview $record, array $data): void {
                                $record->update(['review_notes' => $data['notes']]);

                                Notification::make()->title('Catatan review diperbarui')->success()->send();
                            }),
                    ])
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('status')
                                ->label('Status Review')
                                ->badge()
                                ->formatStateUsing(fn (?string $state): string => match ($state) {
                                    'pending' => 'Menunggu Review',
                                    'review' => 'Direview',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    default => $state ?? '-',
                                })
                                ->color(fn (?string $state): string => match ($state) {
                                    'pending' => 'gray',
                                    'review' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    default => 'gray',
                                }),
                            TextEntry::make('review_notes')->label('Catatan Review')->placeholder('-')->columnSpanFull(),
                            TextEntry::make('reviewed_at')->label('Direview Pada')->dateTime()->placeholder('-'),
                        ]),
                    ]),
            ]);
    }
}
