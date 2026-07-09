<?php

namespace App\Support\Filament;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use LaraZeus\Tabler\Tabler;
use Leek\FilamentRightClick\Menu\ContextMenuItem;

final class TableContextMenu
{
    public static function view(?Action $action = null): ContextMenuItem
    {
        return self::wrap(self::asNavigation($action ?? ViewAction::make()), Tabler::Eye, 'info');
    }

    public static function edit(?Action $action = null): ContextMenuItem
    {
        return self::wrap(self::asNavigation($action ?? EditAction::make()), Tabler::Pencil, 'warning');
    }

    public static function delete(?Action $action = null): ContextMenuItem
    {
        return self::wrap($action ?? DeleteAction::make(), Tabler::Trash, 'danger');
    }

    public static function submit(Action $action): ContextMenuItem
    {
        return self::wrap($action, Tabler::Send, 'info');
    }

    public static function approve(Action $action): ContextMenuItem
    {
        return self::wrap($action, Tabler::CircleCheck, 'success');
    }

    public static function reject(Action $action): ContextMenuItem
    {
        return self::wrap($action, Tabler::X, 'danger');
    }

    public static function urlAction(Action $action, Tabler $icon, string $color): ContextMenuItem
    {
        return self::wrap(self::asNavigation($action), $icon, $color);
    }

    public static function modal(Action $action, Tabler $icon, string $color): ContextMenuItem
    {
        return self::wrap($action, $icon, $color);
    }

    /**
     * ponytail: context menu mounts actions via Livewire; URL actions must redirect on mount, not open modal.
     */
    private static function asNavigation(Action $action): Action
    {
        $openInNewTab = $action->shouldOpenUrlInNewTab();

        return $action
            ->modal(false)
            ->successNotification(null)
            ->action(function (Action $action) use ($openInNewTab): void {
                $url = $action->getUrl();

                if (blank($url)) {
                    return;
                }

                $livewire = $action->getLivewire();

                if ($openInNewTab) {
                    $livewire->js('window.open('.json_encode($url).', "_blank")');

                    return;
                }

                $livewire->redirect($url);
            });
    }

    private static function wrap(Action $action, Tabler $icon, string $color): ContextMenuItem
    {
        return ContextMenuItem::for($action)
            ->icon($icon)
            ->color($color);
    }
}
