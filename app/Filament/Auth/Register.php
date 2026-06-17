<?php

namespace App\Filament\Auth;

use App\Models\Faculty;
use App\Models\Period;
use App\Models\StudyProgram;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Register extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getUsernameFormComponent(),
                Select::make('faculty_id')
                    ->label('Fakultas')
                    ->options(fn () => Faculty::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),
                Select::make('study_program_id')
                    ->label('Program Studi')
                    ->options(fn (callable $get): Collection => StudyProgram::query()
                        ->when($get('faculty_id'), fn ($query) => $query->where('faculty_id', $get('faculty_id')))
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('period_id')
                    ->label('Periode')
                    ->options(fn () => Period::pluck('name', 'id'))
                    ->default(fn (): ?int => Period::where('is_active', true)->first()?->id)
                    ->required(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Username')
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel());
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $faculty = Faculty::find($data['faculty_id']);
        $studyProgram = StudyProgram::find($data['study_program_id']);

        $data['faculty'] = $faculty?->name;
        $data['study_program'] = $studyProgram?->name;
        $data['is_active'] = true;

        return $data;
    }

    protected function handleRegistration(array $data): Model
    {
        $user = parent::handleRegistration($data);
        $user->assignRole('prodi');

        return $user;
    }
}
