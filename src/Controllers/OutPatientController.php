<?php

namespace App\Controllers;

use App\Models\OutPatient;
use App\Controllers\BaseController;

class OutPatientController extends BaseController
{
    // Render the outpatient form
    public function create()
    {
        echo $this->render('/out-patient');
    }

    // Save outpatient findings to the database
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'case_number' => $_POST['case_number'],
                'date' => $_POST['date'],
                'chief_complaint' => $_POST['chief_complaint'],
                'history_present_illness' => $_POST['history_present_illness'],
                'physical_examination' => $_POST['physical_examination'],
                'diagnosis' => $_POST['diagnosis'],
                'blood_pressure' => $_POST['blood_pressure'],
                'respiratory_rate' => $_POST['respiratory_rate'],
                'capillary_refill' => $_POST['capillary_refill'],
                'temperature' => $_POST['temperature'],
                'weight' => $_POST['weight'],
                'pulse_rate' => $_POST['pulse_rate'],
                'medication_treatment' => $_POST['medication_treatment'],
                'attending_physician' => $_POST['attending_physician'],
            ];

            $outPatientModel = new OutPatient();
            $outPatientModel->save($data);

            header('Location: /outpatient/findings?success=Record added successfully');
            exit();
        }
    }
}
