<?php

declare(strict_types=1);

namespace N98\Magento\Command\System\Check\Settings;

use Mage;
use Mage_Core_Model_Store;
use N98\Magento\Command\System\Check\StoreCheck;
use ReflectionMethod;

/**
 * Class CheckAbstract
 *
 * @package N98\Magento\Command\System\Check\Settings
 *
 * @author Tom Klingenberg (https://github.com/ktomk)
 */
abstract class CheckAbstract implements StoreCheck
{
    final public function __construct()
    {
        $this->initConfigPaths();
    }

    abstract protected function initConfigPaths(): void;
}
