<?php

if ( ! function_exists('take'))
{
    /**
     * Take element out from array or object by key or property name
     *
     * Removes element from array/object and returns it
     * @param string $by
     * @param object|array $from
     * @return mixed
     */
    function take(&$from, string $by)
    {
        if(is_array($from)) {
            $temp = $from[$by] ?? null;
            unset($from[$by]);
            return $temp;
        }

        if(is_object($from)) {
            $temp = $from->{$by} ?? null;
            unset($from->{$by});
            return $temp;
        }

        return null;
    }
}
