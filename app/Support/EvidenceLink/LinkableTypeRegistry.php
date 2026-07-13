<?php

namespace App\Support\EvidenceLink;

use App\Models\Indicator;
use App\Models\Standard;

/**
 * Registry of "linkable" reference types for evidence_links.reference_type.
 *
 * Only types with a registered `model` are actually selectable/searchable in
 * the UI today. Types without a model (finding, risk) are placeholders for
 * modules that are Out of Scope for this ticket (AMI, Risk Management) —
 * once those modules exist, they can call self::register() to light up
 * without any changes to the Evidence Link feature itself.
 */
class LinkableTypeRegistry
{
    /**
     * @var array<string, array{label: string, model: class-string|null, titleAttribute: string}>
     */
    protected static array $types = [
        'indicator' => ['label' => 'Indikator', 'model' => Indicator::class, 'titleAttribute' => 'name'],
        'standard' => ['label' => 'Dokumen (Standar)', 'model' => Standard::class, 'titleAttribute' => 'name'],
        'finding' => ['label' => 'Temuan Audit (AMI)', 'model' => null, 'titleAttribute' => 'name'],
        'risk' => ['label' => 'Risiko', 'model' => null, 'titleAttribute' => 'name'],
    ];

    public static function register(string $key, string $label, ?string $model, string $titleAttribute = 'name'): void
    {
        self::$types[$key] = ['label' => $label, 'model' => $model, 'titleAttribute' => $titleAttribute];
    }

    /**
     * @return array<string, array{label: string, model: class-string|null, titleAttribute: string}>
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
}
