<?php

namespace App\Support;

use App\Models\QualityPeriod;
use App\Models\Standard;
use App\Models\StandardVersion;
use App\Services\Versioning\VersionGeneratorService;

class StandardVersionPersister
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function shouldPersist(array $data): bool
    {
        if (! ($data['include_standard_version'] ?? false)) {
            return false;
        }

        if (($data['quality_period_mode'] ?? 'existing') === 'new') {
            return filled(data_get($data, 'qualityPeriod.code'));
        }

        return filled($data['quality_period_id'] ?? null);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function stripNestedFormData(array &$data): array
    {
        $versionData = [
            'include_standard_version' => $data['include_standard_version'] ?? false,
            'quality_period_mode' => $data['quality_period_mode'] ?? 'existing',
            'quality_period_id' => $data['quality_period_id'] ?? null,
            'qualityPeriod' => $data['qualityPeriod'] ?? [],
            'standardVersion' => $data['standardVersion'] ?? [],
        ];

        unset(
            $data['include_standard_version'],
            $data['quality_period_mode'],
            $data['quality_period_id'],
            $data['qualityPeriod'],
            $data['standardVersion'],
        );

        return $versionData;
    }

    /**
     * @return array<string, mixed>
     */
    public static function fromParent(Standard $parent): array
    {
        $parent->unsetRelation('standardVersion');
        $parent->load('standardVersion');

        if ($parent->standardVersion === null) {
            throw new \InvalidArgumentException('Induk standar belum memiliki periode kualitas dan versi standar.');
        }

        return self::toFormData($parent->standardVersion);
    }

    public static function inheritFromParent(Standard $child, Standard $parent): void
    {
        self::sync($child, self::fromParent($parent));
    }

    /**
     * @param  array<string, mixed>  $versionData
     */
    public static function cascadeToDescendants(Standard $standard, array $versionData): void
    {
        foreach ($standard->descendants()->get() as $descendant) {
            self::persistVersion($descendant, $versionData);
        }
    }

    /**
     * @param  array<string, mixed>  $versionData
     */
    public static function sync(Standard $standard, array $versionData): void
    {
        self::persistVersion($standard, $versionData);

        if ($standard->parent_id === null) {
            self::cascadeToDescendants($standard, $versionData);
        }
    }

    /**
     * @param  array<string, mixed>  $versionData
     */
    protected static function persistVersion(Standard $standard, array $versionData): void
    {
        if (! self::shouldPersist($versionData)) {
            $standard->standardVersions()->delete();

            return;
        }

        $attributes = self::buildVersionAttributes($standard, $versionData);
        $existingVersion = $standard->standardVersion;
        $newQualityPeriodId = (int) $attributes['quality_period_id'];
        $inheritedVersion = data_get($versionData, 'standardVersion.version');

        if ($existingVersion === null) {
            $attributes['version'] = filled($inheritedVersion)
                ? (string) $inheritedVersion
                : app(VersionGeneratorService::class)->next(
                    StandardVersion::class,
                    'standard_id',
                    $standard->id,
                );

            StandardVersion::query()->create($attributes);

            return;
        }

        if ((int) $existingVersion->quality_period_id !== $newQualityPeriodId) {
            $attributes['version'] = app(VersionGeneratorService::class)->next(
                StandardVersion::class,
                'standard_id',
                $standard->id,
            );

            StandardVersion::query()->create($attributes);

            return;
        }

        unset($attributes['version']);
        $existingVersion->update($attributes);
    }

    /**
     * @return array<string, mixed>
     */
    public static function toFormData(?StandardVersion $version): array
    {
        if ($version === null) {
            return [
                'include_standard_version' => false,
                'quality_period_mode' => 'existing',
                'quality_period_id' => null,
                'qualityPeriod' => [],
                'standardVersion' => [],
            ];
        }

        $status = $version->status;
        $statusValue = $status instanceof \BackedEnum ? $status->value : $status;

        return [
            'include_standard_version' => true,
            'quality_period_mode' => 'existing',
            'quality_period_id' => $version->quality_period_id,
            'standardVersion' => [
                'version' => $version->version,
                'start_date' => $version->start_date,
                'end_date' => $version->end_date,
                'status' => $statusValue,
                'is_active' => $version->is_active,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $versionData
     * @return array<string, mixed>
     */
    protected static function buildVersionAttributes(Standard $standard, array $versionData): array
    {
        $standardVersion = $versionData['standardVersion'] ?? [];

        return [
            'standard_id' => $standard->id,
            'quality_period_id' => self::resolveQualityPeriodId($versionData),
            'start_date' => $standardVersion['start_date'] ?? now()->toDateString(),
            'end_date' => $standardVersion['end_date'] ?? null,
            'status' => $standardVersion['status'] ?? 'draft',
            'is_active' => $standardVersion['is_active'] ?? true,
        ];
    }

    /**
     * @param  array<string, mixed>  $versionData
     */
    protected static function resolveQualityPeriodId(array $versionData): int
    {
        if (($versionData['quality_period_mode'] ?? 'existing') === 'new') {
            return QualityPeriod::query()->create($versionData['qualityPeriod'])->id;
        }

        return (int) $versionData['quality_period_id'];
    }
}
