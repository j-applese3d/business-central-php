<?php
/**
 * @package   business-central-sdk
 * @author    Morten Harders 🐢
 * @copyright 2020
 */

namespace BusinessCentral\Validator\Rules;


use BusinessCentral\Schema;
use DateTime;
use Rakit\Validation\Rule;

class TypeOf extends Rule
{
    protected $message = "The :attribute must be of type ':type'";

    public function fillParameters(array $params) : Rule
    {
        $this->params['type'] = array_shift($params);

        return $this;
    }

    public function check($value) : bool
    {
        $this->requireParameters(['type']);
        $type = $this->parameter('type');

        $this->key = "typeof:$type";

        return match ($type) {
            'required' => !is_null($value),
            'guid' => preg_match('/[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}/i', $value) !== false,
            'string' => is_string($value),
            'int' => is_int($value),
            'float', 'double' => is_int($value) || is_float($value),
            'bool', 'boolean' => is_bool($value),
            'date' => (bool)DateTime::createFromFormat('Y-m-d', $value),
            'null' => is_null($value),
            'mixed' => true,
            default => throw new \Exception("Unknown validation type $type in TypeOf rule"),
        };
    }
}