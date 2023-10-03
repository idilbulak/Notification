<?php
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$notification = [
    'type' => 'financial',
    'message' => 'Your account has been credited!'
];

switch ($notification['type']) {
    case 'financial':
        $queue = 'high_priority_notifications';
        break;
    case 'support':
        $queue = 'medium_priority_notifications';
        break;
    default:
        $queue = 'low_priority_notifications';
}

$redis->lPush($queue, json_encode($notification));
?>

<!-- basa ekle sondan cikar fifo first in forst out -->
