<?php

namespace App\Models;

use App\Models\BaseModel;
use PDO;

class OutPatient extends BaseModel
{
    // Save outpatient data to the database
    public function save($data)
    {
        $sql = "INSERT INTO outpatient_findings (
            case_number, date, location, quality, severity, duration,
            diagnosis, blood_pressure, respiratory_rate,
            temperature, oxygen_saturation, medication_treatment, attending_physician
        ) VALUES (
            :case_number, :date, :location, :quality, :severity, :duration,
            :diagnosis, :blood_pressure, :respiratory_rate,
            :temperature, :oxygen_saturation, :medication_treatment, :attending_physician
        )";

    
        $stmt = $this->db->prepare($sql);
    
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value); // Binding all data to the query
        }
    
        $stmt->execute();
    }
    
    // Retrieve all outpatient records
    public function getAllRecords()
    {
        $sql = "SELECT * FROM outpatient_findings ORDER BY date DESC";

        $statement = $this->db->prepare($sql);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete an outpatient record by case number
     // Delete an outpatient record by its ID
     public function deleteRecord($id)
     {
         $sql = "DELETE FROM outpatient_findings WHERE id = :id";
         $stmt = $this->db->prepare($sql);
         $stmt->bindValue(':id', $id, PDO::PARAM_INT);
 
         try {
             $stmt->execute();
 
             // Check if the record was actually deleted
             if ($stmt->rowCount() === 0) {
                 throw new Exception("No record found with ID {$id} to delete.");
             }
         } catch (Exception $e) {
             error_log("Error deleting outpatient record: " . $e->getMessage());
             throw new Exception("Error deleting outpatient record.");
         }
     }

    public function find($case_number)
    {
        $sql = "SELECT * FROM outpatient_findings WHERE case_number = :case_number";
        $statement = $this->db->prepare($sql);
    
        try {
            $statement->bindParam(':case_number', $case_number, PDO::PARAM_STR); // Bind the case_number
            $statement->execute();
            return $statement->fetch(PDO::FETCH_OBJ); // Fetch as an object for compatibility
        } catch (\PDOException $e) {
            throw new \Exception("Error finding outpatient record: " . $e->getMessage());
        }
    }
    
    public function getOutpatientById($id)
    {
        $sql = "SELECT * FROM outpatient_findings WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching outpatient record: " . $e->getMessage());
            throw new Exception("Error fetching outpatient record.");
        }
    }

    public function update($id, $location, $quality, $severity, $duration, $blood_pressure, $temperature, $oxygen_saturation)
    {
        $sql = "UPDATE outpatient_findings 
                SET location = :location, 
                    quality = :quality, 
                    severity = :severity, 
                    duration = :duration, 
                    blood_pressure = :blood_pressure, 
                    temperature = :temperature, 
                    oxygen_saturation = :oxygen_saturation
                WHERE id = :id";
    
        $statement = $this->db->prepare($sql);
    
        // Bind parameters
        $statement->bindParam(':id', $id);
        $statement->bindParam(':location', $location);
        $statement->bindParam(':quality', $quality);
        $statement->bindParam(':severity', $severity);
        $statement->bindParam(':duration', $duration);
        $statement->bindParam(':blood_pressure', $blood_pressure);
        $statement->bindParam(':temperature', $temperature);
        $statement->bindParam(':oxygen_saturation', $oxygen_saturation);
    
        try {
            return $statement->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Error updating Outpatient record: " . $e->getMessage());
        }
    }
    
}
