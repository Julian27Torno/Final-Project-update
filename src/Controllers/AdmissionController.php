<?php

namespace App\Controllers;

use App\Models\Admission;
use App\Controllers\BaseController;

class AdmissionController extends BaseController
{
    // Display the admission form and admitted patients
    public function index()
{
    $admissionModel = new Admission();
    $admittedPatients = $admissionModel->getAllAdmissions(); // Get admitted patients
    $availableRooms = $admissionModel->getAvailableRooms();  // Get available rooms

    // Pass data to the view
    echo $this->render('admission-record', [
        'admitted_patients' => $admittedPatients,
        'available_rooms' => $availableRooms
    ]);
}


    // Handle form submission and save data
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'case_number' => $_POST['case_number'],
                'date_admitted' => $_POST['date_admitted'],
                'reason' => $_POST['reason'],
                'room_number' => $_POST['room_number'],
                'attending_physician' => $_POST['attending_physician'],
            ];
    
            $admissionModel = new Admission();
            $admissionModel->save($data);
    
            // Mark the selected room as unavailable
            $admissionModel->markRoomAsUnavailable($data['room_number']);
    
            // Redirect back to the form with a success message
            header('Location: /admission?success=Record added successfully');
            exit();
        }
    }
    
    public function create()
    {
        $admissionModel = new Admission();
        $availableRooms = $admissionModel->getAvailableRooms();
    
        // Debug available rooms
        print_r($availableRooms);
        die();
    }
    
public function releaseRoom($roomNumber)
{
    $this->updateRoomStatus($roomNumber, 'available');
}

public function discharge($case_number)
{
    try {
        $admissionModel = new \App\Models\Admission();

        // Fetch the room associated with the case number
        $room_number = $admissionModel->getRoomByCaseNumber($case_number);

        if (!$room_number) {
            throw new \Exception("No room found for case number {$case_number}");
        }

        // Remove the patient from admission records
        $admissionModel->removeByCaseNumber($case_number);

        // Mark the room as available
        $admissionModel->markRoomAsAvailable($room_number);

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
