<?php

require "vendor/autoload.php";
require "init.php";

// Database connection object (from init.php (DatabaseConnection))
global $conn;

try {

    // Create Router instance
    $router = new \Bramus\Router\Router();

    // Define routes
    $router->get('/', '\App\Controllers\LoginController@showForm');

    $router->get('/suppliers', '\App\Controllers\SupplierController@list');
    $router->get('/suppliers/{id}', '\App\Controllers\SupplierController@single');
    $router->post('/suppliers/{id}', '\App\Controllers\SupplierController@update');

    $router->get('/registration-form', '\App\Controllers\RegistrationController@showForm');
    $router->post('/register', '\App\Controllers\RegistrationController@register');

    $router->get('/outpatient', '\App\Controllers\OutPatientController@create'); // Route to display the form
    $router->post('/outpatient/store', '\App\Controllers\OutPatientController@store'); // Route to save data

    $router->get('/admission', 'App\Controllers\AdmissionController@index'); // Display admission form
    $router->post('/admission/store', 'App\Controllers\AdmissionController@store'); // Save admission record
    $router->post('/admission/discharge/{case_number}', 'App\Controllers\AdmissionController@discharge');




    $router->get('/print-pdf/{case_no}', 'App\Controllers\PatientController@printPDF');

    
    $router->post('/login', '\App\Controllers\LoginController@login');
    $router->get('/dashboard', '\App\Controllers\LoginController@showDashboard');// Route for the welcome page after login
    $router->get('/patients', '\App\Controllers\PatientsController@showPatients');
    $router->get('/logout', '\App\Controllers\LoginController@logout');

    $router->get('/view-patient/{case_no}', 'App\Controllers\PatientsController@viewPatient');

    $router->get('/edit-patient/{case_no}', 'App\Controllers\PatientsController@editPatient');
    $router->post('/update/{case_no}', 'App\Controllers\PatientsController@updatePatient');


    $router->get('/add-records', '\App\Controllers\PatientsController@addForm');
    $router->post('/addRecord', '\App\Controllers\PatientsController@addRecord');
    $router->run();

} catch (Exception $e) {

    echo json_encode([
        'error' => $e->getMessage()
    ]);

}
