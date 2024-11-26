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

    public function viewDoctor($doctor_id)
    {
        $doctorModel = new Doctors();

        // Fetch patient data by case number
        $doctor = $doctorModel->find($doctor_id);

        // Check if patient exists
        if (!$doctor) {
            // Redirect or show an error message
            header("Location: /doctor?error=doctor not found");
            exit;
        }

        // Render the view form with patient data
        echo $this->render('view-doctors', [
            'doctor_id' => $doctor->doctor_id,
            'last_name' => $doctor->last_name,
            'first_name' => $doctor->first_name,
            'email' => $doctor->email,
            'specialization' => $doctor->specialization,
            'contact_no'=> $doctor->contact_no,
            'date_added' => $doctor->created_at,
            
        ]);
    }
}
