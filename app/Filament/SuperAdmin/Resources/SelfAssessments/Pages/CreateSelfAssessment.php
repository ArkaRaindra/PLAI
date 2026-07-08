<?php

namespace App\Filament\SuperAdmin\Resources\SelfAssessments\Pages;

use App\Events\TraceabilityRecorded;
use App\Filament\SuperAdmin\Resources\SelfAssessments\SelfAssessmentResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;

class CreateSelfAssessment extends CreateRecord
{
    protected static string $resource = SelfAssessmentResource::class;

    protected static ?string $title = 'Tambah Self Assessment';

    protected static bool $canCreateAnother = false;

    protected function fillForm(): void
    {
        $this->form->fill([
            'status' => 'draft',
            'details' => [
                [
                    'realization_id' => null,
                    'score' => null,
                    'analysis' => null,
                    'strength' => null,
                    'weakness' => null,
                ],
            ],
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(fn (): string => self::$resource::getUrl('index'))
                ->button()
                ->color('gray')
                ->icon(Heroicon::ArrowLeft),
        ];
    }

    protected function afterCreate(): void
    {
        $selfAssessment = $this->record->loadMissing('details.realization');

        foreach ($selfAssessment->details as $detail) {
            if ($detail->realization === null) {
                continue;
            }

            TraceabilityRecorded::dispatch(
                source: $detail->realization,
                target: $selfAssessment,
                relationType: 'evaluated_in',
            );
        }
    }
}
