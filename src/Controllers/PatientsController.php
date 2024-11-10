<?php

namespace App\Controllers;

use App\Models\Patients;
use App\Controllers\BaseController;
use \Exception;

class PatientsController extends BaseController
{
    protected $patients;

    public function __construct()
    {
        $this->patients = new Patients(); // Initialize the Patients model
    }

    // List all patients
    public function list()
    {
        try {
            $data['patients'] = $this->patients->getAllPatients();
            echo $this->render('patient-records.mustache', $data);
        } catch (Exception $e) {
            echo "Error retrieving patient records: " . $e->getMessage();
        }
    }

    // Show the form to add a new patient
    public function showAddForm()
    {
        echo $this->render('add-records.mustache');
    }

    // Save a new patient record
    public function save()
    {
        try {
            $this->patients->save(
                $_POST['case_no'],
                $_POST['last_name'],
                $_POST['first_name'],
                $_POST['middle_name'],
                $_POST['gender'],
                $_POST['age'],
                $_POST['contact_no']
            );
            header('Location: /patient-records'); // Redirect to the patient list
        } catch (Exception $e) {
            echo "Error saving patient: " . $e->getMessage();
        }
    }

    // Show the form to edit an existing patient
    public function showEditForm($case_no)
    {
        try {
            $data['patient'] = $this->patients->getPatient($case_no);
            echo $this->render('edit-record.mustache', $data);
        } catch (Exception $e) {
            echo "Error loading edit form: " . $e->getMessage();
        }
    }

    // Update an existing patient record
    public function update($case_no)
    {
        try {
            $this->patients->update(
                $case_no,
                $_POST['last_name'],
                $_POST['first_name'],
                $_POST['middle_name'],
                $_POST['gender'],
                $_POST['age'],
                $_POST['contact_no']
            );
            header('Location: /patient-records'); // Redirect to the patient list
        } catch (Exception $e) {
            echo "Error updating patient: " . $e->getMessage();
        }
    }

    // View a single patient record
    public function view($case_no)
    {
        try {
            $data['patient'] = $this->patients->getPatient($case_no);
            echo $this->render('view-record.mustache', $data);
        } catch (Exception $e) {
            echo "Error viewing patient record: " . $e->getMessage();
        }
    }

    // Delete a patient record
    public function delete($case_no)
    {
        try {
            $this->patients->delete($case_no);
            header('Location: /patient-records'); // Redirect to the patient list
        } catch (Exception $e) {
            echo "Error deleting patient record: " . $e->getMessage();
        }
    }
}
