<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public const GUARD = 'web';

    /**
     * @var array<string, list<string>>
     */
    public const MODULES = [
        'master-data' => ['view', 'create', 'update', 'delete'],
        'ppepp' => ['view', 'create', 'update', 'delete', 'approve'],
        'evidence' => ['view', 'upload', 'review', 'approve', 'reject'],
        'audit' => ['view', 'create', 'update', 'verify', 'close'],
        'capa' => ['view', 'create', 'update', 'verify', 'close'],
        'rtm' => ['view', 'create', 'update', 'approve'],
        'risk' => ['view', 'create', 'update', 'approve'],
        'accreditation' => ['view', 'create', 'update', 'export'],
    ];

    /**
     * @return list<string>
     */
    public static function allPermissionNames(): array
    {
        $permissions = [];

        foreach (self::MODULES as $module => $actions) {
            foreach ($actions as $action) {
                $permissions[] = self::permissionName($module, $action);
            }
        }

        return $permissions;
    }

    public static function permissionName(string $module, string $action): string
    {
        return "{$module}.{$action}";
    }

    /**
     * @param  list<string>  $patterns
     * @return list<string>
     */
    public static function resolvePermissionNames(array $patterns): array
    {
        $resolved = [];

        foreach ($patterns as $pattern) {
            if ($pattern === '*') {
                return self::allPermissionNames();
            }

            if (str_ends_with($pattern, '.*')) {
                $module = substr($pattern, 0, -2);

                foreach (self::MODULES[$module] ?? [] as $action) {
                    $resolved[] = self::permissionName($module, $action);
                }

                continue;
            }

            $resolved[] = $pattern;
        }

        return array_values(array_unique($resolved));
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::allPermissionNames() as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => self::GUARD,
            ]);
        }
    }
}
