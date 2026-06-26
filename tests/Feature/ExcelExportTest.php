<?php

namespace Tests\Feature;

use App\Exports\UsersExport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class ExcelExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_be_exported_to_xlsx_file(): void
    {
        Storage::fake('local');

        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'username' => 'johndoe',
        ]);

        ExcelFacade::store(new UsersExport, 'exports/users.xlsx', 'local', Excel::XLSX);

        Storage::disk('local')->assertExists('exports/users.xlsx');

        $path = Storage::disk('local')->path('exports/users.xlsx');
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertSame('ID', $sheet->getCell('A1')->getValue());
        $this->assertSame('Nama', $sheet->getCell('B1')->getValue());
        $this->assertSame('Email', $sheet->getCell('C1')->getValue());
        $this->assertSame('Username', $sheet->getCell('D1')->getValue());

        $this->assertSame($user->id, $sheet->getCell('A2')->getValue());
        $this->assertSame('John Doe', $sheet->getCell('B2')->getValue());
        $this->assertSame('john@example.com', $sheet->getCell('C2')->getValue());
        $this->assertSame('johndoe', $sheet->getCell('D2')->getValue());
    }

    public function test_users_export_can_be_downloaded(): void
    {
        User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'username' => 'janedoe',
        ]);

        $response = $this->get(route('exports.users'));

        $response->assertOk();
        $response->assertHeader(
            'content-type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        );
        $response->assertDownload('users.xlsx');

        $tempPath = $response->baseResponse->getFile()->getPathname();
        $spreadsheet = IOFactory::load($tempPath);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertSame('Jane Doe', $sheet->getCell('B2')->getValue());
        $this->assertSame('jane@example.com', $sheet->getCell('C2')->getValue());
    }

    public function test_users_export_triggers_excel_download_facade(): void
    {
        ExcelFacade::fake();

        User::factory()->count(2)->create();

        $this->get(route('exports.users'));

        ExcelFacade::assertDownloaded('users.xlsx', function (UsersExport $export): bool {
            return $export->collection()->count() === 2;
        });
    }
}
