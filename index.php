<?php

require "vendor/autoload.php";
require "init.php";

// Database connection object (from init.php (DatabaseConnection))
global $conn;

try {

    // Create Router instance
    $router = new \Bramus\Router\Router();

    // Define routes
    $router->get('/', '\App\Controllers\HomeController@index');
    $router->get('/suppliers', '\App\Controllers\SupplierController@list');
    $router->get('/suppliers/{id}', '\App\Controllers\SupplierController@single');
    $router->post('/suppliers/{id}', '\App\Controllers\SupplierController@update');

    $router->get('/registration-form', '\App\Controllers\RegistrationController@showForm');
    $router->post('/register', '\App\Controllers\RegistrationController@register');

    
    $router->get('/login-form', '\App\Controllers\LoginController@showForm');
    $router->post('/login', '\App\Controllers\LoginController@login');
    $router->get('/welcome', '\App\Controllers\LoginController@welcome');// Route for the welcome page after login
    $router->get('/logout', '\App\Controllers\LoginController@logout');

    $router->run();

} catch (Exception $e) {

    echo json_encode([
        'error' => $e->getMessage()
    ]);

}
