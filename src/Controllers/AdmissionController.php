<?php

namespace App\Controllers;

use App\Models\Admission;
use App\Models\Patients;
use App\Models\Doctors;
use App\Controllers\BaseController;
use App\Controllers\UserLogsController; // Import UserLogsController

class AdmissionController extends BaseController
{
    // Display the admission form and admitted patients
    public function index()
    {
        $patientsModel = new Patients();
        $admissionModel = new Admission();
        $doctorsModel = new Doctors();
        $admittedPatients = $admissionModel->getActiveAdmissions(); // Get admitted patients
        // Get past addmitteed patients
        $availableRooms = $admissionModel->getAvailableRooms();  // Get available rooms
        $availablePatients = $patientsModel->getAvailablePatients(); // Use this method to fetch patients
        $availableDoctors = $doctorsModel->getAll();
      
       

        
        // Pass data to the view
        echo $this->render('add-admission', [
            'admitted_patients' => $admittedPatients,
            'available_rooms' => $availableRooms,
            'available_patients' => $availablePatients,
            'available_doctors' => $availableDoctors,
            
          
            
        ]);
    }

    public function showAdmissionRecords()
    {
        $admissionModel = new Admission();
    
        // Fetch the search query from GET, defaulting to an empty string
        $searchQuery = $_GET['search'] ?? '';
    
        // Fetch records based on the query
        $dischargedPatients = $admissionModel->search($searchQuery);
    
        // Render the view
        echo $this->render('admission-records', [
            'discharged_patients' => $dischargedPatients,
            'search_query' => $searchQuery,
        ]);
    }
    
    // Handle form submission and save data
    public function store()
{
    // Start the session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'case_number' => $_POST['case_number'],
            'date_admitted' => $_POST['date_admitted'],
            'reason' => $_POST['reason'],
            'room_number' => $_POST['room_number'],
            'attending_physician' => $_POST['attending_physician'],
        ];

        // Create an instance of the Admission model
        $admissionModel = new Admission();

        try {
            // Save the admission record to the database
            $admissionModel->save($data);

            // Mark the room as unavailable
            $admissionModel->markRoomAsUnavailable($data['room_number']);

            // Log the action
            if (isset($_SESSION['user_id'])) {
                $logsController = new UserLogsController();
                $logsController->logAction(
                    $_SESSION['user_id'],
                    'ADD',
                    'admission',
                    "Admitted patient with case number {$data['case_number']} to room {$data['room_number']}"
                );
            } else {
                error_log("Warning: user_id is not set in session. Action not logged.");
            }

            // Redirect to the success page with a success message
            return $this->render('admit-success', [
                'success' => 'Admission record added successfully!'
            ]);
        } catch (\Exception $e) {
            // If needed, log the error and display a generic error message
            error_log("Error during admission: " . $e->getMessage());

            // Redirect with a generic error message (optional, if you want to show an error page)
            return $this->render('admit-success', [
                'success' => 'There was an issue processing the admission. Please try again later.'
            ]);
        }
    }
}


    // Discharge a patient and release their room
    public function discharge($case_number)
    {
        // Start the session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            $admissionModel = new Admission();

            // Fetch the room associated with the case number
            $room_number = $admissionModel->getRoomByCaseNumber($case_number);

            if (!$room_number) {
                throw new \Exception("No room found for case number {$case_number}");
            }

            // Remove the patient from admission records
            $admissionModel->removeByCaseNumber($case_number);

            // Mark the room as available
            $admissionModel->markRoomAsAvailable($room_number);

            // Log the action
            if (isset($_SESSION['user_id'])) {
                $logsController = new UserLogsController();
                $logsController->logAction(
                    $_SESSION['user_id'],
                    'DISCHARGE',
                    'admission',
                    "Discharged patient with case number {$case_number} and released room {$room_number}"
                );
            } else {
                error_log("Warning: user_id is not set in session. Action not logged.");
            }

            // Redirect back with a success message
            header("Location: /admission?success=Patient discharged successfully");
            exit();
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            error_log("Error in discharge: " . $e->getMessage());

            // Redirect back with an error message
            header("Location: /admission?error=Failed to discharge patient");
            exit();
        }
    }

    // Fetch available rooms
    public function getAvailableRooms()
    {
        $sql = "SELECT room_number FROM rooms WHERE status = 'available'";
        $statement = $this->db->prepare($sql);
        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Error fetching available rooms: " . $e->getMessage());
        }
    }
}
