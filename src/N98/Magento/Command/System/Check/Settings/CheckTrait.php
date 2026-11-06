<?php

declare(strict_types=1);

namespace N98\Magento\Command\System\Check\Settings;

use Mage;
use Mage_Core_Model_Store;
use N98\Magento\Command\System\Check\ResultCollection;
use ReflectionMethod;

/**
 * Class CheckAbstract
 *
 * @package N98\Magento\Command\System\Check\Settings
 *
 * @author Tom Klingenberg (https://github.com/ktomk)
 */

trait CheckTrait
{
    private array $storeConfigPaths = [];

    protected function registerStoreConfigPath(string $name, string $configPath): void
    {
        $this->storeConfigPaths[$name] = $configPath;
    }

    public function check(ResultCollection $resultCollection, Mage_Core_Model_Store $mageCoreModelStore): void
    {
        $result = $resultCollection->createResult();

        $typedParams = ['result' => $result, 'store'  => $mageCoreModelStore];

        $paramValues = $this->getParamValues($mageCoreModelStore, $typedParams);

        $reflectionMethod = new ReflectionMethod($this, 'checkSettings');
        $parameters = $reflectionMethod->getParameters();

        $arguments = [];
        foreach ($parameters as $parameter) {
            $paramName = $parameter->getName();
            $paramClass = $parameter->getClass();

            // create named parameter from type-hint if applicable
            if ($paramClass) {
                foreach ($typedParams as $typedParam) {
                    if ($paramClass->isSubclassOf(get_class($typedParam))) {
                        $paramValues[$paramName] = $typedParam;
                        break;
                    }
                }
            }

            // use named parameter, otherwise null
            $paramValues += [$paramName => null];
            $arguments[] = $paramValues[$paramName];
        }

        call_user_func_array($this->checkSettings(...), $arguments);
    }

    /**
     *
     * @return array
     * @param array<array<string, mixed>, mixed> $typedParams
     */
    private function getParamValues(Mage_Core_Model_Store $mageCoreModelStore, array $typedParams)
    {
        $paramValues = $this->storeConfigPaths;

        foreach ($paramValues as $name => $path) {
            $value = Mage::getStoreConfig($path, $mageCoreModelStore);
            $paramValues[$name] = $value;
        }

        return $typedParams + $paramValues;
    }
}