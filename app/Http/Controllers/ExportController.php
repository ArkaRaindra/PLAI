<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function downloadUsers(): BinaryFileResponse
    {
        return ExcelFacade::download(
            new UsersExport,
            'users.xlsx',
            Excel::XLSX,
        );
    }
}
