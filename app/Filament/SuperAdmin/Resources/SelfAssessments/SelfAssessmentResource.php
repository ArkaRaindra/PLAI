<?php

namespace App\Filament\SuperAdmin\Resources\SelfAssessments;

use App\Filament\SuperAdmin\Resources\SelfAssessments\Pages\CreateSelfAssessment;
use App\Filament\SuperAdmin\Resources\SelfAssessments\Pages\EditSelfAssessment;
use App\Filament\SuperAdmin\Resources\SelfAssessments\Pages\ListSelfAssessments;
use App\Filament\SuperAdmin\Resources\SelfAssessments\Pages\ViewSelfAssessment;
use App\Filament\SuperAdmin\Resources\SelfAssessments\Schemas\SelfAssessmentForm;
use App\Filament\SuperAdmin\Resources\SelfAssessments\Schemas\SelfAssessmentInfolist;
use App\Filament\SuperAdmin\Resources\SelfAssessments\Tables\SelfAssessmentsTable;
use App\Models\SelfAssessment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SelfAssessmentResource extends Resource
{
    protected static ?string $model = SelfAssessment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SelfAssessmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SelfAssessmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SelfAssessmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSelfAssessments::route('/'),
            'create' => CreateSelfAssessment::route('/create'),
            'view' => ViewSelfAssessment::route('/{record}'),
            'edit' => EditSelfAssessment::route('/{record}/edit'),
        ];
    }
}
