<?php

function stat_mean ($data) {
    return (array_sum($data) / count($data));
}

function stat_median ($data) {
    sort ($data);
    $elements = count ($data);
    if (($elements % 2) == 0) {
        $i = $elements / 2;
        return (($data[$i - 1] + $data[$i]) / 2);
    } else {
        $i = ($elements - 1) / 2;
        return $data[$i];
    }
}

function stat_range ($data) {
    return (max($data) - min($data));
}

function stat_var ($data) {
// calculates sample variance
    $n = count ($data);
    $mean = stat_mean ($data);
    $sum = 0;
    foreach ($data as $element) {
        $sum += pow (($element - $mean), 2);
    }
    return ($sum / ($n - 1));
}

function stat_varp ($data) {
// calculates population variance
    $n = count ($data);
    $mean = stat_mean ($data);
    $sum = 0;
    foreach ($data as $element) {
        $sum += pow (($element - $mean), 2);
    }
    return ($sum / $n);
}

function stat_stdev ($data) {
// calculates sample standard deviation
    return sqrt (stat_var($data));
}

function stat_stdevp ($data) {
// calculates population standard deviation
    return sqrt (stat_varp($data));
}

function stat_simple_regression ($x, $y) {
// runs a simple linear regression on $x and $y
// returns an associative array containing the following fields:
// a - intercept
// b - slope
// s - standard error of estimate
// r - correlation coefficient
// r2 - coefficient of determination (r-squared)
// cov - covariation
// t - t-statistic
    $output = array();
    $output['a'] = 0;
    $n = min (count($x), count($y));
    $mean_x = stat_mean ($x);
    $mean_y = stat_mean ($y);
    $SS_x = 0;
    foreach ($x as $element) {
        $SS_x += pow (($element - $mean_x), 2);
    }
    $SS_y = 0;
    foreach ($y as $element) {
        $SS_y += pow (($element - $mean_y), 2);
    }
    $SS_xy = 0;
    for ($i = 0; $i < $n; $i++) {
        $SS_xy += ($x[$i] - $mean_x) * ($y[$i] - $mean_y);
    }
    $output['b'] = $SS_xy / $SS_x;
    $output['a'] = $mean_y - $output['b'] * $mean_x;
    $output['s'] = sqrt (($SS_y - $output['b'] * $SS_xy)/ ($n - 2));
    $output['r'] = $SS_xy / sqrt ($SS_x * $SS_y);
    $output['r2'] = pow ($output['r'], 2);
    $output['cov'] = $SS_xy / ($n - 1);
    $output['t'] = $output['r'] / sqrt ((1 - $output['r2']) / ($n - 2));
    
    return $output;
}

function stat_percentile($data, $percentile) {
    if ( 0 < $percentile && $percentile < 1 ) {
        $p = $percentile;
    } else if ( 1 < $percentile && $percentile <= 100 ) {
        $p = $percentile * .01;
    } else {
        return "";
    }
    $count = count($data);
    $allindex = ($count-1)*$p;
    $intvalindex = intval($allindex);
    $floatval = $allindex - $intvalindex;
    sort($data);
    if (!is_float($floatval)){
        $result = $data[$intvalindex];
    } else {
        if ($count > $intvalindex+1)
            $result = $floatval*($data[$intvalindex+1] - $data[$intvalindex]) + $data[$intvalindex];
        else
            $result = $data[$intvalindex];
    }
    return number_format($result,6);
} 

