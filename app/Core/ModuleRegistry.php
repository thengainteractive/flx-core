<?php

namespace App\Core;

use Nwidart\Modules\Facades\Module;

class ModuleRegistry
{
    /**
     * Get all modules (both enabled and disabled) with metadata.
     */
    public static function all(): array
    {
        $modules = Module::all();
        $list = [];

        foreach ($modules as $module) {
            $name = $module->getName();
            $list[] = [
                'name' => $name,
                'alias' => $module->getLowerName(),
                'description' => $module->get('description') ?? 'No description provided.',
                'version' => $module->get('version') ?? '1.0.0',
                'enabled' => $module->isEnabled(),
                'path' => $module->getPath(),
            ];
        }

        return $list;
    }

    /**
     * Get all active modules.
     */
    public static function enabled(): array
    {
        return Module::allEnabled();
    }

    /**
     * Toggle a module status.
     */
    public static function toggle(string $name): bool
    {
        $module = Module::find($name);
        if (!$module) {
            return false;
        }

        if ($module->isEnabled()) {
            $module->disable();
            return false;
        } else {
            $module->enable();
            return true;
        }
    }

    /**
     * Check if a module is enabled.
     */
    public static function isEnabled(string $name): bool
    {
        $module = Module::find($name);
        return $module ? $module->isEnabled() : false;
    }
}
