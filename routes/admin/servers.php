<?php 
$router->add("/admin/servers", function () {
    require("../include/main.php");
    require("../view/admin/servers/list.php");
});


$router->add("/admin/server/delete", function () {
    require("../include/main.php");
    require("../view/admin/servers/delete.php");
});
?>