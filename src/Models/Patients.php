<?php

namespace App\Models;

use App\Models\BaseModel;
use \PDO;

class Patients extends BaseModel
{
    // Insert a new patient record into the database
    public function insert($data)
    {
        $sql = "INSERT INTO patients (last_name, first_name, middle_name, address, age, birthday, birthplace, civil_status, gender, contact_no, religion, occupation, date_added)
                VALUES (:lastname, :firstname, :middleinitial, :address, :age, :birthday, :birthplace, :civil_status, :gender, :mobile, :religion, :occupation, NOW())";

        $statement = $this->db->prepare($sql);

        // Bind parameters
        $statement->bindParam(':lastname', $data['lastname']);
        $statement->bindParam(':firstname', $data['firstname']);
        $statement->bindParam(':middleinitial', $data['middleinitial']);
        $statement->bindParam(':address', $data['address']);
        $statement->bindParam(':age', $data['age']);
        $statement->bindParam(':birthday', $data['birthday']);
        $statement->bindParam(':birthplace', $data['birthplace']);
        $statement->bindParam(':civil_status', $data['civil_status']);
        $statement->bindParam(':gender', $data['gender']);
        $statement->bindParam(':mobile', $data['mobile']);
        $statement->bindParam(':religion', $data['religion']);
        $statement->bindParam(':occupation', $data['occupation']);

        try {
            $statement->execute();
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception("Error saving patient record: " . $e->getMessage());
        }
    }

    public function getCountByDate($date)
{
    $sql = "SELECT COUNT(*) AS count FROM patients WHERE DATE(date_added) = :date"; // Filter by specific date
    $statement = $this->db->prepare($sql);

    try {
        $statement->bindParam(':date', $date); // Bind the date parameter
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC); // Fetch result as associative array
        return $result['count']; // Return the count of patients
    } catch (\PDOException $e) {
        throw new \Exception("Error fetching patient count for date {$date}: " . $e->getMessage());
    }
}


    public function getNewPatientsToday()
{
    $sql = "SELECT COUNT(*) AS count FROM patients WHERE DATE(date_added) = CURDATE()"; // Fetch patients added today
    $statement = $this->db->prepare($sql);

    try {
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC); // Fetch result as associative array
        return $result['count']; // Return the count of new patients
    } catch (\PDOException $e) {
        throw new \Exception("Error fetching today's patients: " . $e->getMessage());
    }
}

public function update($case_no, $last_name, $first_name, $middle_name, $age, $gender, $contact_no)
{
    $sql = "UPDATE patients 
            SET last_name = :last_name, 
                first_name = :first_name, 
                middle_name = :middle_name, 
                age = :age, 
                gender = :gender, 
                contact_no = :contact_no
            WHERE case_no = :case_no";

    $statement = $this->db->prepare($sql);

    // Bind parameters
    $statement->bindParam(':case_no', $case_no);
    $statement->bindParam(':last_name', $last_name);
    $statement->bindParam(':first_name', $first_name);
    $statement->bindParam(':middle_name', $middle_name);
    $statement->bindParam(':age', $age);
    $statement->bindParam(':gender', $gender);
    $statement->bindParam(':contact_no', $contact_no);

    try {
        return $statement->execute();
    } catch (\PDOException $e) {
        throw new \Exception("Error updating patient record: " . $e->getMessage());
    }
}

public function find($case_no)
{
    $sql = "SELECT * FROM patients WHERE case_no = :case_no"; // Query to fetch a patient by case number
    $statement = $this->db->prepare($sql);

    try {
        $statement->bindParam(':case_no', $case_no); // Bind the case number
        $statement->execute();
        return $statement->fetchObject(); // Fetch as an object for easy access
    } catch (\PDOException $e) {
        throw new \Exception("Error finding patient record: " . $e->getMessage());
    }
}

public function search($query)
{
    $sql = "SELECT * FROM patients 
            WHERE case_no LIKE :query 
               OR last_name LIKE :query 
               OR first_name LIKE :query";

    $statement = $this->db->prepare($sql);
    $likeQuery = '%' . $query . '%';

    try {
        $statement->bindParam(':query', $likeQuery, PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        throw new \Exception("Error searching patients: " . $e->getMessage());
    }
}


    // Fetch all patient records from the database
    public function getAll()
    {
        $sql = "SELECT case_no, last_name, first_name, middle_name, gender, age, contact_no, date_added FROM patients";
        $statement = $this->db->prepare($sql);
        
        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC); // Return as associative array for Mustache compatibility
        } catch (\PDOException $e) {
            throw new \Exception("Error fetching patient records: " . $e->getMessage());
        }
    }
}
