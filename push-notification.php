<?php
require 'vendor/autoload.php';
require 'db.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

$auth = [
    'VAPID' => [
        'subject' => 'mailto:frontendplux@gmail.com',
        'publicKey' => 'BBYXvq1PxO2cbiObWyYyqQWVMBSM_mPQhuTQ_4b_b9h2nTezZ9NOcZglfcr4KXBFSyZQixoxbppWVDvFhIxD4C0',
        'privateKey' => 'kIipYEdGgUxoWg9LUL5d7rWtVg8K9ej79qppUfHsdrk',
    ],
];

$webPush = new WebPush($auth);

// Get user subscriptions
$result = $conn->query("SELECT * FROM push_subscriptions WHERE user_id = $receiver_id");

while ($row = $result->fetch_assoc()) {

    $subscription = Subscription::create([
        'endpoint' => $row['endpoint'],
        'publicKey' => $row['p256dh'],
        'authToken' => $row['auth'],
    ]);

    $webPush->sendOneNotification(
        $subscription,
        json_encode([
            'title' => 'New Friend Request',
            'body'  => 'You received a friend request'
        ])
    );
}