<?php

namespace App\Controllers;

use App\Models\Patients;
use App\Controllers\BaseController;
use App\Models\OutPatient;
use App\Controllers\UserLogsController; // Import the UserLogsController
use Fpdf\Fpdf; 
use \Exception;

class PatientsController extends BaseController
{
    public function showPatients()
    {
        $patientsModel = new Patients();
    
        // Get the search query from the request
        $searchQuery = $_GET['search'] ?? '';
    
        // Check if the search query is empty
        if (trim($searchQuery) === '') {
            // Fetch all patients if the search query is empty
            $patients = $patientsModel->getAll();
        } else {
            // Fetch patients based on the search query
            $patients = $patientsModel->search($searchQuery);
        }
    
        // Render the patient records table view
        echo $this->render('patients', [
            'patients' => $patients,
            'search_query' => $searchQuery, // Pass the search query back to the view
        ]);
    }

    public function addForm()
    {
        echo $this->render('add-records');
    }

    public function addRecord()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Start session if not already started
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
    
                // Retrieve form data
                $lastname = $_POST['lastname'] ?? '';
                $firstname = $_POST['firstname'] ?? '';
                $middleinitial = $_POST['middleinitial'] ?? '';
                $address = $_POST['address'] ?? '';
                $age = $_POST['age'] ?? '';
                $birthday = $_POST['birthday'] ?? '';
                $birthplace = $_POST['birthplace'] ?? '';
                $civil_status = $_POST['civil_status'] ?? '';
                $gender = $_POST['gender'] ?? '';
                $mobile = $_POST['mobile'] ?? '';
                $religion = $_POST['religion'] ?? '';
                $occupation = $_POST['occupation'] ?? '';
    
                // Validate required fields
                if (empty($lastname) || empty($firstname) || empty($address) || empty($age) || empty($birthday) || empty($mobile)) {
                    throw new Exception("Please fill in all required fields.");
                }
    
                // Initialize the Patients model
                $patientsModel = new Patients();
    
                // Prepare data array
                $patientData = [
                    'lastname' => $lastname,
                    'firstname' => $firstname,
                    'middleinitial' => $middleinitial,
                    'address' => $address,
                    'age' => $age,
                    'birthday' => $birthday,
                    'birthplace' => $birthplace,
                    'civil_status' => $civil_status,
                    'gender' => $gender,
                    'mobile' => $mobile,
                    'religion' => $religion,
                    'occupation' => $occupation
                ];
    
                // Insert the new record into the database
                $result = $patientsModel->insert($patientData);
    
                if ($result) {
                    // Log the action
                    if (isset($_SESSION['user_id'])) {
                        $logsController = new UserLogsController();
                        $logsController->logAction(
                            $_SESSION['user_id'],
                            'ADD',
                            'patient',
                            "Added a new patient: {$firstname} {$lastname}"
                        );
                    } else {
                        error_log("Warning: user_id is not set in session. Action not logged.");
                    }
    
                    // Redirect to the success page
                    echo $this->render('patients-success', [
                        'success' => "Patient {$firstname} {$lastname} has been successfully added!"
                    ]);
                    exit;
                } else {
                    throw new Exception("Failed to add patient record.");
                }
            }
    
            // Render the add-records form if the request is GET
            echo $this->render('add-records');
        } catch (Exception $e) {
            error_log("Error adding patient record: " . $e->getMessage());
            echo $this->render('add-records', ['error' => 'Failed to add patient record. Please try again.']);
        }
    }
    

    public function editPatient($case_no)
    {
        $patientsModel = new Patients();
    
        // Fetch patient data by case number
        $patient = $patientsModel->find($case_no);
    
        // Check if patient exists
        if (!$patient) {
            // Redirect or show an error message
            header("Location: /patients?error=Patient not found");
            exit;
        }
    
        // Render the edit form with patient data
        echo $this->render('edit-patient', [
            'case_no' => $patient->case_no,
            'last_name' => $patient->last_name,
            'first_name' => $patient->first_name,
            'middle_name' => $patient->middle_name,
            'age' => $patient->age,
            'gender' => $patient->gender,
            'contact_no' => $patient->contact_no,
            'is_male' => $patient->gender === 'Male',
            'is_female' => $patient->gender === 'Female',
        ]);
    }

    public function updatePatient($case_no)
    {
        $patientsModel = new Patients();

        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Fetch form data
        $last_name = $_POST['last_name'];
        $first_name = $_POST['first_name'];
        $middle_name = $_POST['middle_name'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $contact_no = $_POST['contact_no'];

        // Update the patient record
        $result = $patientsModel->update($case_no, $last_name, $first_name, $middle_name, $age, $gender, $contact_no);

        if ($result) {
            // Log the action
            if (isset($_SESSION['user_id'])) {
                $logsController = new UserLogsController();
                $logsController->logAction(
                    $_SESSION['user_id'],
                    'UPDATE',
                    'patient',
                    "Updated patient record for case number {$case_no}"
                );
            } else {
                error_log("Warning: user_id is not set in session. Action not logged.");
            }

            // Redirect to the patients list with success message
            header("Location: /patients?success=Record updated successfully");
            exit;
        } else {
            // Redirect to the edit page with error message
            header("Location: /edit-patient/{$case_no}?error=Failed to update record");
            exit();
        }
    }

    public function viewPatient($case_no)
    {
        $patientsModel = new Patients();

        // Fetch patient data by case number
        $patient = $patientsModel->find($case_no);

        // Check if patient exists
        if (!$patient) {
            // Redirect or show an error message
            header("Location: /patients?error=Patient not found");
            exit;
        }

        // Render the view form with patient data
        echo $this->render('view-patient', [
            'case_no' => $patient->case_no,
            'last_name' => $patient->last_name,
            'first_name' => $patient->first_name,
            'middle_name' => $patient->middle_name,
            'age' => $patient->age,
            'gender' => $patient->gender,
            'contact_no' => $patient->contact_no,
            'address' => $patient->address,
            'birthday' => $patient->birthday,
            'birthplace' => $patient->birthplace,
            'civil_status' => $patient->civil_status,
            'religion' => $patient->religion,
            'occupation' => $patient->occupation,
            'date_added' => $patient->date_added,
        ]);
    }

    public function printPDF($case_no)
    {
        $patientModel = new Patients();

        $patient = $patientModel->find($case_no);
        
    
        // Check if patient exists
        if (!$patient) {
            die("Patient not found or case number invalid!");
        }
        
      
        // Initialize FPDF
        $pdf = new FPDF();
        $pdf->AddPage();
    
        // Add a logo
        $logoPath = __DIR__ . '/../../images/logo.png'; // Update with your actual logo path
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 10, 10, 30); // Position: x=10, y=10; Width=30mm
        }
    
        // Add header
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetTextColor(0, 102, 204); // Blue color
        $pdf->Cell(0, 15, "Patient Personal Information", 0, 1, 'C');
        
        // Hopes and Heal Hospital - Smaller text
        $pdf->SetFont('Times', '', 12);
        $pdf->SetTextColor(0, 0, 0); // Black color
        $pdf->Cell(0, 5, "Hopes and Heal Hospital", 0, 1, 'C');
        $pdf->Ln(10);
    
        // Add horizontal line
        $pdf->SetDrawColor(0, 0, 0); // Black line
        $pdf->SetLineWidth(0.5); // Line thickness
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(10); // Spacing after the line
    
        // Left-side information
        $pdf->SetFont('Arial', '', 12);
        $leftX = 10; // Left margin
        $rightX = 110; // Right side starting point
    
        // Case Number
        $pdf->SetXY($leftX, $pdf->GetY());
        $pdf->Cell(90, 10, "Case Number: {$patient->case_no}", 0, 0, 'L');
    
        // Age
        $pdf->SetXY($rightX, $pdf->GetY());
        $pdf->Cell(90, 10, "Age: {$patient->age}", 0, 1, 'L');
    
        // Patient Name
        $pdf->SetXY($leftX, $pdf->GetY());
        $pdf->Cell(90, 10, "Patient Name: {$patient->first_name} {$patient->middle_name} {$patient->last_name}", 0, 0, 'L');
    
        // Contact Number
        $pdf->SetXY($rightX, $pdf->GetY());
        $pdf->Cell(90, 10, "Contact Number: {$patient->contact_no}", 0, 1, 'L');
    
        // Gender
        $pdf->SetXY($leftX, $pdf->GetY());
        $pdf->Cell(90, 10, "Gender: {$patient->gender}", 0, 0, 'L');

        // Date Added
        $pdf->SetXY($rightX, $pdf->GetY());
        $pdf->Cell(90, 10, "Occupation: {$patient->occupation}", 0, 1, 'L');
        
        $pdf->SetXY($leftX, $pdf->GetY());
        $pdf->Cell(90, 10, "Address: {$patient->address}", 0, 0, 'L');

        $pdf->SetXY($rightX, $pdf->GetY());
        $pdf->Cell(90, 10, "Birth Date: {$patient->birthday}", 0, 1, 'L');

        $pdf->SetXY($leftX, $pdf->GetY());
        $pdf->Cell(90, 10, "Birth Place: {$patient->birthplace}", 0, 0, 'L');

        $pdf->SetXY($rightX, $pdf->GetY());
        $pdf->Cell(90, 10, "Civil Status: {$patient->civil_status}", 0, 1, 'L');
        
        $pdf->SetXY($leftX, $pdf->GetY());
        $pdf->Cell(90, 10, "Religion: {$patient->religion}", 0, 0, 'L');

        $pdf->SetXY($rightX, $pdf->GetY());
        $pdf->Cell(90, 10, "Date Added: {$patient->date_added}", 0, 1, 'L');

        $pdf->Ln(10); // Add some space before the line
    $pdf->SetDrawColor(0, 0, 0); // Black color for the line
    $pdf->SetLineWidth(0.5); // Line thickness
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(10); // Add some space after the line

    
        // Output PDF
        $pdf->Output("I", "Patient_Record_{$case_no}.pdf");
    }
    

    

    public function deleteRecord($case_no)
    {
        try {
            $patientsModel = new Patients();

            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Check if the patient exists
            $patient = $patientsModel->getPatientByCaseNumber($case_no);

            if (!$patient) {
                throw new \Exception("No patient found with case number {$case_no}");
            }

            // Delete the patient record
            $patientsModel->deleteByCaseNumber($case_no);

            // Log the action
            if (isset($_SESSION['user_id'])) {
                $logsController = new UserLogsController();
                $logsController->logAction(
                    $_SESSION['user_id'],
                    'DELETE',
                    'patient',
                    "Deleted patient record with case number {$case_no}"
                );
            } else {
                error_log("Warning: user_id is not set in session. Action not logged.");
            }

            // Redirect back with a success message
            header("Location: /patients?success=Patient record deleted successfully");
            exit();
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            error_log("Error deleting patient: " . $e->getMessage());

            // Redirect back with an error message
            header("Location: /patients?error=Failed to delete patient record");
            exit();
        }
    }
}
