<?php

namespace App\Support;

use App\Models\QualityPeriod;
use App\Models\Standard;
use App\Models\StandardVersion;

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

        return filled(data_get($data, 'standardVersion.version'));
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
     * @param  array<string, mixed>  $versionData
     */
    public static function sync(Standard $standard, array $versionData): void
    {
        if (! self::shouldPersist($versionData)) {
            $standard->standardVersion?->delete();

            return;
        }

        $attributes = self::buildVersionAttributes($standard, $versionData);

        if ($standard->standardVersion) {
            $standard->standardVersion->update($attributes);
        } else {
            StandardVersion::query()->create($attributes);
        }
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
            'version' => $standardVersion['version'],
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
