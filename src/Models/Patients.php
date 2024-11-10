<?php

namespace App\Models;

use App\Models\BaseModel;
use \PDO;

class Patients extends BaseModel
{
    // Save a new patient record
    public function save($case_no, $last_name, $first_name, $middle_name, $gender, $age, $contact_no)
    {
        $sql = "INSERT INTO patients (case_no, last_name, first_name, middle_name, gender, age, contact_no, date_added)
                VALUES (:case_no, :last_name, :first_name, :middle_name, :gender, :age, :contact_no, CURRENT_DATE)";
        
        $statement = $this->db->prepare($sql);

        // Bind parameters
        $statement->bindParam(':case_no', $case_no);
        $statement->bindParam(':last_name', $last_name);
        $statement->bindParam(':first_name', $first_name);
        $statement->bindParam(':middle_name', $middle_name);
        $statement->bindParam(':gender', $gender);
        $statement->bindParam(':age', $age, PDO::PARAM_INT);
        $statement->bindParam(':contact_no', $contact_no);

        // Execute the statement
        try {
            $statement->execute();
            return $statement->rowCount(); // Return the number of affected rows
        } catch (\PDOException $e) {
            throw new \Exception("Error saving patient record: " . $e->getMessage());
        }
    }

    // Get all patient records
    public function getAllPatients()
    {
        $sql = "SELECT case_no, last_name, first_name, middle_name, gender, age, contact_no, date_added FROM patients";
        $statement = $this->db->prepare($sql);

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC); // Return data as an associative array
    }

    // Retrieve a single patient's data based on case number
    public function getPatient($case_no)
    {
        $sql = "SELECT * FROM patients WHERE case_no = :case_no";
        $statement = $this->db->prepare($sql);

        $statement->execute([':case_no' => $case_no]);
        return $statement->fetch(PDO::FETCH_ASSOC); // Return a single record
    }

    // Update an existing patient record
    public function update($case_no, $last_name, $first_name, $middle_name, $gender, $age, $contact_no)
    {
        $sql = "UPDATE patients 
                SET last_name = :last_name, first_name = :first_name, middle_name = :middle_name, 
                    gender = :gender, age = :age, contact_no = :contact_no 
                WHERE case_no = :case_no";

        $statement = $this->db->prepare($sql);

        // Bind parameters
        $statement->bindParam(':case_no', $case_no);
        $statement->bindParam(':last_name', $last_name);
        $statement->bindParam(':first_name', $first_name);
        $statement->bindParam(':middle_name', $middle_name);
        $statement->bindParam(':gender', $gender);
        $statement->bindParam(':age', $age, PDO::PARAM_INT);
        $statement->bindParam(':contact_no', $contact_no);

        // Execute the statement
        try {
            $statement->execute();
            return $statement->rowCount(); // Return the number of affected rows
        } catch (\PDOException $e) {
            throw new \Exception("Error updating patient record: " . $e->getMessage());
        }
    }

    // Delete a patient record
    public function delete($case_no)
    {
        $sql = "DELETE FROM patients WHERE case_no = :case_no";
        $statement = $this->db->prepare($sql);

        $statement->bindParam(':case_no', $case_no);

        try {
            $statement->execute();
            return $statement->rowCount(); // Return the number of affected rows
        } catch (\PDOException $e) {
            throw new \Exception("Error deleting patient record: " . $e->getMessage());
        }
    }
}
