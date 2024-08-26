<?php
/**
 * This file is part of VinChan.
 * @link     https://www.vinchan.cn
 * @contact  ademo@vip.qq.com
 * @license  https://www.vinchan.cn
 */
declare(strict_types=1);

namespace Vin\ObjectBuilder;

use ReflectionClass;
use ReflectionException;
use Vin\ObjectBuilder\Mime\Form;
use Vin\ObjectBuilder\Mime\Query;

class Builder
{
    /**
     * @throws ReflectionException
     */
    public static function make(object|string $closure): object
    {
        $request = request();

        $reflection = new ReflectionClass($closure);
        $properties = $reflection->getProperties();

        $instance = $reflection->newInstance();

        foreach ($properties as $property) {
            $attributes = $property->getAttributes();
            $function = self::studly('set_' . $property->getName());

            foreach ($attributes as $attribute) {
                $value = match ($attribute->getName()) {
                    Query::class => $request->get($property->getName()),
                    Form::class => $request->post($property->getName()),
                    default => '',
                };
                if ($property->getType()) {
                    $type = $property->getType()->getName();
                    $value = self::convert($value, $type);
                }
                $instance->{$function}($value);
            }
        }

        return $instance;
    }

    private static function studly(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
    }

    private static function convert($value, $type)
    {
        switch ($type) {
            case 'int':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'bool':
                $boolValues = [true, false, 'true', 'false', 0, 1];
                return in_array($value, $boolValues) ? (bool) $value : null;
            case 'string':
                return (string) $value;
            case 'array':
                return is_array($value) ? $value : null;
            case 'object':
                return is_object($value) ? $value : null;
            default:
                return null;
        }
    }
}
