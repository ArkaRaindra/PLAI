<?php

namespace App\Filament\SuperAdmin\Resources\AuditChecklistResponses\Schemas;

use App\Models\AuditAssignment;
use App\Models\AuditChecklistResponse;
use App\Models\AuditChecklistTemplateItem;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AuditChecklistResponseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('audit_assignment_id'),
                Select::make('checklist_item_id')
                    ->label('Nomor Item Checklist')
                    ->required()
                    ->searchable()
                    ->reactive()
                    ->options(function (Get $get, ?AuditChecklistResponse $record = null) {
                        $assignmentId = $get('audit_assignment_id');

                        if (blank($assignmentId)) {
                            return [];
                        }

                        $assignment = AuditAssignment::query()->find($assignmentId);

                        if ($assignment === null) {
                            return [];
                        }

                        $answered = $assignment->responses()
                            ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                            ->pluck('checklist_item_id');

                        return AuditChecklistTemplateItem::query()
                            ->where('template_id', $assignment->auditCycle->checklist_template_id)
                            ->whereNotIn('id', $answered)
                            ->orderBy('sequence')
                            ->pluck('sequence', 'id');
                    }),
                Placeholder::make('checklist_item_question')
                    ->label('Pertanyaan')
                    ->live()
                    ->visible(fn (Get $get): bool => filled($get('checklist_item_id')))
                    ->content(function (Get $get): string {
                        $itemId = $get('checklist_item_id');

                        if (blank($itemId)) {
                            return '';
                        }

                        return (string) AuditChecklistTemplateItem::query()->where('id', $itemId)->value('question') ?? '';
                    })
                    ->columnSpanFull(),
                Textarea::make('answer')
                    ->label('Jawaban')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Catatan Auditor')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
