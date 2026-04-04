<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Service;

use App\Domain\Authorization\Enum\Action;
use App\Domain\Authorization\Enum\Feature;
use App\Domain\Authorization\ValueObject\PermissionKey;

final readonly class ModuleConfigExpander
{
    /**
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
     * @return list<string>
     */
    public function expandToLeaves(PermissionKey $permissionKey, array $availableModules): array
    {
        $moduleName = $permissionKey->module->value;

        if (! isset($availableModules[$moduleName])) {
            return [];
        }

        $moduleConfig = $availableModules[$moduleName];

        if (! $permissionKey->feature instanceof Feature) {
            return $this->allModuleLeafKeys($moduleName, $moduleConfig);
        }

        $featureName = $permissionKey->feature->value;

        if (! isset($moduleConfig['features'][$featureName])) {
            return [];
        }

        if (! $permissionKey->action instanceof Action) {
            return $this->allFeatureLeafKeys($moduleName, $featureName, $moduleConfig['features'][$featureName]);
        }

        return [$moduleName.'.'.$featureName.'.'.$permissionKey->action->value];
    }

    /**
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
     * @return list<array{string, string, string}>
     */
    public function allLeafKeys(array $availableModules): array
    {
        $keys = [];

        foreach ($availableModules as $moduleName => $moduleConfig) {
            foreach ($moduleConfig['features'] as $featureName => $featureConfig) {
                foreach ($featureConfig['actions'] as $actionName) {
                    $keys[] = [$moduleName, $featureName, $actionName];
                }
            }
        }

        return $keys;
    }

    /**
     * @param  array{features: array<string, array{actions: list<string>}>}  $moduleConfig
     * @return list<string>
     */
    private function allModuleLeafKeys(string $moduleName, array $moduleConfig): array
    {
        $leaves = [];

        foreach ($moduleConfig['features'] as $featureName => $featureConfig) {
            foreach ($featureConfig['actions'] as $actionName) {
                $leaves[] = $moduleName.'.'.$featureName.'.'.$actionName;
            }
        }

        return $leaves;
    }

    /**
     * @param  array{actions: list<string>}  $featureConfig
     * @return list<string>
     */
    private function allFeatureLeafKeys(string $moduleName, string $featureName, array $featureConfig): array
    {
        $leaves = [];

        foreach ($featureConfig['actions'] as $actionName) {
            $leaves[] = $moduleName.'.'.$featureName.'.'.$actionName;
        }

        return $leaves;
    }
}
