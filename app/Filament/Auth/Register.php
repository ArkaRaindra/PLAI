<?php

namespace App\Filament\Auth;

use App\Models\Period;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class Register extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getUsernameFormComponent(),
                TextInput::make('faculty')
                    ->label('Fakultas')
                    ->required()
                    ->maxLength(255),
                TextInput::make('study_program')
                    ->label('Program Studi')
                    ->required()
                    ->maxLength(255),
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
