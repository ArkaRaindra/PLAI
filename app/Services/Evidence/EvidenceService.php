<?php

namespace App\Services\Evidence;

use App\Models\EvidenceLinks;
use App\Models\Evidences;
use App\Models\EvidenceVersions;
use App\Services\Versioning\VersionGeneratorService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvidenceService
{
    public function __construct(protected VersionGeneratorService $versionGenerator) {}

    public function record(
        Model $reference,
        int $organizationUnitId,
        string $title,
        ?string $description,
        string $type,
        ?string $filePath = null,
        ?string $urlPath = null,
        ?int $uploadedBy = null,
    ): Evidences {
        return DB::transaction(function () use (
            $reference,
            $organizationUnitId,
            $title,
            $description,
            $type,
            $filePath,
            $urlPath,
            $uploadedBy,
        ): Evidences {
            $existingLink = EvidenceLinks::query()
                ->where('reference_type', $reference->getMorphClass())
                ->where('reference_id', $reference->getKey())
                ->first();

            if ($existingLink !== null) {
                $evidence = $existingLink->evidence;

                $evidence->update([
                    'organization_unit_id' => $organizationUnitId,
                    'title' => $title,
                    'description' => $description,
                ]);

                if ($type === 'file' && filled($filePath)) {
                    $version = $this->versionGenerator->next(
                        EvidenceVersions::class,
                        'evidence_id',
                        $evidence->id,
                    );

                    $evidence->evidenceVersions()->create([
                        'version' => $version,
                        'type' => $type,
                        'file_path' => $filePath,
                        'url_path' => $urlPath,
                        'uploaded_by' => $uploadedBy ?? Auth::id(),
                    ]);

                    $evidence->update(['current_version' => $version]);
                } else {
                    $this->updateCurrentVersionRow($evidence, $type, $filePath, $urlPath);
                }

                return $evidence->refresh();
            }

            $evidence = Evidences::query()->create([
                'organization_unit_id' => $organizationUnitId,
                'title' => $title,
                'description' => $description,
            ]);

            $version = $this->versionGenerator->next(
                EvidenceVersions::class,
                'evidence_id',
                $evidence->id,
            );

            EvidenceVersions::query()->create([
                'evidence_id' => $evidence->id,
                'version' => $version,
                'type' => $type,
                'file_path' => $filePath,
                'url_path' => $urlPath,
                'uploaded_by' => $uploadedBy ?? Auth::id(),
            ]);

            $evidence->update(['current_version' => $version]);

            EvidenceLinks::query()->updateOrCreate(
                [
                    'reference_type' => $reference->getMorphClass(),
                    'reference_id' => $reference->getKey(),
                ],
                [
                    'evidence_id' => $evidence->id,
                ],
            );

            return $evidence->refresh();
        });
    }

    protected function updateCurrentVersionRow(
        Evidences $evidence,
        string $type,
        ?string $filePath,
        ?string $urlPath,
    ): void {
        $currentVersion = $evidence->evidenceVersions()
            ->where('version', $evidence->current_version)
            ->first()
            ?? $evidence->evidenceVersions()->latest('id')->first();

        if ($currentVersion === null) {
            return;
        }

        $currentVersion->update([
            'type' => $type,
            'file_path' => $filePath,
            'url_path' => $urlPath,
        ]);
    }
}
