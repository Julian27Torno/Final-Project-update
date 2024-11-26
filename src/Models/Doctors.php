<?php

namespace App\Models;

use App\Models\BaseModel;
use PDO;

class Doctors extends BaseModel
{
    public function getAll()
    {
        $sql = "SELECT * FROM doctors ORDER BY created_at DESC";
        $statement = $this->db->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

 

    public function find($doctor_id)
    {
        $sql = "SELECT * FROM doctors WHERE doctor_id = :doctor_id";
        $statement = $this->db->prepare($sql);
        $statement->bindParam(':doctor_id', $doctor_id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }
    

    public function save($data)
    {
        $sql = "INSERT INTO doctors (first_name, last_name, specialization, contact_no, email) 
                VALUES (:first_name, :last_name, :specialization, :contact_no, :email)";
        $statement = $this->db->prepare($sql);
        foreach ($data as $key => $value) {
            $statement->bindValue(":{$key}", $value);
        }
        $statement->execute();
    }

    public function delete($doctor_id)
    {
        $sql = "DELETE FROM doctors WHERE doctor_id = :doctor_id";
        $statement = $this->db->prepare($sql);
        $statement->bindParam(':doctor_id', $doctor_id);
        $statement->execute();
    }
}
