<?php

namespace App\Support\EvidenceLink;

use App\Models\Indicator;
use App\Models\Realization;
use App\Models\Standard;

/**
 * Registry of "linkable" reference types for evidence_links.reference_type.
 *
 * Only types with a registered `model` are actually selectable/searchable in
 * the UI today. Types without a model (finding, risk) are placeholders for
 * modules that are Out of Scope for this ticket (AMI, Risk Management) —
 * once those modules exist, they can call self::register() to light up
 * without any changes to the Evidence Link feature itself.
 *
 * `manual` distinguishes types the user can pick from the "Kelola Link"
 * form (indicator, standard, and the not-yet-available placeholders) from
 * types that are only ever created programmatically — e.g. `realization`,
 * which is attached automatically when evidence is uploaded from a
 * Realization record (see EvidenceService::record()). Those still need a
 * label/title so they render correctly wherever an EvidenceLinks row is
 * displayed, but must never appear in the manual "Jenis Referensi" select,
 * since their titleAttribute is a computed accessor, not a real, queryable
 * column (the manual form's option list/search run raw `orderBy`/`where
 * ... like` queries against titleAttribute).
 */
class LinkableTypeRegistry
{
    /**
     * @var array<string, array{label: string, model: class-string|null, titleAttribute: string, manual: bool}>
     */
    protected static array $types = [
        'indicator' => ['label' => 'Indikator', 'model' => Indicator::class, 'titleAttribute' => 'name', 'manual' => true],
        'standard' => ['label' => 'Dokumen (Standar)', 'model' => Standard::class, 'titleAttribute' => 'name', 'manual' => true],
        'finding' => ['label' => 'Temuan Audit (AMI)', 'model' => null, 'titleAttribute' => 'name', 'manual' => true],
        'risk' => ['label' => 'Risiko', 'model' => null, 'titleAttribute' => 'name', 'manual' => true],
        'realization' => ['label' => 'Realisasi Indikator', 'model' => Realization::class, 'titleAttribute' => 'display_title', 'manual' => false],
    ];

    public static function register(string $key, string $label, ?string $model, string $titleAttribute = 'name', bool $manual = true): void
    {
        self::$types[$key] = [
            'label' => $label,
            'model' => $model,
            'titleAttribute' => $titleAttribute,
            'manual' => $manual,
        ];
    }

    /**
     * @return array<string, array{label: string, model: class-string|null, titleAttribute: string, manual: bool}>
     */
    public static function all(): array
    {
        return self::$types;
    }

    /**
     * All type options (including not-yet-available ones), for display.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::$types as $key => $type) {
            $options[$key] = self::isAvailable($key) ? $type['label'] : $type['label'].' (segera hadir)';
        }

        return $options;
    }

    /**
     * Only type options that are currently linkable (have a registered model).
     *
     * @return array<string, string>
     */
    public static function availableOptions(): array
    {
        $options = [];

        foreach (self::$types as $key => $type) {
            if (self::isAvailable($key)) {
                $options[$key] = $type['label'];
            }
        }

        return $options;
    }

    /**
     * Options meant to be picked manually from the "Kelola Link" form —
     * excludes types (like `realization`) that are only ever attached
     * programmatically. Includes not-yet-available placeholders so the
     * option is visible (disabled) rather than silently missing.
     *
     * @return array<string, string>
     */
    public static function selectableOptions(): array
    {
        $options = [];

        foreach (self::$types as $key => $type) {
            if ($type['manual'] ?? true) {
                $options[$key] = self::isAvailable($key) ? $type['label'] : $type['label'];
            }
        }

        return $options;
    }

    public static function modelFor(string $key): ?string
    {
        return self::$types[$key]['model'] ?? null;
    }

    public static function titleAttributeFor(string $key): string
    {
        return self::$types[$key]['titleAttribute'] ?? 'id';
    }

    public static function labelFor(string $key): string
    {
        return self::$types[$key]['label'] ?? $key;
    }

    public static function isAvailable(string $key): bool
    {
        return (self::$types[$key]['model'] ?? null) !== null;
    }

    public static function isManual(string $key): bool
    {
        return self::$types[$key]['manual'] ?? true;
    }
}
