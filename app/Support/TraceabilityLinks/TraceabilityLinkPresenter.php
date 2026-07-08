<?php

namespace App\Support\TraceabilityLinks;

use App\Models\Indicator;
use App\Models\OrganizationUnit;
use App\Models\Realization;
use App\Models\SelfAssessment;
use App\Models\Standard;
use App\Models\StandardSource;
use App\Models\Target;
use App\Models\TraceabilityLinks;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class TraceabilityLinkPresenter
{
    /** @var array<string, string> */
    private const RELATION_LABELS = [
        'defines' => 'Mendefinisikan',
        'measured_by' => 'Diukur melalui',
        'supported_by' => 'Didukung oleh',
        'evaluated_in' => 'Dievaluasi pada',
        'reviewed_in' => 'Direview pada',
        'resolved_by' => 'Diselesaikan melalui',
        'caused_by' => 'Disebabkan oleh',
        'improved_by' => 'Ditingkatkan melalui',
        'mapped_to' => 'Dipetakan ke',
        'related_to' => 'Berkaitan dengan',
    ];

    /** @var array<string, string> */
    private const ENTITY_LABELS = [
        'standard' => 'Standar',
        'indicator' => 'Indikator',
        'target' => 'Target',
        'realization' => 'Realisasi',
        'evidence' => 'Bukti',
        'self_assessment' => 'Penilaian Mandiri',
        'finding' => 'Temuan',
        'corrective_action' => 'Tindakan Perbaikan',
        'risk' => 'Risiko',
        'decision' => 'Keputusan',
        'organization_unit' => 'Unit Organisasi',
        'standard_source' => 'Sumber Standar',
        'user' => 'Pengguna',
    ];

    /** @var array<string, string> */
    private const ENTITY_ICONS = [
        'standard' => 'tabler-book-2',
        'indicator' => 'tabler-chart-bar',
        'target' => 'tabler-flag',
        'realization' => 'tabler-presentation-analytics',
        'evidence' => 'tabler-paperclip',
        'self_assessment' => 'tabler-clipboard-check',
        'finding' => 'tabler-alert-triangle',
        'corrective_action' => 'tabler-tools',
        'risk' => 'tabler-shield-exclamation',
        'decision' => 'tabler-scale',
        'organization_unit' => 'tabler-building',
        'standard_source' => 'tabler-books',
        'user' => 'tabler-user',
    ];

    /** @var array<string, string> */
    private const ENTITY_COLORS = [
        'standard' => 'info',
        'indicator' => 'primary',
        'target' => 'warning',
        'realization' => 'success',
        'evidence' => 'gray',
        'self_assessment' => 'purple',
        'finding' => 'danger',
        'corrective_action' => 'warning',
        'risk' => 'danger',
        'decision' => 'info',
        'organization_unit' => 'gray',
        'standard_source' => 'info',
        'user' => 'gray',
    ];

    /** @var array<string, string> */
    private const METADATA_LABELS = [
        'name' => 'Nama',
        'code' => 'Kode',
        'target_value' => 'Nilai Target',
        'actual_value' => 'Nilai Aktual',
        'score' => 'Skor',
        'status' => 'Status',
        'is_primary' => 'Data Utama',
        'filename' => 'Berkas',
        'file_name' => 'Berkas',
    ];

    public static function targetDisplayName(TraceabilityLinks $link): string
    {
        return self::entityDisplayName($link->target_type, $link->target, $link->metadata);
    }

    public static function sourceDisplayName(TraceabilityLinks $link): string
    {
        return self::entityDisplayName($link->source_type, $link->source, $link->metadata);
    }

    public static function relationLabel(string $relationType): string
    {
        return self::RELATION_LABELS[$relationType] ?? str($relationType)->headline()->toString();
    }

    public static function entityTypeLabel(string $type): string
    {
        return self::ENTITY_LABELS[$type] ?? str($type)->headline()->toString();
    }

    public static function iconFor(string $type): string
    {
        return self::ENTITY_ICONS[$type] ?? 'tabler-link';
    }

    public static function colorFor(string $type): string
    {
        return self::ENTITY_COLORS[$type] ?? 'gray';
    }

    public static function timelineAt(TraceabilityLinks $link): CarbonInterface
    {
        return $link->performed_at ?? $link->created_at;
    }

    /**
     * @return array<int, string>
     */
    public static function journeyLines(TraceabilityLinks $link): array
    {
        return [
            self::sourceDisplayName($link),
            '↓',
            self::relationLabel($link->relation_type),
            '↓',
            self::targetDisplayName($link),
        ];
    }

    public static function formatMetadata(TraceabilityLinks $link): ?string
    {
        $metadata = $link->metadata;

        if (! is_array($metadata) || $metadata === []) {
            return null;
        }

        $lines = [];

        foreach (self::METADATA_LABELS as $key => $label) {
            if (! array_key_exists($key, $metadata) || blank($metadata[$key])) {
                continue;
            }

            $value = $metadata[$key];

            if (is_bool($value)) {
                $value = $value ? 'Ya' : 'Tidak';
            }

            $lines[] = "{$label}: {$value}";
        }

        if ($lines === []) {
            return null;
        }

        return implode(' · ', $lines);
    }

    /**
     * @param  array<string, mixed>|null  $metadata
     */
    public static function entityDisplayName(string $type, ?Model $entity, ?array $metadata = null): string
    {
        if ($entity !== null) {
            $resolved = self::resolveModelDisplayName($type, $entity);

            if (filled($resolved)) {
                return $resolved;
            }
        }

        return self::resolveMetadataDisplayName($type, $metadata);
    }

    private static function resolveModelDisplayName(string $type, Model $entity): ?string
    {
        return match ($type) {
            'standard' => $entity instanceof Standard ? $entity->name : null,
            'indicator' => $entity instanceof Indicator ? $entity->name : null,
            'target' => $entity instanceof Target
                ? 'Target '.self::formatPercentage($entity->target_value)
                : null,
            'realization' => $entity instanceof Realization
                ? 'Realisasi '.self::formatPercentage($entity->actual_value)
                : null,
            'self_assessment' => $entity instanceof SelfAssessment
                ? self::formatSelfAssessmentName($entity)
                : null,
            'organization_unit' => $entity instanceof OrganizationUnit ? $entity->name : null,
            'standard_source' => $entity instanceof StandardSource ? $entity->name : null,
            'user' => $entity instanceof User ? $entity->name : null,
            'evidence' => self::resolveEvidenceName($entity),
            default => self::resolveGenericName($entity),
        };
    }

    /**
     * @param  array<string, mixed>|null  $metadata
     */
    private static function resolveMetadataDisplayName(string $type, ?array $metadata): string
    {
        if (is_array($metadata)) {
            if (filled($metadata['name'] ?? null)) {
                return (string) $metadata['name'];
            }

            if (filled($metadata['filename'] ?? null)) {
                return (string) $metadata['filename'];
            }

            if (filled($metadata['file_name'] ?? null)) {
                return (string) $metadata['file_name'];
            }

            if ($type === 'target' && filled($metadata['target_value'] ?? null)) {
                return 'Target '.self::formatPercentage($metadata['target_value']);
            }

            if ($type === 'realization' && filled($metadata['actual_value'] ?? null)) {
                return 'Realisasi '.self::formatPercentage($metadata['actual_value']);
            }

            if (filled($metadata['code'] ?? null)) {
                return (string) $metadata['code'];
            }
        }

        return self::entityTypeLabel($type).' (tidak tersedia)';
    }

    private static function formatSelfAssessmentName(SelfAssessment $selfAssessment): string
    {
        $selfAssessment->loadMissing('organizationUnit');

        $unitName = $selfAssessment->organizationUnit?->name;

        if (filled($unitName)) {
            return "Penilaian Mandiri {$unitName}";
        }

        return 'Penilaian Mandiri';
    }

    private static function resolveEvidenceName(Model $entity): ?string
    {
        foreach (['filename', 'file_name', 'name', 'title'] as $attribute) {
            if (filled($entity->getAttribute($attribute))) {
                return (string) $entity->getAttribute($attribute);
            }
        }

        return null;
    }

    private static function resolveGenericName(Model $entity): ?string
    {
        foreach (['name', 'title', 'label'] as $attribute) {
            if (filled($entity->getAttribute($attribute))) {
                return (string) $entity->getAttribute($attribute);
            }
        }

        return null;
    }

    private static function formatPercentage(mixed $value): string
    {
        if (! is_numeric($value)) {
            return (string) $value;
        }

        $formatted = rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');

        return "{$formatted}%";
    }
}
