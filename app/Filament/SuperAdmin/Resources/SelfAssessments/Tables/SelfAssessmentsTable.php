<?php

namespace App\Filament\SuperAdmin\Resources\SelfAssessments\Tables;

use App\Filament\SuperAdmin\Resources\SelfAssessments\SelfAssessmentResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SelfAssessmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('organizationUnit.name')
                    ->label('Unit Organisasi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('qualityPEriod.name')
                    ->label('Periode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'submitetd' => 'Diajukan',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('final_score')
                    ->label('Final Score')
                    ->numeric(decimalPlaces: 2)
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                SelfAssessmentResource::submitAction(),
                SelfAssessmentResource::approveAction(),
                SelfAssessmentResource::rejectAction(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
