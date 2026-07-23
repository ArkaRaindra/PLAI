<?php

namespace App\Filament\SuperAdmin\Resources\AuditAssignments\Schemas;

use App\Models\UserPosition;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class AuditAssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('audit_cycle_id'),
                Select::make('auditor_position_id')
                    ->label('Auditor')
                    ->helperText('Auditor dipilih berdasarkan jabatan yang sedang aktif')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->getSearchResultsUsing(function (string $search): array {
                        return UserPosition::query()
                            ->with(['user', 'position'])
                            ->where('is_active', true)
                            ->whereHas('user', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn (UserPosition $userPosition): array => [
                                $userPosition->id => "{$userPosition->user?->name} - {$userPosition->position?->name}",
                            ])
                            ->all();
                    })
                    ->getOptionLabelUsing(function ($value): ?string {
                        $userPosition = UserPosition::query()->with(['user', 'position'])->find($value);

                        return $userPosition === null
                            ? null
                            : "{$userPosition->user?->name} - {$userPosition->position?->name}";
                    })
                    ->getOptionLabelsUsing(function ($value): ?string {
                        $userPosition = UserPosition::query()->with(['user', 'position'])->find($value);

                        return $userPosition === null
                            ? null
                            : "{$userPosition->user?->name} - {$userPosition->position?->name}";
                    }),
                Select::make('organization_unit_id')
                    ->label('Unit Auditee')
                    ->relationship(
                        name: 'organizationUnit',
                        titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
