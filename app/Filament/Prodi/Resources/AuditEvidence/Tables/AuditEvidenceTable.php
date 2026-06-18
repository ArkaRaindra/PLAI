<?php

namespace App\Filament\Prodi\Resources\AuditEvidence\Tables;

use App\Models\AuditEvidence;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditEvidenceTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('subStandards.code')
                    ->label('Sub Standar')
                    ->getStateUsing(fn (AuditEvidence $record): string => $record->subStandards
                        ->pluck('code')
                        ->implode(', ') ?: ($record->subStandard?->code ?? '-'))
                    ->limitList(3),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'warning',
                        'approved' => 'success',
                        'returned' => 'danger',
                    })
                    ->label('Status'),
                TextColumn::make('auditor_score')
                    ->label('Nilai')
                    ->default('-'),
                TextColumn::make('auditor_note')
                    ->limit(50)
                    ->label('Note'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('submitToAuditor')
                    ->label('Kirim ke auditor')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim bukti audit ke auditor?')
                    ->visible(fn (?AuditEvidence $record): bool => in_array($record?->status, ['draft', 'returned'], true))
                    ->action(function (AuditEvidence $record): void {
                        $record->update(['status' => 'submitted']);

                        Notification::make()
                            ->title('Bukti audit berhasil dikirim ke auditor.')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
