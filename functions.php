<?php

function factorial($n) {
    return array_product(range(1, max($n, 1)));
}

function roundInterval($num, $interval) {
	return round($num / $interval) * $interval;
}
