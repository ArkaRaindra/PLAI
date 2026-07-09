<?php

namespace App\Filament\SuperAdmin\Resources\SelfAssessments\Pages;

use App\Filament\SuperAdmin\Resources\SelfAssessments\SelfAssessmentResource;
use App\Models\SelfAssessment;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class ViewSelfAssessment extends ViewRecord
{
    protected static string $resource = SelfAssessmentResource::class;

    protected function resolveRecord(int | string $key): Model
    {
        $record = parent::resolveRecord($key);

        if ($record instanceof SelfAssessment) {
            $record->load([
                'organizationUnit',
                'qualityPeriod',
                'createdBy',
                'submittedBy',
                'approvedBy',
                'rejectedBy',
                'details.realization.target.indicator',
            ]);
        }

        return $record;
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
            EditAction::make()
                ->visible(fn ($record) => in_array($record->status, ['draft', 'rejected'], true)),
            SelfAssessmentResource::submitAction(),
            SelfAssessmentResource::approveAction(),
            SelfAssessmentResource::rejectAction(),
        ];
    }
}
