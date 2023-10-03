<?php

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$thresholds = [
    'high_priority_notifications' => ['start' => 10, 'stop' => 5],
    'medium_priority_notifications' => ['start' => 20, 'stop' => 10],
    'low_priority_notifications' => ['start' => 30, 'stop' => 15]
];

while(true) {
    foreach($thresholds as $queueName => $threshold) {
        checkQueue($redis, $queueName, $threshold['start'], $threshold['stop']);
    }
    sleep(5);
}

function checkQueue($redis, $queueName, $startThreshold, $stopThreshold) {
    $queueSize = $redis->lLen($queueName);

    if ($queueSize > $startThreshold) {
        echo "$queueName is too long ($queueSize items). Starting a processor daemon.\n";
        // Start a new processor
        exec("php processor.php $queueName > /dev/null &");
    } elseif ($queueSize < $stopThreshold) {
        $pidFile = "processorDaemon_$queueName.pid";
        if (file_exists($pidFile)) {
            $pid = file_get_contents($pidFile);
            // Stop the processor
            exec("kill -15 $pid");
            unlink($pidFile); // Delete the PID file
            echo "$queueName has only $queueSize items. Stopping processor daemon with PID $pid.\n";
        }
    }
}

