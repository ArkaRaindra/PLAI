<?php

namespace Tests\Unit\Support\Filament;

use App\Support\Filament\TableContextMenu;
use Filament\Actions\Action;
use LaraZeus\Tabler\Tabler;
use ReflectionProperty;
use Tests\TestCase;

class TableContextMenuTest extends TestCase
{
    public function test_view_menu_item_uses_tabler_eye_and_info_color(): void
    {
        $payload = TableContextMenu::view()->toPayload();

        $this->assertSame('view', $payload['action']);
        $this->assertSame('info', $payload['color']);
        $this->assertStringContainsString('eye', $payload['icon']);
    }

    public function test_modal_item_preserves_action_without_wrapping_navigation(): void
    {
        $action = Action::make('detail')->label('Detail');

        $item = TableContextMenu::modal($action, Tabler::Eye, 'info');

        $this->assertSame($action, $item->getAction());
        $this->assertSame('detail', $item->toPayload()['action']);
    }

    public function test_url_action_disables_modal_for_context_menu_navigation(): void
    {
        $action = Action::make('open')
            ->url(fn (): string => 'https://example.com/test');

        $wrapped = TableContextMenu::urlAction($action, Tabler::ExternalLink, 'info')->getAction();

        $this->assertFalse($wrapped->shouldOpenModal());
    }

    public function test_view_action_disables_modal_for_context_menu_navigation(): void
    {
        $wrapped = TableContextMenu::view()->getAction();

        $this->assertFalse($wrapped->shouldOpenModal());
    }

    public function test_edit_action_disables_success_notification_for_navigation(): void
    {
        $property = new ReflectionProperty(Action::class, 'isSuccessNotificationDisabled');
        $property->setAccessible(true);

        $this->assertTrue($property->getValue(TableContextMenu::edit()->getAction()));
    }
}
