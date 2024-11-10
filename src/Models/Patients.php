<?php

namespace App\Models;

use App\Models\BaseModel;
use \PDO;

class Patients extends BaseModel
{
    // Properties
    public $case_no;
    public $last_name;
    public $first_name;
    public $middle_name;
    public $gender;
    public $age;
    public $contact_no;
    public $date_added;

    // Fetch all patients
    public function all()
    {
        $sql = "SELECT case_no, last_name, first_name, middle_name, gender, age, contact_no, date_added FROM patients";
        $statement = $this->db->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_CLASS, '\App\Models\Patients');
        return $result;
    }

    // Find a patient by case number
    public function find($case_no)
    {
        $sql = "SELECT * FROM patients WHERE case_no = :case_no";
        $statement = $this->db->prepare($sql);
        $statement->bindParam(':case_no', $case_no);
        $statement->execute();
        return $statement->fetchObject('\App\Models\Patients');
    }

    // Getters
    public function getCaseNo()
    {
        return $this->case_no;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

    public function getFirstName()
    {
        return $this->first_name;
    }

    public function getMiddleName()
    {
        return $this->middle_name;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function getContactNo()
    {
        return $this->contact_no;
    }

    public function getDateAdded()
    {
        return $this->date_added;
    }

    // Setters
    public function setCaseNo($case_no)
    {
        $this->case_no = $case_no;
    }

    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    public function setMiddleName($middle_name)
    {
        $this->middle_name = $middle_name;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    public function setAge($age)
    {
        $this->age = $age;
    }

    public function setContactNo($contact_no)
    {
        $this->contact_no = $contact_no;
    }

    public function setDateAdded($date_added)
    {
        $this->date_added = $date_added;
    }

    // Method to get full name
    public function getFullName()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    // Method to save a new patient record
    public function save($case_no, $last_name, $first_name, $middle_name, $gender, $age, $contact_no)
    {
        $sql = "INSERT INTO patients (case_no, last_name, first_name, middle_name, gender, age, contact_no, date_added)
                VALUES (:case_no, :last_name, :first_name, :middle_name, :gender, :age, :contact_no, NOW())";

        $statement = $this->db->prepare($sql);

        $statement->bindParam(':case_no', $case_no);
        $statement->bindParam(':last_name', $last_name);
        $statement->bindParam(':first_name', $first_name);
        $statement->bindParam(':middle_name', $middle_name);
        $statement->bindParam(':gender', $gender);
        $statement->bindParam(':age', $age);
        $statement->bindParam(':contact_no', $contact_no);

        try {
            $statement->execute();
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception("Error saving patient: " . $e->getMessage());
        }
    }

    // Method to update an existing patient record
    public function update($case_no, $last_name, $first_name, $middle_name, $gender, $age, $contact_no)
    {
        $sql = "UPDATE patients SET
                last_name = :last_name,
                first_name = :first_name,
                middle_name = :middle_name,
                gender = :gender,
                age = :age,
                contact_no = :contact_no
                WHERE case_no = :case_no";

        $statement = $this->db->prepare($sql);

        $statement->bindParam(':case_no', $case_no);
        $statement->bindParam(':last_name', $last_name);
        $statement->bindParam(':first_name', $first_name);
        $statement->bindParam(':middle_name', $middle_name);
        $statement->bindParam(':gender', $gender);
        $statement->bindParam(':age', $age);
        $statement->bindParam(':contact_no', $contact_no);

        try {
            $statement->execute();
            return $statement->rowCount();
        } catch (\PDOException $e) {
            throw new \Exception("Error updating patient: " . $e->getMessage());
        }
    }

    // Method to delete a patient record
    public function delete($case_no)
    {
        $sql = "DELETE FROM patients WHERE case_no = :case_no";
        $statement = $this->db->prepare($sql);
        $statement->bindParam(':case_no', $case_no);

        try {
            $statement->execute();
            return $statement->rowCount();
        } catch (\PDOException $e) {
            throw new \Exception("Error deleting patient: " . $e->getMessage());
        }
    }
}
