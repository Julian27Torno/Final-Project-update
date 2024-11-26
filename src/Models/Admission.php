<?php

namespace App\Models;

use App\Models\BaseModel;
use PDO;

class Admission extends BaseModel
{
    // Save admission data to the database
    public function save($data)
    {
        $sql = "INSERT INTO admission_records (
                    case_number, date_admitted, reason, room_number, attending_physician
                ) VALUES (
                    :case_number, :date_admitted, :reason, :room_number, :attending_physician
                )";

        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }

        try {
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Error saving admission record: " . $e->getMessage());
        }
    }

    // Retrieve all admitted patients
    public function getActiveAdmissions()
{
    $sql = "SELECT 
                a.case_number, 
                a.date_admitted, 
                a.reason, 
                a.room_number, 
                a.attending_physician,
                d.first_name AS doctor_first_name,
                d.last_name AS doctor_last_name
            FROM admission_records a
            LEFT JOIN doctors d ON a.attending_physician = d.doctor_id
            WHERE a.status = 'admitted'
            ORDER BY a.date_admitted DESC";

    $statement = $this->db->prepare($sql);
    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

    public function getAvailableRooms()
{
    $sql = "SELECT room_number FROM rooms WHERE status = 'available'";
    $statement = $this->db->prepare($sql);
    try {
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        throw new \Exception("Error fetching available rooms: " . $e->getMessage());
    }
}





public function updateRoomStatus($roomNumber, $status)
{
    $sql = "UPDATE rooms SET status = :status WHERE room_number = :room_number";
    $statement = $this->db->prepare($sql);

    try {
        $statement->bindParam(':status', $status);
        $statement->bindParam(':room_number', $roomNumber);
        $statement->execute();
    } catch (\PDOException $e) {
        throw new \Exception("Error updating room status: " . $e->getMessage());
    }
}

public function markRoomAsUnavailable($roomNumber)
{
    $sql = "UPDATE rooms SET status = 'unavailable' WHERE room_number = :room_number";
    $statement = $this->db->prepare($sql);

    try {
        $statement->bindParam(':room_number', $roomNumber, PDO::PARAM_INT);
        $statement->execute();
    } catch (\PDOException $e) {
        throw new \Exception("Error marking room as unavailable: " . $e->getMessage());
    }
}



public function getRoomByCaseNumber($case_number)
{
    $sql = "SELECT room_number FROM admission_records WHERE case_number = :case_number";
    $statement = $this->db->prepare($sql);
    try {
        $statement->bindParam(':case_number', $case_number, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['room_number'] ?? null;
    } catch (\PDOException $e) {
        throw new \Exception("Error fetching room for case number {$case_number}: " . $e->getMessage());
    }
}


public function removeByCaseNumber($case_number)
{
    $sql = "UPDATE admission_records 
            SET status = 'discharged', date_discharged = CURDATE() 
            WHERE case_number = :case_number";

    $statement = $this->db->prepare($sql);
    try {
        $statement->bindParam(':case_number', $case_number, PDO::PARAM_STR);
        $statement->execute();
    } catch (\PDOException $e) {
        throw new \Exception("Error updating discharge record for case number {$case_number}: " . $e->getMessage());
    }
}




public function markRoomAsAvailable($room_number)
{
    $sql = "UPDATE rooms SET status = 'available' WHERE room_number = :room_number";
    $statement = $this->db->prepare($sql);
    try {
        $statement->bindParam(':room_number', $room_number, PDO::PARAM_INT);
        $statement->execute();
    } catch (\PDOException $e) {
        throw new \Exception("Error marking room as available: " . $e->getMessage());
    }
}

public function search($query)
{
    $sql = "SELECT 
                a.case_number, 
                a.date_admitted, 
                a.reason, 
                a.room_number, 
                d.first_name AS doctor_first_name, 
                d.last_name AS doctor_last_name 
            FROM admission_records a
            LEFT JOIN doctors d ON a.attending_physician = d.doctor_id
            WHERE 
                a.case_number LIKE :query OR 
                CONCAT(d.first_name, ' ', d.last_name) LIKE :query";

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

public function getPastAdmissions($searchQuery = '')
{
    $sql = "SELECT 
                a.case_number, 
                a.room_number, 
                a.date_admitted, 
                a.date_discharged, 
                d.first_name AS doctor_first_name,
                d.last_name AS doctor_last_name
            FROM admission_records a
            LEFT JOIN doctors d ON a.attending_physician = d.doctor_id
            WHERE a.status = 'discharged'";

    // Append search condition if a query exists
    if (!empty($searchQuery)) {
        $sql .= " AND (
                    a.case_number LIKE :searchQuery OR 
                    CONCAT(d.first_name, ' ', d.last_name) LIKE :searchQuery
                  )";
    }

    $sql .= " ORDER BY a.date_discharged DESC";

    $statement = $this->db->prepare($sql);

    if (!empty($searchQuery)) {
        $searchTerm = '%' . $searchQuery . '%';
        $statement->bindParam(':searchQuery', $searchTerm, PDO::PARAM_STR);
    }

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}



}
