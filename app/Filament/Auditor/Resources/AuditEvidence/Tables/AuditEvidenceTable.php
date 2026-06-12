<?php

namespace App\Filament\Auditor\Resources\AuditEvidence\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditEvidenceTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.study_program')
                    ->label('Prodi'),
                TextColumn::make('sub_standard_indicator')
                    ->label('Sub Standar'),
                TextColumn::make('created_at')
                    ->dateTime(),
                TextColumn::make('auditor_note')
                    ->label('Catatan')
                    ->toggleable()
                    ->toggledHiddenByDefault(true),
                TextColumn::make('scores.score')
                    ->label('Nilai')
                    ->default('-'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Beri Penilaisn'),
            ])
            ->toolbarActions([

            ]);
    }
}
