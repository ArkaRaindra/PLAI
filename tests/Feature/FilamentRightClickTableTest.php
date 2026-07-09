<?php

namespace Tests\Feature;

use App\Filament\SuperAdmin\Resources\Realizations\Tables\RealizationsTable;
use Filament\Facades\Filament;
use Filament\Tables\Table;
use Leek\FilamentRightClick\FilamentRightClickPlugin;
use Tests\Support\FakeFilamentTable;
use Tests\TestCase;

class FilamentRightClickTableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $panel = Filament::getPanel('super-admin');

        if (! $panel->hasPlugin('filament-right-click')) {
            FilamentRightClickPlugin::make()->register($panel);
        }
    }

    public function test_super_admin_panel_registers_right_click_plugin(): void
    {
        $this->assertTrue(
            Filament::getPanel('super-admin')->hasPlugin('filament-right-click'),
        );
    }

    public function test_realizations_table_registers_context_menu_actions(): void
    {
        $table = RealizationsTable::configure(Table::make(new FakeFilamentTable));
        $attributes = $table->getExtraAttributes();

        $this->assertTrue($table->hasAction('view'));
        $this->assertTrue($table->hasAction('edit'));
        $this->assertTrue($table->hasAction('submit'));
        $this->assertEmpty($table->getRecordActions());
        $this->assertStringContainsString('fi-right-click-table', $attributes['class'] ?? '');
        $this->assertArrayHasKey('data-filament-right-click-record-config', $attributes);

        $payload = json_decode(base64_decode($attributes['data-filament-right-click-record-config']), associative: true);

        $this->assertSame('record', $payload['target']);
        $this->assertContains('view', array_column($payload['items'], 'action'));
        $this->assertContains('submit', array_column($payload['items'], 'action'));
    }
}
