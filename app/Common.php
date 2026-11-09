<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('uuid_v4')) {
    function uuid_v4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

if (!function_exists('to_bool')) {
    /**
     * Parse PostgreSQL boolean ('t', 'f', 1, 0, true, false) to true PHP boolean
     */
    function to_bool($val): bool
    {
        if (is_bool($val)) {
            return $val;
        }
        if (is_numeric($val)) {
            return (int)$val === 1;
        }
        if (is_string($val)) {
            $v = strtolower(trim($val));
            return in_array($v, ['t', 'true', '1', 'y', 'yes'], true);
        }
        return false;
    }
}

