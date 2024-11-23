<?php

namespace App\Models;

use App\Models\BaseModel;
use PDO;

class UserLogs extends BaseModel
{
    // Fetch all logs from the database
    public function getAllLogs()
    {
        $sql = "SELECT * FROM user_logs ORDER BY timestamp DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a new log entry
    public function addLog($data)
    {
        $sql = "INSERT INTO user_logs (user_id, action, module, details) VALUES (:user_id, :action, :module, :details)";
        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->execute();
    }
}
