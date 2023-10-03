<?php

$notifications = [
    [
        'type' => 'payment',
        'message' => 'Your payment of $100 has been processed.',
        'user_id' => 1,
    ],
    [
        'type' => 'marketing',
        'message' => 'Check out our new products.',
        'user_id' => 2,
    ],
    [
        'type' => 'support',
        'message' => 'Your support ticket has been updated.',
        'user_id' => 1,
    ],
];

$highPriorityQueue = [];
$lowPriorityQueue = [];

foreach ($notifications as $notification) {
    switch ($notification['type']) {
        case 'payment':
        case 'support':
            $highPriorityQueue[] = $notification;
            break;
        case 'marketing':
            $lowPriorityQueue[] = $notification;
            break;
        default:
            break;
    }
}
