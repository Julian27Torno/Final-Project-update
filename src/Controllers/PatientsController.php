<?php

namespace App\Controllers;

use App\Models\Patients;
use App\Controllers\BaseController;
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

                    // Redirect to the patients list page with success message
                    header("Location: /patients?success=Record added successfully.");
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

    // Add a logo (Ensure the path to your logo is correct)
    $logoPath = __DIR__ . '/../../images/logo.png'; // Update with your actual logo path
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, 10, 10, 30); // Position: x=10, y=10; Width=30mm
    }

    // Add a header
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->SetTextColor(0, 102, 204); // Blue color
    $pdf->Cell(0, 15, "Patient Record", 0, 1, 'C');
    
    // Hopes and Heal Hospital - Smaller and Times New Roman
    $pdf->SetFont('Times', '', 12); // Times New Roman, regular, size 12
    $pdf->SetTextColor(0, 0, 0); // Black color
    $pdf->Cell(0, 5, "Hopes and Heal Hospital", 0, 1, 'C'); // Center-aligned
    $pdf->Ln(20); // Add vertical spacing

    // Add patient details with improved styling
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(0, 0, 0); // Black color

    $pdf->Cell(50, 10, "Case No:", 1, 0, 'L', false);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $patient->case_no, 1, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, "Last Name:", 1, 0, 'L', false);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $patient->last_name, 1, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, "First Name:", 1, 0, 'L', false);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $patient->first_name, 1, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, "Middle Name:", 1, 0, 'L', false);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $patient->middle_name, 1, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, "Gender:", 1, 0, 'L', false);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $patient->gender, 1, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, "Age:", 1, 0, 'L', false);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $patient->age, 1, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, "Contact No.:", 1, 0, 'L', false);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $patient->contact_no, 1, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, "Date Added:", 1, 0, 'L', false);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $patient->date_added, 1, 1);

    // Add footer
$pdf->SetY(-30); // Move to 30mm from the bottom of the page
$pdf->SetFont('Arial', 'I', 8); // Set font to Arial, italic, size 8
$pdf->SetTextColor(128, 128, 128); // Gray color
$pdf->Cell(0, 10, 'Printed on: ' . date('Y-m-d H:i:s'), 0, 0, 'C'); // Timestamp in the center

// Add signature
$pdf->Ln(5); // Move slightly below the timestamp
$pdf->SetFont('Arial', 'B', 10); // Set font to Arial, bold, size 10
$pdf->SetTextColor(0, 0, 0); // Black color
$pdf->Cell(0, 10, 'Signed by: John Doe', 0, 0, 'R'); // Signature aligned to the right

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
