<?php
function hypo($a, $b)
{
    if (abs($a) > abs($b)) {
        $r = $b / $a;
        $r = abs($a) * sqrt(1 + $r * $r);
    } elseif ($b != 0) {
        $r = $a / $b;
        $r = abs($b) * sqrt(1 + $r * $r);
    } else {
        $r = 0.0;
    }
    return $r;
}