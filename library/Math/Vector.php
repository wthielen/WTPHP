<?php

/**
 * A multi-dimensional vector class
 *
 * Implements ArrayAccess so you can get its coordinates easily
 */
class Vector implements ArrayAccess
{
    // Exception messages
    const UNKNOWN_SIZE = "Can not initialize a null vector";
    const INVALID_DATA = "Data is not an array of values";
    const INVALID_MEMBER = "Trying to get invalid member %s from %s";
    const INVALID_OPERATION = "Trying to do an operation on a set of incompatible vectors";
    const INVALID_INDEX = "Index '%s' not valid";
    const OUT_OF_BOUNDS = "Index %d out of bounds";
    const CANNOT_UNSET = "You can not unset a vector's coordinate";

    // Private $_data
    private $_data;

    // Read-only $dimension
    private $dimension;

    /**
     * Constructor
     * Flexible constructor to create a vector based on its numerical arguments
     * When only one argument has been given, that argument represents the dimension
     * and initializes a zero-vector in that dimension.
     */
    public function __construct()
    {
        $data = func_get_args();

        // Check number of arguments
        if (count($data) == 0) throw new Exception(self::UNKNOWN_SIZE);
        if (count($data) == 1) $data = current($data);

        // If data is an integer, it is the dimension
        if (is_int($data)) {
            $this->dimension = intval($data);
            $this->_data = array_fill(0, $this->dimension, 0);
        } else {
            // Must be an array ...
            if (!is_array($data)) throw new Exception(self::INVALID_DATA);
        
            // ... numerically indexed ...
            $data = array_values($data);

            // ... of numbers (and turn them into floats while testing them)
            $pass = array_reduce($data, function($u, &$v) {
                if (is_numeric($v)) {
                    $v = floatval($v);
                    return $u;
                }

                return false;
            }, true);
            if (!$pass) throw new Exception(self::INVALID_DATA);

            $this->_data = $data;
            $this->dimension = count($data);
        }
    }

    /**
     * Magical getter
     *
     * Only useful for getting the dimension
     */
    public function __get($var)
    {
        $whitelist = array(
            'dimension'
        );

        if (!in_array($var, $whitelist)) 
            throw new Exception(sprintf(self::INVALID_MEMBER, $var, __CLASS__));

        return $this->$var;
    }

    /**
     * offsetExists
     *
     * As required by ArrayAccess
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    /**
     * offsetGet
     *
     * As required by ArrayAccess
     */
    public function offsetGet($offset)
    {
        if (!is_int($offset)) throw new Exception(sprintf(self::INVALID_INDEX, $offset));
        if ($offset < 0 || $offset >= $this->dimension) throw new Exception(sprintf(self::OUT_OF_BOUNDS, $offset));

        return $this->_data[$offset];
    }

    /**
     * offsetSet
     *
     * As required by ArrayAccess
     */
    public function offsetSet($offset, $value) 
    {
        if (!is_int($offset)) throw new Exception(sprintf(self::INVALID_INDEX, $offset));
        if ($offset < 0 || $offset >= $this->dimension) throw new Exception(sprintf(self::OUT_OF_BOUNDS, $offset));
        if (!is_numeric($value)) throw new Exception(self::INVALID_DATA);

        $this->_data[$offset] = floatval($value);
    }

    /**
     * offsetUnset
     *
     * As required by ArrayAccess
     * Throws an exception as it does not make sense in this class
     */
    public function offsetUnset($offset)
    {
        throw new Exception(self::CANNOT_UNSET);
    }

    /**
     * length
     *
     * Calculates the vector's length
     */
    public function length()
    {
        return sqrt(array_reduce($this->_data, function($u, $v) {
            return $u + $v * $v;
        }));
    }

    /**
     * Returns the coordinates.
     * Even if it is an ArrayAccess class, the object variable
     * can not be passed as an array to functions :(
     */
    public function get()
    {
        return $this->_data;
    }

    /**
     * Subtract
     *
     * Subtracts the given vector from the current vector and returns the result
     */
    public function subtract(Vector $v) {
        if ($this->dimension != $v->dimension) throw new Exception(self::INVALID_OPERATION);

        $ret = array_map(function($i, $j) {
            return $i - $j;
        }, $this->_data, $v->get());

        return new Vector($ret);
    }

    /**
     * Add
     *
     * Adds the given vector to the current vector and returns the result
     */
    public function add(Vector $v) 
    {
        if ($this->dimension != $v->dimension) throw new Exception(self::INVALID_OPERATION);

        $ret = array_map(function($i, $j) {
            return $i + $j;
        }, $this->_data, $v->get());

        return new Vector($ret);
    }

    /**
     * Distance
     *
     * Calculates the distance between the current vector and the given vector
     * This is just returning the length of the subtraction
     */
    public function distance(Vector $v) 
    {
        return $this->subtract($v)->length();
    }

    /**
     * Minimum
     *
     * Returns the vector with the minimum coordinate values from the current and the
     * given vector
     */
    public function min(Vector $v)
    {
        if ($this->dimension != $v->dimension) throw new Exception(self::INVALID_OPERATION);

        $ret = array_map("min", $this->_data, $v->get());

        return new Vector($ret);
    }

    /**
     * Maximum
     *
     * Returns the vector with the maximum coordinate values from the current and the
     * given vector
     */
    public function max(Vector $v)
    {
        if ($this->dimension != $v->dimension) throw new Exception(self::INVALID_OPERATION);

        $ret = array_map("max", $this->_data, $v->get());

        return new Vector($ret);
    }

    /**
     * Group minimum
     *
     * Returns the vector with the minimum coordinate values from the given array of
     * vectors
     *
     * TODO: Check if the array indeed contains vectors only
     */
    public static function groupMin($vectors)
    {
        $ret = current($vectors);
        foreach($vectors as $v) $ret = $ret->min($v);
        return $ret;
    }

    /**
     * Group maximum
     *
     * Returns the vector with the maximum coordinate values from the given array of
     * vectors
     *
     * TODO: Check if the array indeed contains vectors only
     */
    public static function groupMax($vectors)
    {
        $ret = current($vectors);
        foreach($vectors as $v) $ret = $ret->max($v);
        return $ret;
    }
}
