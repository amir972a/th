<?php
require_once '../config/config.php';
require_once '../app/Core/Autoloader.php';
require_once '../app/Core/Router.php';

session_start();

$router = new Router();

// Load routes
require_once '../app/routes.php';

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
