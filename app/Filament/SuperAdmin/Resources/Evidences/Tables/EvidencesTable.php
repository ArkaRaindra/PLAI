<?php

namespace App\Filament\SuperAdmin\Resources\Evidences\Tables;

use App\Filament\SuperAdmin\Resources\EvidenceLinks\EvidenceLinkResource;
use App\Filament\SuperAdmin\Resources\EvidenceReviews\EvidenceReviewResource;
use App\Filament\SuperAdmin\Resources\Evidences\EvidencesResource;
use App\Models\Evidences;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EvidencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(60),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'file' => 'File',
                        'url' => 'URL',
                        default => $state ?? '-',
                    }),
                TextColumn::make('file_path')
                    ->label('File')
                    ->limit(40)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('url_path')
                    ->label('URL')
                    ->limit(40)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('organizationUnit.name')
                    ->label('Unit Organisasi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('workflowInstance.current_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state !== null
                        ? (EvidencesResource::workflowStatusLabels()[$state] ?? $state)
                        : '-')
                    ->color(fn (?string $state): string => $state !== null
                        ? (EvidencesResource::workflowStatusColors()[$state] ?? 'gray')
                        : 'gray'),
                TextColumn::make('current_version')
                    ->label('Versi Saat Ini')
                    ->placeholder('-'),
                TextColumn::make('createdBy.name')
                    ->label('Diunggah Oleh')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('workflow_status')
                    ->label('Status')
                    ->options(EvidencesResource::workflowStatusLabels())
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;

                        return $query->status($value);
                    }),
                SelectFilter::make('organization_unit_id')
                    ->label('Unit Organisasi')
                    ->relationship('organizationUnit', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->schema([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $q, string $date): Builder => $q->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $q, string $date): Builder => $q->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = 'Dari '.$data['from'];
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = 'Sampai '.$data['until'];
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions(ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
                Action::make('manageLinks')
                    ->label('Kelola Link')
                    ->icon(Heroicon::Link)
                    ->url(fn (Evidences $record): string => EvidenceLinkResource::getListUrl($record->id)),
                EvidencesResource::submitAction(),
                EvidencesResource::startReviewAction(),
                EvidencesResource::approveAction(),
                EvidencesResource::rejectAction(),
                EvidencesResource::publishAction(),
            ]))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
