<nav>
    <?php foreach(
        [
            ['ri-user-follow-fill','Home', 'friends/'],
            ['ri-user-received-fill','Friend Requests', 'friends/friend-request.php'],
            ['ri-user-add-fill','Suggestions','friends/Suggestions.php'],
            ['ri-group-fill','All Friends', 'friends/all-friends.php'],
            ['ri-cake-2-fill','Birthdays', 'friends/birthday.php']
        ]
            as $key => $menu
    ): $key += 1 ?>
    <a href="/<?= $menu[2]; ?>" class="nav-link-custom <?= $page[1] == $key ? 'active' : '' ?>  my-2">
        <i class="<?= $menu[0]; ?>"></i> <?= $menu[1]; ?>
    </a>
    <?php endforeach; ?>
</nav>