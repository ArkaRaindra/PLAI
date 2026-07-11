<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

/**
 * Generic, reusable approval-workflow engine.
 *
 * A WorkflowInstance tracks the current_status of any "workflowable" entity
 * (identified polymorphically via entity_type/entity_id), while every
 * transition is appended to workflow_histories for a full audit trail.
 *
 * This is intentionally NOT evidence-specific: other modules (CAPA, AMI
 * Findings, RTM, etc.) can plug into the same engine by registering their
 * own transition map via WorkflowInstance::registerTransitions().
 */
class WorkflowInstance extends Model
{
    protected $table = 'workflow_instances';

    public const string DEFAULT_INITIAL_STATUS = 'draft';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'current_status',
    ];

    /**
     * Per-entity-type transition maps. Register additional entries from
     * another module's service provider via registerTransitions() to reuse
     * this engine without modifying this class.
     *
     * @var array<class-string, array<string, list<string>>>
     */
    protected static array $transitionRegistry = [
        'evidence' => [
            'draft' => ['submitted'],
            'submitted' => ['review'],
            'review' => ['approved', 'rejected'],
            'approved' => ['published'],
            'rejected' => ['draft'],
            'published' => [],
        ],
    ];

    /**
     * Register (or override) the transition map for a workflowable entity type.
     *
     * @param  array<string, list<string>>  $transitions
     */
    public static function registerTransitions(string $entityType, array $transitions): void
    {
        self::$transitionRegistry[$entityType] = $transitions;
    }

    /**
     * @return array<string, list<string>>
     */
    public static function transitionsFor(string $entityType): array
    {
        return self::$transitionRegistry[$entityType] ?? [];
    }

    /**
     * Find the workflow instance belonging to a given entity, if any.
     */
    public static function for(Model $entity): ?self
    {
        return self::query()
            ->where('entity_type', $entity->getMorphClass())
            ->where('entity_id', $entity->getKey())
            ->first();
    }

    /**
     * Get or create the workflow instance for a given entity, initializing
     * it (with an initial history entry) the first time it's called.
     */
    public static function initialize(
        Model $entity,
        string $initialStatus = self::DEFAULT_INITIAL_STATUS,
        ?int $actedBy = null,
    ): self {
        $instance = self::for($entity);

        if ($instance !== null) {
            return $instance;
        }

        $instance = self::query()->create([
            'entity_type' => $entity->getMorphClass(),
            'entity_id' => $entity->getKey(),
            'current_status' => $initialStatus,
        ]);

        $instance->histories()->create([
            'status' => $initialStatus,
            'notes' => null,
            'acted_by' => $actedBy ?? Auth::id(),
            'acted_at' => now(),
        ]);

        return $instance;
    }

    public function entity(): MorphTo
    {
        return $this->morphTo('entity', 'entity_type', 'entity_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(WorkflowHistory::class)->orderByDesc('acted_at');
    }

    public function canTransitionTo(string $status): bool
    {
        $transitions = self::transitionsFor($this->entity_type);

        return in_array($status, $transitions[$this->current_status] ?? [], true);
    }

    /**
     * Move the workflow to a new status (if allowed) and append a history entry.
     */
    public function transitionTo(string $status, ?string $notes = null, ?int $actedBy = null): void
    {
        if (! $this->canTransitionTo($status)) {
            throw new \RuntimeException(
                "Transisi workflow dari '{$this->current_status}' ke '{$status}' tidak diizinkan"
            );
        }

        $this->current_status = $status;
        $this->save();

        $this->histories()->create([
            'status' => $status,
            'notes' => $notes,
            'acted_by' => $actedBy ?? Auth::id(),
            'acted_at' => now(),
        ]);
    }

    public function isAt(string $status): bool
    {
        return $this->current_status === $status;
    }
}
