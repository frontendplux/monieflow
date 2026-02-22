<?php
require 'vendor/autoload.php';

use Minishlink\WebPush\VAPID;

$keys = VAPID::createVapidKeys();
print_r($keys);
var_dump(extension_loaded('openssl'));