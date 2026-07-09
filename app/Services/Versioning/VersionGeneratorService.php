<?php

namespace App\Services\Versioning;

use Illuminate\Database\Eloquent\Model;

class VersionGeneratorService
{
    /**
     * @param  class-string<Model>  $versionModel
     */
    public function next(
        string $versionModel,
        string $foreignKeyColumn,
        int|string $foreignKeyValue,
        string $versionColumn = 'version',
        float $increment = 1.0,
    ): string {
        $latest = $versionModel::query()
            ->where($foreignKeyColumn, $foreignKeyValue)
            ->lockForUpdate()
            ->pluck($versionColumn)
            ->map(fn (mixed $version): float => (float) $version)
            ->max();

        $next = $latest === null ? 1.0 : $latest + $increment;

        return number_format($next, 1, '.', '');
    }
}
