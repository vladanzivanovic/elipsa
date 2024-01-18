<?php

namespace App\Helper;

use ReflectionException;

/**
 * Class ConstantsHelper
 */
final class ConstantsHelper
{
    /**
     * @param string|int $searchVal
     * @param string $group
     * @param string $className
     *
     * @return string|null
     * @throws ReflectionException
     */
    public static function getConstantName($searchVal, string $group, string $className): ?string
    {
        $reflection = new \ReflectionClass($className);
        $constants = $reflection->getConstants();

        foreach ($constants as $name => $value) {
            if ($value == $searchVal && false !== strpos($name, $group)) {
                return strtolower(str_replace($group.'_', '', $name));
            }
        }

        return null;
    }

    /**
     * @param string $str
     * @param string $group
     * @param string $className
     *
     * @return mixed|null
     * @throws ReflectionException
     */
    public static function getConstantValueByStr(string $str, string $group, string $className)
    {
        $reflection = new \ReflectionClass($className);
        $constants = $reflection->getConstants();

        $constName = $group.'_'.strtoupper($str);

        if (true === array_key_exists($constName, $constants)) {
            return $reflection->getConstant($constName);
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \ReflectionException
     */
    public function getClassConstants(string $className, string $prefix = null): array
    {
        $reflection = new \ReflectionClass($className);

        $constants = $reflection->getConstants();

        if (null === $prefix) {
            return $constants;
        }

        $filteredConstants = [];

        foreach ($constants as $constant => $value) {
            if (false !== strpos($constant, $prefix)) {
                $filteredConstants[$constant] = $value;
            }
        }

        return $filteredConstants;
    }
}
