<?php

namespace App\Controllers;

use App\Models\Patients;
use App\Controllers\BaseController;
use Dompdf\Dompdf;
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

    // Method to handle form submission and add a new patient record
    public function addRecord()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        // Redirect to the patients list with success message
        header("Location: /patients?success=Record updated successfully");
        exit;
    } else {
        // Redirect to the edit page with error message
        header("Location: /edit-patient/{$case_no}?error=Failed to update record");
        exit;
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
    $patientModel = new Patients(); // Ensure the class name matches
    $patient = $patientModel->find($case_no);

    // Check if patient exists
    if (!$patient) {
        die("Patient not found or case number invalid!");
    }

    // Initialize Dompdf
    $dompdf = new Dompdf();

    // Generate HTML for PDF
    $html = "
    <h1>Patient Record</h1>
    <p><strong>Case No:</strong> P-{$patient['case_no']}</p>
    <p><strong>Last Name:</strong> {$patient['last_name']}</p>
    <p><strong>First Name:</strong> {$patient['first_name']}</p>
    <p><strong>Middle Name:</strong> {$patient['middle_name']}</p>
    <p><strong>Gender:</strong> {$patient['gender']}</p>
    <p><strong>Age:</strong> {$patient['age']}</p>
    <p><strong>Contact No.:</strong> {$patient['contact_no']}</p>
    <p><strong>Date Added:</strong> {$patient['date_added']}</p>
    ";

    // Load HTML into Dompdf
    $dompdf->loadHtml($html);

    // (Optional) Setup paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the PDF
    $dompdf->render();

    // Output the generated PDF to browser
    $dompdf->stream("patient_record_{$case_no}.pdf", ["Attachment" => false]);
}




}
