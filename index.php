<?php
session_start();
define('BOSS_TRADER_APP', 1);
require_once "includes/db.inc.php";
require_once "includes/dbconfig.inc.php";

$get = $_GET; $post = $_POST; $session = $_SESSION;
?>

<?php
require_once 'controller/controller.ctrl.php';

