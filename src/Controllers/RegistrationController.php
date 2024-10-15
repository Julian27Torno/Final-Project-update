<?php
namespace App\Controllers;

use App\Models\User;
use Exception;

class RegistrationController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function showRegistrationForm() {
        include 'path/to/views/registration-form.php'; // Adjust the path accordingly
    }

    public function register() {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $password = $_POST['password'];
        $passwordConfirmation = $_POST['password_confirmation'];

        // Validation
        $errors = $this->validateRegistration($username, $email, $password, $passwordConfirmation);

        if (!empty($errors)) {
            include 'path/to/views/registration-form.php'; // Show errors in the form
            return;
        }

        // Register user
        if ($this->userModel->register($username, $email, $firstName, $lastName, $password)) {
            echo "Successful Registration. <a href='/login-form'>Login here</a>";
        } else {
            echo "Registration failed.";
        }
    }

    private function validateRegistration($username, $email, $password, $passwordConfirmation) {
        $errors = [];

        if (empty($username) || empty($email) || empty($password) || empty($passwordConfirmation)) {
            $errors[] = "All fields are required.";
        }

        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters.";
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one numeric character.";
        }

        if (!preg_match('/[a-zA-Z]/', $password)) {
            $errors[] = "Password must contain at least one non-numeric character.";
        }

        if (!preg_match('/[!@#$%^&*()+\-]/', $password)) {
            $errors[] = "Password must contain at least one special character.";
        }

        if ($password !== $passwordConfirmation) {
            $errors[] = "Passwords do not match.";
        }

        return $errors;
    }
}
