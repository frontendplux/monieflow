<?php
include __DIR__."/function.php";
$new = new codium($conn);

$hooks = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$hooks2 = $_POST['action'] ?? '';
$route = $hooks['action'] ?? $hooks2;

switch ($route) {
   case 'create_feeds':
      $new->createFeed();
      break;
    case 'fetch_all_feeds':
        echo json_encode([
            "feed"   => $new->feeds($hooks['limit'] = 0, 'feed'),
            "reels"   => $new->feeds($hooks['limit'] = 0, 'reel'),
            "market" => $new->feeds($hooks['limit'] = 0 , 'market'),
            "groups" => $new->feeds($hooks['limit'] = 0, 'groups'),
            "contact" => $new->feeds($hooks['limit'] = 0, 'contact'),
            "friends"  => $new->friends($hooks['limit'] = 0),
            "user_info" =>[$new->isLoggedIn(), $new->getUserData()]
        ]);
        break;
        
         case 'like_post':
          echo  $new->like_post($hooks['feed_id'], $hooks['status']);
         break;

    default:
        break;
}
?>
<pre>
    <?= print_r($new->friends()); ?>
</pre>