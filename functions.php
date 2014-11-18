<?php

/**
 * A way of doing factorial without writing math
 */
function factorial($n) {
    return array_product(range(1, max($n, 1)));
}

/** 
 * Rounds to the nearest multiple of $interval. eg 4,8,12 etc
 */
function roundInterval($num, $interval) {
    return round($num / $interval) * $interval;
}

/**
 * Checks if all items in $checks are trueish. AKA AND.
 */
function all(array $checks) {
    foreach($checks as $item) {
        if(!$item) {
            return false;
        }
    }
    
    return true;
}

/**
 * Checks if any item in $checks is trueish. AKA OR
 */
function any(array $checks) {
    foreach($checks as $item) {
        if($item) {
            return true;
        }
    }
    
    return false;
}
