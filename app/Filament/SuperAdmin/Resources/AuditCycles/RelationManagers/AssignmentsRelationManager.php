<?php

namespace App\Filament\SuperAdmin\Resources\AuditCycles\RelationManagers;

use App\Models\UserPosition;
use Closure;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    protected static ?string $title = 'Penugasan Auditor';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('auditor_position_id')
                    ->label('Auditor')
                    ->options(
                        fn () => UserPosition::query()
                            ->with(['user', 'position', 'organizationUnit'])
                            ->where('is_active', true)
                            ->get()
                            ->mapWithKeys(fn (UserPosition $userPosition): array => [
                                $userPosition->id => "{$userPosition->user?->name} - {$userPosition->position?->name} ({$userPosition->organizationUnit?->name})",
                            ]),
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),

                Select::make('organization_unit_id')
                    ->label('Auditee')
                    ->relationship('organizationUnit', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->rules([
                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get): void {
                            $auditorPositionId = $get('auditor_position_id');

                            if (! $auditorPositionId) {
                                return;
                            }

                            $auditorPosition = UserPosition::query()->find($auditorPositionId);

                            if ($auditorPosition && (int) $auditorPosition->organization_unit_id === (int) $value) {
                                $fail('Auditor tidak dapat ditugaskan untuk mengaudit unitnya sendiri,');
                            }
                        },
                    ]),
                 DateTimePicker::make('assigned_at')
                    ->label('Tanggal Penugasan')
                    ->default(now())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('auditorPosition.user.name')
                    ->label('Auditor'),
                TextColumn::make('auditorPosition.position.name')
                    ->label('Jabatan'),
                TextColumn::make('auditorPosition.organizationUnit.name')
                    ->label('Unit Auditor'),
                TextColumn::make('organizationUnit.name')
                    ->label('Auditee'),
                TextColumn::make('assigned_at')
                    ->label('Tanggal Penugasan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('assigned_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::Plus),
            ])
            ->recordActions(ActionGroup::make([
                EditAction::make(),
                DeleteAction::make(),
            ]))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
