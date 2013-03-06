<?php

require_once(dirname(__FILE__) . "/" . 'simple_statistics.php');


class Stats {
    public function get($duration) {
        $metrics = array(
            'Sample'=> count($duration),
            'First' => $duration[0],
            'Max'   => max($duration),
            'Min'   => min($duration),
            '95-Percentile' => stat_percentile($duration, 95),
            '99-Percentile' => stat_percentile($duration, 99),
            );
        return $metrics;        
    }
}
