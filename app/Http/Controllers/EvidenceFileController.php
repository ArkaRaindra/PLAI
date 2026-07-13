<?php

namespace App\Http\Controllers;

use App\Models\Evidences;
use App\Models\EvidenceVersions;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EvidenceFileController extends Controller
{
    public function download(EvidenceVersions $version): StreamedResponse|Response
    {
        Gate::authorize('view', $version);

        if ($version->type !== 'file' || blank($version->file_path)) {
            abort(404);
        }

        if (! Storage::disk('local')->exists($version->file_path)) {
            abort(404);
        }

        return Storage::disk('local')->download($version->file_path);
    }

    public function preview(EvidenceVersions $version): StreamedResponse|Response
    {
        Gate::authorize('view', $version);

        if ($version->type !== 'file' || blank($version->file_path)) {
            abort(404);
        }

        if (! Storage::disk('local')->exists($version->file_path)) {
            abort(404);
        }

        return Storage::disk('local')->response($version->file_path);
    }

    public function downloadEvidence(Evidences $evidence): StreamedResponse|Response
    {
        if ($evidence->type !== 'file' || blank($evidence->file_path)) {
            abort(404);
        }

        if (! Storage::disk('local')->exists($evidence->file_path)) {
            abort(404);
        }

        return Storage::disk('local')->download($evidence->file_path);
    }
}
