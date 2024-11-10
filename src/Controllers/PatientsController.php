<?php

namespace App\Controllers;

use App\Models\Patients;
use App\Controllers\BaseController;
use \Exception;

class PatientsController extends BaseController
{
    public function showPatients()
    {
        try {
            // Initialize the Patients model
            $patientsModel = new Patients();

            // Fetch all patients from the database
            $patients = $patientsModel->all();

            // Check if records are fetched
            if (empty($patients)) {
                throw new Exception("No patient records found.");
            }

            // Render the welcome view with the fetched patient data
            echo $this->render('/patients', ['patients' => $patients]);

        } catch (Exception $e) {
            // Log the error message for debugging
            error_log("Error fetching patient records: " . $e->getMessage());

            // Render an error message on the view
            echo $this->render('patients', ['error' => 'Failed to load patient records. Please try again later.']);
        }
    }
    public function addForm(){
        $patientsModel = new Patients();

        echo $this->render('add-records');
    }
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

                // Prepare the data array
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
}
