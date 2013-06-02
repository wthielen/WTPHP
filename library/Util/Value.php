<?php

namespace WT\Util;

/**
 * A static class to restrict/modify values easily
 */
abstract class Value
{
    /**
     * Clamps a value between a minimum and maximum value.
     */
    public static clamp($val, $min, $max) {
        if ($val < $min) $val = $min;
        if ($val > $max) $val = $max;

        return $val;
    }
}
