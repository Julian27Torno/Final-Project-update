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

    

    $router->get('/registration-form', '\App\Controllers\RegistrationController@showForm');
    $router->post('/register', '\App\Controllers\RegistrationController@register');

    $router->get('/outpatient', '\App\Controllers\OutPatientController@index'); // Main route to display the form and records
    $router->post('/outpatient/store', '\App\Controllers\OutPatientController@store'); // Route to handle form submission
    $router->post('/outpatient/delete/{case_number}', '\App\Controllers\OutPatientController@delete'); // Route to handle record deletion
    $router->get('/view-outpatient/{case_number}', '\App\Controllers\OutPatientController@viewOutPatient');
    $router->get('/edit-outpatient/{case_number}', '\App\Controllers\OutPatientController@editOutpatient');
    $router->post('/update-outpatient/{id}', 'App\Controllers\OutPatientController@updateoutPatient');
    $router->post('/outpatients/delete/{id}', '\App\Controllers\OutPatientController@deleteOutpatientRecord');
    

    $router->get('/admission', 'App\Controllers\AdmissionController@index'); // Display admission form
    $router->post('/admission/store', 'App\Controllers\AdmissionController@store'); // Save admission record
    $router->post('/admission/discharge/{case_number}', 'App\Controllers\AdmissionController@discharge');

    $router->get('/outpatient/findings', '\App\Controllers\OutPatientController@findings');

    $router->post('/patients/delete/{case_no}', '\App\Controllers\PatientsController@deleteRecord');

    $router->get('/add-doctors', '\App\Controllers\DoctorsController@index');
    $router->post('/add-doctors/store', '\App\Controllers\DoctorsController@store');

    
    

    $router->get('/print-pdf/{case_no}', '\App\Controllers\PatientsController@printPDF');



    
    $router->post('/login', '\App\Controllers\LoginController@login');
    $router->get('/dashboard', '\App\Controllers\LoginController@showDashboard');// Route for the welcome page after login
    $router->get('/patients', '\App\Controllers\PatientsController@showPatients');
    $router->get('/logout', '\App\Controllers\LoginController@logout');

    $router->get('/view-patient/{case_no}', 'App\Controllers\PatientsController@viewPatient');

    $router->get('/edit-patient/{case_no}', 'App\Controllers\PatientsController@editPatient');
    $router->post('/update/{case_no}', 'App\Controllers\PatientsController@updatePatient');

    

    $router->get('/login-form', '\App\Controllers\LoginController@showForm');


    $router->get('/user-logs', '\App\Controllers\UserLogsController@index'); 
    $router->get('/add-records', '\App\Controllers\PatientsController@addForm');
    $router->post('/addRecord', '\App\Controllers\PatientsController@addRecord');
    $router->run();

} catch (Exception $e) {

    echo json_encode([
        'error' => $e->getMessage()
    ]);

}
