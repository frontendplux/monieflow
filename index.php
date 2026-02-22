<?php
$path_to_loads=parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router_path=explode('/',$path_to_loads);

switch ($router_path[1]) {
    case '':
        include __DIR__."/login.php";
        break;
    
    default:
        include __DIR__."/master.php";
        break;
}

?>