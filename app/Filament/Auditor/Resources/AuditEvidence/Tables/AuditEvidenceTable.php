<?php

namespace App\Filament\Auditor\Resources\AuditEvidence\Tables;

use App\Models\AuditEvidence;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditEvidenceTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama'),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.studyProgramName')
                    ->label('Prodi'),
                TextColumn::make('subStandards.code')
                    ->label('Sub Standard')
                    ->getStateUsing(fn (AuditEvidence $record): string => $record->subStandards
                        ->pluck('code')
                        ->implode(', ') ?: ($record->subStandard?->code ?? '-'))
                    ->limitList(3),
                TextColumn::make('created_at')
                    ->dateTime(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'warning',
                        'approved' => 'success',
                        'returned' => 'danger',
                    })
                    ->label('Status'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Beri Penilaian'),
            ])
            ->toolbarActions([

            ]);
    }
}
