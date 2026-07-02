<?php

namespace App\Filament\SuperAdmin\Resources\Users\Pages;

use App\Filament\SuperAdmin\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Edit User';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url($this->getResource()::getUrl('index'))
                ->button()
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
            ViewAction::make()->icon('heroicon-o-eye')->color('info'),
            Action::make('resetPassword')
                ->label('Reset Password')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->schema([
                    TextInput::make('password')
                        ->label('Password Baru')
                        ->password()
                        ->revealable()
                        ->required()
                        ->minLength(8)
                        ->confirmed(),
                    TextInput::make('password_confirmation')
                        ->label('Konfirmasi Password')
                        ->password()
                        ->revealable()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->record->forceFill([
                        'password' => $data['password'],
                    ])->save();

                    Notification::make()
                        ->title('Password berhasil direset')
                        ->success()
                        ->send();
                }),
            DeleteAction::make()
                ->icon(Heroicon::Trash),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return UserResource::getUrl('index');
    }
}
