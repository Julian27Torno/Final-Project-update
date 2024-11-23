<?php

namespace App\Controllers;

use App\Models\Doctors;
use App\Controllers\BaseController;
use App\Controllers\UserLogsController; // Import the UserLogsController

class DoctorsController extends BaseController
{
    // Display the add-doctors page
    public function index()
    {
        $doctorsModel = new Doctors();
        $doctors = $doctorsModel->getAll();

        return $this->render('add-doctors', [
            'doctors' => $doctors
        ]);
    }

    // Save a new doctor record
    public function store()
    {
        // Start the session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'doctor_id' => $_POST['doctor_id'],
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'specialization' => $_POST['specialization'],
                'contact_no' => $_POST['contact_no'],
                'email' => $_POST['email']
            ];

            $doctorsModel = new Doctors();
            $doctorsModel->save($data);

            // Log the action
            if (isset($_SESSION['user_id'])) {
                $logsController = new UserLogsController();
                $logsController->logAction(
                    $_SESSION['user_id'], // Assuming the logged-in user's ID is stored in $_SESSION
                    'ADD',
                    'doctor',
                    "Added a new doctor: {$data['first_name']} {$data['last_name']}"
                );
            } else {
                // Handle case where user_id is not set
                error_log("Warning: user_id is not set in session. Action not logged.");
            }

            // Redirect back to the add-doctors page with success message
            header('Location: /add-doctors?success=Doctor added successfully');
            exit();
        }
    }
}
