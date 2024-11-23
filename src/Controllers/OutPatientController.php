<?php

namespace App\Controllers;

use App\Models\OutPatient;
use App\Models\Patients;
use App\Models\Doctors;
use App\Controllers\BaseController;

class OutPatientController extends BaseController
{
    // Display the outpatient form and records
    public function index()
    {
        $outPatientModel = new OutPatient();
        $patientsModel = new Patients();
        $doctorsModel = new Doctors();

        // Fetch all outpatient records
        $outpatientRecords = $outPatientModel->getAllRecords();

        // Fetch all patients for the dropdown
        $availablePatients = $patientsModel->getAvailablePatients(); // Use this method to fetch patients
         $availableDoctors = $doctorsModel->getAll();

        // Pass data to the view
        echo $this->render('out-patient', [
            'outpatient_records' => $outpatientRecords,
            'available_patients' => $availablePatients, // Pass patients for the dropdown
            'available_doctors' => $availableDoctors
        ]);
    }


    // Handle form submission and save data
    public function store()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'case_number' => $_POST['case_number'] ?? null,
            'date' => $_POST['date'] ?? null,
            'location' => $_POST['location'] ?? null,
            'quality' => $_POST['quality'] ?? null,
            'severity' => $_POST['severity'] ?? null,
            'duration' => $_POST['duration'] ?? null,
            'diagnosis' => $_POST['diagnosis'] ?? null,
            'blood_pressure' => $_POST['blood_pressure'] ?? null,
            'respiratory_rate' => $_POST['respiratory_rate'] ?? null,
            'temperature' => $_POST['temperature'] ?? null,
            'oxygen_saturation' => $_POST['oxygen_saturation'] ?? null,
            'medication_treatment' => $_POST['medication_treatment'] ?? null,
            'attending_physician' => $_POST['attending_physician'] ?? null,
        ];
        

        $outPatientModel = new OutPatient();

        try {
            $outPatientModel->save($data);

            // Redirect back to the form with a success message
            header('Location: /outpatient?success=Record added successfully');
            exit();
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Error saving outpatient record: ' . $e->getMessage()]);
        }
    }
}


    // Method to delete outpatient record (optional functionality)
    public function delete($case_number)
    {
        $outPatientModel = new OutPatient();
        $outPatientModel->deleteRecord($case_number);

        // Redirect back with a success message
        header('Location: /outpatient?success=Record deleted successfully');
        exit();
    }

    public function viewOutPatient($case_number)
{
    $outPatientsModel = new OutPatient();
    $patientsModel = new Patients();

    // Fetch outpatient data by case number
    $outPatient = $outPatientsModel->find($case_number);

    // Check if outpatient record exists
    if (!$outPatient) {
        // Redirect or show an error message if no record is found
        header("Location: /outpatient?error=Outpatient record not found");
        exit;
    }

    // Fetch patient data by case number
    $patient = $patientsModel->find($case_number);

    // Check if patient record exists
    if (!$patient) {
        header("Location: /patients?error=Patient not found for this case number");
        exit;
    }

    // Render the view with outpatient and patient data
    echo $this->render('view-outpatient', [
        'case_number' => $outPatient->case_number,
        'date' => $outPatient->date,
        'location' => $outPatient->location,
        'quality' => $outPatient->quality,
        'severity' => $outPatient->severity,
        'duration' => $outPatient->duration,
        'diagnosis' => $outPatient->diagnosis,
        'blood_pressure' => $outPatient->blood_pressure,
        'respiratory_rate' => $outPatient->respiratory_rate,
        'temperature' => $outPatient->temperature,
        'oxygen_saturation' => $outPatient->oxygen_saturation,
        'medication_treatment' => $outPatient->medication_treatment,
        'attending_physician' => $outPatient->attending_physician,
        'created_at' => $outPatient->created_at,
        'last_name' => $patient->last_name,
        'first_name' => $patient->first_name
    ]);
}


    
public function editOutpatient($case_number)
{
    $outpatientsModel = new OutPatient();
    $patientsModel = new Patients();

    // Fetch outpatient data by case number
    $outpatient = $outpatientsModel->find($case_number);

    // Check if outpatient record exists
    if (!$outpatient) {
        header("Location: /outpatient?error=Outpatient record not found");
        exit;
    }

    // Fetch patient data using the case number
    $patient = $patientsModel->find($case_number);

    // Check if patient exists
    if (!$patient) {
        header("Location: /patients?error=Patient not found for this case number");
        exit;
    }

    // Render the edit form with outpatient and patient data
    echo $this->render('edit-outpatient', [
        'id' => $outpatient->id,
        'case_number' => $outpatient->case_number,
        'date' => $outpatient->date,
        'location' => $outpatient->location,
        'quality' => $outpatient->quality,
        'severity' => $outpatient->severity,
        'duration' => $outpatient->duration,
        'diagnosis' => $outpatient->diagnosis,
        'blood_pressure' => $outpatient->blood_pressure,
        'respiratory_rate' => $outpatient->respiratory_rate,
        'temperature' => $outpatient->temperature,
        'oxygen_saturation' => $outpatient->oxygen_saturation,
        'medication_treatment' => $outpatient->medication_treatment,
        'attending_physician' => $outpatient->attending_physician,
        'created_at' => $outpatient->created_at,
        'last_name' => $patient->last_name,
        'first_name' => $patient->first_name,
    ]);
}

    
public function updateoutPatient($id)
{
    $outpatientsModel = new Outpatient();

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Fetch form data
    $location = $_POST['location'];
    $quality = $_POST['quality'];
    $severity = $_POST['severity'];
    $duration = $_POST['duration'];
    $blood_pressure = $_POST['blood_pressure'];
    $temperature = $_POST['temperature'];
    $oxygen_saturation = $_POST['oxygen_saturation'];

    // Update the patient record
    $result = $outpatientsModel->update($id, $location, $quality, $severity, $duration, $blood_pressure, $temperature, $oxygen_saturation);

    if ($result) {
        // Log the action
        if (isset($_SESSION['user_id'])) {
            $logsController = new UserLogsController();
            $logsController->logAction(
                $_SESSION['user_id'],
                'UPDATE',
                'Outpatient',
                "Updated Outpatient record for case number {$id}"
            );
        } else {
            error_log("Warning: user_id is not set in session. Action not logged.");
        }

        // Redirect to the patients list with success message
        header("Location: /outpatient?success=Outpatient Record updated successfully");
        exit;
    } else {
        // Redirect to the edit page with error message
        header("Location: /edit-patient/{$id}?error=Failed to update record");
        exit();
    }
}


    
    public function deleteOutpatientRecord($id)
    {
        try {
            $outpatientsModel = new OutPatient();

            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Check if the outpatient record exists
            $outpatient = $outpatientsModel->getOutpatientById($id);

            if (!$outpatient) {
                throw new \Exception("No Outpatient record found with ID {$id}");
            }

            // Delete the outpatient record
            $outpatientsModel->deleteRecord($id);

            // Log the action if the user is logged in
            if (isset($_SESSION['user_id'])) {
                $logsController = new UserLogsController();
                $logsController->logAction(
                    $_SESSION['user_id'],
                    'DELETE',
                    'Outpatient',
                    "Deleted outpatient record with ID {$id}"
                );
            } else {
                error_log("Warning: user_id is not set in session. Action not logged.");
            }

            // Redirect back with a success message
            header("Location: /patients?success=Outpatient record deleted successfully");
            exit();
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            error_log("Error deleting outpatient record: " . $e->getMessage());

            // Redirect back with an error message
            header("Location: /patients?error=" . urlencode($e->getMessage()));
            exit();
        }
    }
}
