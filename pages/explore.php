<?php  
    include __dir__."/../config/function.php";
    $main=new Main($conn);
    $feeds=$main->feeds();
    // print_r($feeds);
?>
<div  class="w-100 d-none d-sm-block">
<div class="d-flex p-0 bg-white sticky-top  py-1 d-sm-k overflow-hidden ">
      <a href="javascript:;" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" class="px-3 text-decoration-none text-secondary text-center p-2 fw-bolder border-end"><span class="ri-more-fill"></span></a>
      <input type="text" class="form-control border-0 w-100" style="font-size: small;" placeholder="Search feeds, Friends and ... ">
      <a href="" class="ri-money-dollar-box-line p-1 px-3 fs-3 text-decoration-none"></a>
</div>
<?php
    foreach ($feeds as $feed):
        $image=json_decode($feed['media'],true);
?>
<div class="my-3 bg-white rounded py-3">
        <div class="d-flex gap-3 px-2">
            <img src="/img/rmate2.jpg" style="width: 60px;" class="rounded-circle" alt="">
            <div>
                <div class="text-capitalize fw-bold"><?= $feed['username'] ?></div>
                <div><?= timeAgo($feed['created_at']) ?></div>
            </div>
        </div>
        <div class="p-2" style="font-size:small;"><?= $feed['content'] ?></div>
        <div class="my-2">
            <?php foreach ($image as $images): ?>
               <img src="/img/<?= $images ?>" class="w-100 object-fit-cover" style="max-height: 200px;object-position: top center;" alt="">
            <?php endforeach ?>
        </div>
        <div class="gap-2 p-2 d-flex flex-wrap">
            <a href="?like=<?= $feed['id'] ?>" style="text-decoration: none;display: flex;gap: 5px; align-items: center;"><span class="ri-heart-fill"></span> .<span style="font-size: small;"><?= $feed['total_likes'] ?></span><span>like</span></a>
            <a href="?comments=<?= $feed['id'] ?>" style="text-decoration: none;display: flex;gap: 10px; align-items: center;"><span class="ri-message-fill"></span> .<span style="font-size: small;"><?= $feed['total_comments'] ?></span><span>comment</span></a>
            <a href="?share=<?= $feed['id'] ?>" style="text-decoration: none;display: flex;gap: 10px; align-items: center;"><span class="ri-share-fill"></span><span><?= $feed['shares'] ?></span><span>Share</span></a>
        </div>
</div>
<?php endforeach; ?>
</div>

<div style="max-width: 350px;" class="w-100 sticky-top p-2 d-none d-sm-block">
    <div class="bg-white p-2">
        great products
    </div>
</div>
