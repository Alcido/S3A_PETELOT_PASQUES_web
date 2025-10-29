<?php
declare(strict_types=1);

use src\classes\dispatch\Dispatcher;
use src\classes\repository\QuoicouRepository;

require_once 'vendor/autoload.php';

session_start();

QuoicouRepository::setConfig("config/config.db.ini");

$demandeConn = (isset($_GET['action']) and $_GET['action'] === 'register');

if (!isset($_SESSION['user']) and !$demandeConn) {
    $action = "login";
} else {
    $action = $_GET['action'] ?? 'default';
}

$dispatcher = new Dispatcher($action);
$dispatcher->run();
