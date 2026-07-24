<?php

namespace App\Filament\SuperAdmin\Resources\CorrectiveActions\Schemas;

use App\Models\UserPosition;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CorrectiveActionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('audit_finding_id'),
                Hidden::make('organization_unit_id'),
                Section::make('Corrective Action')
                    ->schema([
                        Select::make('owner_position_id')
                            ->label('PIC')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(function (Get $get): array {
                                $unitId = $get('organization_unit_id');

                                return UserPosition::query()
                                    ->with(['user', 'position'])
                                    ->where('is_active', true)
                                    ->when($unitId, fn ($query) => $query->where('organization_unit_id', $unitId))
                                    ->get()
                                    ->mapWithKeys(fn (UserPosition $userPosition): array => [
                                        $userPosition->id => sprintf(
                                            '%s - %s',
                                            $userPosition->user?->name ?? '-',
                                            $userPosition->position->name ?? '-',
                                        ),
                                    ])
                                    ->all();
                            })
                            ->helperText('Hanya menampilkan jabatan aktif pada unit yang diaudit.'),
                        DatePicker::make('due_date')
                            ->label('Due Date')
                            ->required()
                            ->native(false),
                        Textarea::make('plan')
                            ->label('Action Plan')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
