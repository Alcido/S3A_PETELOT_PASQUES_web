<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';

session_start();

if (!isset($_SESSION['user'])) {
    $action = "login";
} else {
    $action = $_GET['action'];
}

$dispatcher = new Dispatcher($action);
$dispatcher->run();
