<?php

function my_asin($x)
{
    $x = abs($x);
    // For X>=PI/4 is this optimalization
    /*if($x >= pi()/4)
    {
        return 0.5*pi() - my_asin(sqrt(1-$x*$x));
    }*/

    $result = $x;
    $i = 0;

    // Cache coef and root of X, for better performace
    $coef = 1;
    $pow_cache = $x;
    $cache = 0;
    $pow_x = $x*$x;

    while(abs($cache-$result) > 1e-15*$result)
    { // Calculate to fit accuracy

        // Taylor theorem with performance tweaks (caching repeating values)
        $cache = $result;
        $i = $i +1;
        $coef = $coef*$i;
        $i = $i + 1;
        $coef = $coef/$i;
        $pow_cache = $pow_cache * $pow_x;
        $result = $result + $coef*$pow_cache/($i+1);


    }

    return $result;
}
