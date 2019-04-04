<?php declare(strict_types=1);

namespace HarmonyIO\Orm;

abstract class Enum
{
    /** @var mixed */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $validValues = (new \ReflectionClass(static::class))->getConstants();

        if (!in_array($value, $validValues, true)) {
            throw new \UnexpectedValueException(sprintf('`%s` is not a valid enum value for %s.', $value, static::class));
        }

        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
