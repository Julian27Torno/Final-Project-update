<?php 

namespace App\Controllers;

use App\Models\User;
use App\Controllers\UserLogsController; 

class RegistrationController extends BaseController
{
    public function showForm() {
        // Show the empty registration form with no errors
        return $this->render('users');
    }
    public function register()
    {
        $errors = [];
    
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
    
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirmation = $_POST['confirm_password'] ?? '';
    
            // Validate required fields
            if (empty($username) || empty($email) || empty($password) || empty($password_confirmation)) {
                $errors[] = "All required fields must be filled out.";
            }
    
            // Validate password
            if (strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters long.";
            }
            if (!preg_match('/[0-9]/', $password)) {
                $errors[] = "Password must contain at least one numeric character.";
            }
            if (!preg_match('/[a-zA-Z]/', $password)) {
                $errors[] = "Password must contain at least one non-numeric character.";
            }
            if (!preg_match('/[\W]/', $password)) {
                $errors[] = "Password must contain at least one special character (!@#$%^&*-+).";
            }
            if ($password !== $password_confirmation) {
                $errors[] = "Passwords do not match.";
            }
    
            // Check if there are validation errors
            if (!empty($errors)) {
                return $this->render('users', [
                    'errors' => $errors,
                    'username' => $username,
                    'email' => $email,
                    'first_name' => $first_name,
                    'last_name' => $last_name
                ]);
            }
    
            // Save user to the database
            $user = new User();
            $save_result = $user->save($username, $email, $first_name, $last_name, $password);
    
            if ($save_result) {
                // Log the action
                if (isset($_SESSION['user_id'])) {
                    $logsController = new UserLogsController();
                    $logResult = $logsController->logAction(
                        $_SESSION['user_id'],
                        'ADD',
                        'user',
                        "Added a new user with username: $username."
                    );
    
                    if (!$logResult) {
                        error_log("Failed to log action for user ID: {$_SESSION['user_id']}.");
                    }
                } else {
                    error_log("User ID not set in session. Cannot log action.");
                }
    
                // Redirect to success page
                return $this->render('users-success', [
                    'success' => 'User successfully registered!'
                ]);
            } else {
                throw new \Exception("Failed to save user to the database.");
            }
        } catch (\Exception $e) {
            error_log("Error during registration: " . $e->getMessage());
            return $this->render('users', [
                'errors' => ['An unexpected error occurred. Please try again later.']
            ]);
        }
    }
    
}