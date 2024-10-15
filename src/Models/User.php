<?php
namespace App\Models;

use PDO;

class User {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function register($username, $email, $firstName, $lastName, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, email, first_name, last_name, password) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$username, $email, $firstName, $lastName, $hashedPassword]);
    }

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return true;
        }
        return false;
    }
}
