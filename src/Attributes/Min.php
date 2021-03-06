<?php
namespace FormManager\Attributes;

use FormManager\InputInterface;

class Min
{
    public static $error_message = 'The min value allowed is %s';

    /**
     * Callback used on add this attribute to an input
     *
     * @param InputInterface $input The input in which the attribute will be added
     * @param mixed          $value The value of this attribute
     *
     * @return mixed $value The value sanitized
     */
    public static function onAdd($input, $value)
    {
        switch ($input->attr('type')) {
            case 'datetime':
            case 'datetime-local':
            case 'date':
            case 'time':
            case 'month':
            case 'week':
                return self::checkDatetimeAttribute($input, $value);

            default:
                return self::checkAttribute($input, $value);
        }
    }

    /**
     * Callback used on add this attribute to an input
     *
     * @param InputInterface $input The input in which the attribute will be added
     * @param mixed          $value The value of this attribute
     *
     * @return mixed The value sanitized
     */
    protected static function checkAttribute($input, $value)
    {
        if (!is_float($value) && !is_int($value)) {
            throw new \InvalidArgumentException('The min value must be a float number');
        }

        $input->addValidator('min', array(__CLASS__, 'validate'));

        return $value;
    }

    /**
     * Callback used on add this attribute to a datetime input
     *
     * @param InputInterface $input The input in which the attribute will be added
     * @param mixed          $value The value of this attribute
     *
     * @return mixed The value sanitized
     */
    protected static function checkDatetimeAttribute($input, $value)
    {
        if (!date_create($value)) {
            throw new \InvalidArgumentException('The min value must be a valid datetime');
        }

        $input->addValidator('min', array(__CLASS__, 'validateDatetime'));

        return $value;
    }

    /**
     * Callback used on remove this attribute from an input
     *
     * @param InputInterface $input The input from the attribute will be removed
     */
    public static function onRemove($input)
    {
        $input->removeValidator('min');
    }

    /**
     * Validates the input value according to this attribute
     *
     * @param InputInterface $input The input to validate
     *
     * @return string|true True if its valid, string with the error if not
     */
    public static function validate($input)
    {
        $value = $input->val();
        $attr = $input->attr('min');

        return (empty($attr) || ($value >= $attr)) ? true : sprintf(static::$error_message, $attr);
    }

    /**
     * Validates the datetime input value according to this attribute
     *
     * @param InputInterface $input The input to validate
     *
     * @return string|true True if its valid, string with the error if not
     */
    public static function validateDatetime($input)
    {
        $value = $input->val();
        $attr = $input->attr('min');

        return (empty($attr) || (strtotime($value) >= strtotime($attr))) ? true : sprintf(static::$error_message, $attr);
    }
}
