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
            try {
                $data = [
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'specialization' => $_POST['specialization'],
                    'contact_no' => $_POST['contact_no'],
                    'email' => $_POST['email'],
                    'gender' => $_POST['gender'],
                    'birthday' => $_POST['birthday'],
                    'age' => $_POST['age'],
                ];
    
                // Validate required fields
                if (empty($data['first_name']) || empty($data['last_name']) || empty($data['specialization']) || empty($data['contact_no']) || empty($data['email'])) {
                    throw new \Exception("Please fill in all required fields.");
                }
    
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
    
                // Render success page
                echo $this->render('doctors-success', [
                    'success' => "Doctor {$data['first_name']} {$data['last_name']} has been successfully added!"
                ]);
                exit();
            } catch (\Exception $e) {
                error_log("Error adding doctor: " . $e->getMessage());
                echo $this->render('add-doctors', ['error' => $e->getMessage()]);
            }
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
            'gender' => $doctor->gender,
            'birthday' => $doctor->birthday,
            'age' => $doctor->age,
            
        ]);
    }

    public function editDoctor($doctor_id)
{
   $doctorModel = new Doctors();

    // Fetch outpatient data by case number
    $doctor = $doctorModel->find($doctor_id);
    if (!$doctor) {
        // Redirect or show an error message
        header("Location: /doctor?error=doctor not found");
        exit;
    }
  

    // Render the edit form with outpatient and patient data
    echo $this->render('edit-doctors', [
        'doctor_id' => $doctor->doctor_id,
        'last_name' => $doctor->last_name,
        'first_name' => $doctor->first_name,
        'email' => $doctor->email,
        'specialization' => $doctor->specialization,
        'contact_no'=> $doctor->contact_no,
        'date_added' => $doctor->created_at,
        'age' => $doctor->age,
        
        
    ]);
}

public function updateDoctor($doctor_id)
{
    $doctorModel = new Doctors();

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Fetch form data
    $specialization = $_POST['specialization'];
    $email = $_POST['email'];  // Add email
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $contact_no = $_POST['contact_no'];

    // Update the doctor record with all 6 parameters
    $result = $doctorModel->update($doctor_id, $specialization, $email, $age, $gender, $contact_no);

    if ($result) {
        // Log the action
        if (isset($_SESSION['user_id'])) {
            $doctor = $doctorModel->find($doctor_id); // Assuming find() retrieves the doctor's full name as well
            $first_name = $doctor->first_name;
            $last_name = $doctor->last_name;
            $logsController = new UserLogsController();
            $logsController->logAction(
                $_SESSION['user_id'],
                'UPDATE',
                'doctor',
                "Updated doctor: {$first_name} {$last_name}"  // Make sure to use the updated doctor's info for logging
            );
        } else {
            error_log("Warning: user_id is not set in session. Action not logged.");
        }

        // Redirect to the doctors list with success message
        header("Location: /add-doctors?success=Record updated successfully");
        exit;
    } else {
        // Redirect to the edit page with error message
        header("Location: /edit-doctors/{$doctor_id}?error=Failed to update record");
        exit();
    }
}


    public function deleteDoctor($doctor_id) 
{
    try {
        // Create an instance of the Doctors model
        $doctorModel = new Doctors();

        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the doctor exists
        $doctor = $doctorModel->find($doctor_id);

        if (!$doctor) {
            throw new \Exception("No doctor found with ID {$doctor_id}");
        }

        // Delete the doctor record
        $doctorModel->delete($doctor_id);

        // Log the action
        if (isset($_SESSION['user_id'])) {
            $logsController = new UserLogsController();
            $logsController->logAction(
                $_SESSION['user_id'],
                'DELETE',
                'doctor',
                "Deleted doctor record with ID {$doctor_id}"
            );
        } else {
            error_log("Warning: user_id is not set in session. Action not logged.");
        }

        // Redirect back with a success message
        header("Location: /add-doctors?success=Doctor record deleted successfully");
        exit();
    } catch (\Exception $e) {
        // Log the error for debugging purposes
        error_log("Error deleting doctor: " . $e->getMessage());

        // Redirect back with an error message
        header("Location: /doctors?error=Failed to delete doctor record");
        exit();
    }
}

}
