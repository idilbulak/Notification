<?php

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$high_priority_item = $redis->rPop('high_priority');

$medium_priority_item = $redis->rPop('medium_priority');

$low_priority_item = $redis->rPop('low_priority');

?>
