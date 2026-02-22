<!DOCTYPE html>
<html lang="en">
<head>
     <?php 
                            include __DIR__."/backend/function.php"; 
                            $new=new codium($conn);
                            $fetch_feeds=$new->feeds( 0, 'feed');
                            $fetch_user_info=$new->getUserData()['data'];
                            $profile=json_decode($fetch_user_info['profile'],true);
                            $fetch_reels=$new->feeds($hooks['limit'] = 0, 'reel');
            // "reels"   => ,
            // "market" => $new->feeds($hooks['limit'] = 0 , 'market'),
            // "groups" => $new->feeds($hooks['limit'] = 0, 'groups'),
            // "contact" => $new->feeds($hooks['limit'] = 0, 'contact'),
            // "friends"  => $new->friends($hooks['limit'] = 0),
            // "user_info" =>[$new->isLoggedIn(), $new->getUserData()]

                        ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Monieflow is your all-in-one digital lifestyle platform, combining the power of social networking, hotel booking, cryptocurrency, gaming, job discovery, live video streaming, short-form reels, gift cards, and e-commerce — all in one seamless experience. Whether you're connecting with friends like on Facebook, exploring travel deals, trading crypto, or shopping online, Moniflow empowers users and businesses to thrive in a vibrant, interactive ecosystem.">
    <meta name="keywords" content="Monieflow is a next-generation digital platform that brings together social media, hotel booking, cryptocurrency, gaming, job opportunities, live video, short-form reels, gift cards, and e-commerce — all in one seamless experience. Whether you're connecting with friends, booking your next getaway, trading crypto, discovering new games, or shopping online, Moniflow empowers users and businesses to thrive in a dynamic, interactive ecosystem.">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <link rel="icon" href="/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <script src="/api.js"></script>
    <script type="module" src="script.js"></script>
    <title>monieflow -  conneting you to friends and family around the globe</title>
</head>
<body>
    <div id="preloader-loading" style="z-index: 20000;" class="position-fixed bg-white top-0 bottom-0 w-100 justify-content-center d-flex align-items-center placeholder-glow">
        <img src="/logo.png" alt="" style="width: 80px;" class="placeholder bg-white">
    </div>
    <div class="main-div-for-root-per-page">
        <div id="main-feeds">
            <?php include __DIR__."/pages/feed.php"; ?>
        </div>
        <div id="main-feed-post">
            <?php include __DIR__."/pages/createPost.php"; ?>
        </div>
        <div id="main-feed-reel-post">
            <?php include __DIR__."/pages/create-reels.html"; ?>
        </div>        
        <div id="main-profile">
            <?php include __DIR__."/pages/profile.js"; ?>
        </div>
    </div>
</body>
</html>