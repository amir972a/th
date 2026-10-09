<?php
// app/routes.php
$router->get('/', 'HomeController@index');
$router->get('/search', 'HomeController@search');

$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@login');

$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@register');

$router->get('/logout', 'AuthController@logout');

// Video Routes
$router->get('/upload', 'VideoController@upload');
$router->post('/upload', 'VideoController@upload');
$router->get('/watch/{id}', 'VideoController@watch');
$router->get('/stream/{id}', 'VideoController@stream');
$router->get('/thumbnail/{filename}', 'VideoController@thumbnail');

// Profile Routes
$router->get('/profile', 'ProfileController@show');
$router->get('/profile/{username}', 'ProfileController@show');

// Social Routes
$router->post('/video/{id}/comment', 'SocialController@addComment');
$router->post('/video/{id}/rate', 'SocialController@rateVideo');
$router->post('/friend/{id}', 'SocialController@friendAction');

// Admin Routes
$router->get('/setup', 'SetupController@index');
$router->post('/setup', 'SetupController@index');
$router->get('/admin', 'AdminController@dashboard');
$router->get('/admin/users', 'AdminController@users');
$router->post('/admin/user/{id}/status', 'AdminController@toggleUserStatus');
$router->get('/admin/videos', 'AdminController@videos');
$router->post('/admin/video/{id}/moderate', 'AdminController@moderateVideo');
