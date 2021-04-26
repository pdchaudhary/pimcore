<?php
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @category   Pimcore
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Model\DataObject\ClassDefinition\Helper;

use Pimcore\Logger;

/**
 * @internal
 */
abstract class ClassResolver
{
    private static $cache;

    protected static function resolve($class, callable $validationCallback = null)
    {
        if ($class) {
            if (isset(self::$cache[$class])) {
                return self::$cache[$class];
            }
            try {
                if (strpos($class, '@') === 0) {
                    $serviceName = substr($class, 1);
                    $service = \Pimcore::getKernel()->getContainer()->get($serviceName);
                } else {
                    $service = new $class;
                }

                self::$cache[$class] = self::returnValidServiceOrNull($service, $validationCallback);

                return self::$cache[$class];
            } catch (\Throwable $e) {
                Logger::error($e);
            }
        }

        return null;
    }

    private static function returnValidServiceOrNull($service, callable $validationCallback = null)
    {
        if ($validationCallback && !$validationCallback($service)) {
            return null;
        }

        return $service;
    }
}
