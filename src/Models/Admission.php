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
    public function getAllAdmissions()
    {
        $sql = "SELECT 
                    case_number, 
                    date_admitted, 
                    reason, 
                    room_number, 
                    attending_physician 
                FROM admission_records";

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
    $sql = "DELETE FROM admission_records WHERE case_number = :case_number";
    $statement = $this->db->prepare($sql);
    try {
        $statement->bindParam(':case_number', $case_number, PDO::PARAM_STR);
        $statement->execute();
    } catch (\PDOException $e) {
        throw new \Exception("Error removing admission record for case number {$case_number}: " . $e->getMessage());
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




}
