<?php

namespace App\Models;

use App\Models\BaseModel;
use \PDO;

class OutPatient extends BaseModel
{
    // Save outpatient findings to the database
    public function save($data)
    {
        $sql = "INSERT INTO outpatient_findings (
                    case_number, date, chief_complaint, history_present_illness, physical_examination, diagnosis, 
                    blood_pressure, respiratory_rate, capillary_refill, temperature, weight, pulse_rate, 
                    medication_treatment, attending_physician
                ) VALUES (
                    :case_number, :date, :chief_complaint, :history_present_illness, :physical_examination, :diagnosis, 
                    :blood_pressure, :respiratory_rate, :capillary_refill, :temperature, :weight, :pulse_rate, 
                    :medication_treatment, :attending_physician
                )";

        $stmt = $this->db->prepare($sql);

        // Bind parameters
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }

        try {
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Error saving outpatient findings: " . $e->getMessage());
        }
    }
}
