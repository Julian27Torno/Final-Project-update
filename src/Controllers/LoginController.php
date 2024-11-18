<?php 

namespace App\Controllers;

use App\Models\User;
use App\Models\Patients; 

class LoginController extends BaseController
{
    public function showForm() {
        // Show the login form without errors
        return $this->render('login-form');
    }

    public function login() {
        // Initialize an array to store validation errors
        $errors = [];
        
        // Start session
        session_start();

        // Initialize login attempts if not set
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }

        try {
            // Retrieve form data
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Required field check
            if (empty($username) || empty($password)) {
                $errors[] = "Both fields are required.";
            }

            // Check for errors before processing
            if (!empty($errors)) {
                return $this->render('login-form', ['errors' => $errors, 'disabled' => $_SESSION['login_attempts'] >= 3]);
            }

            // Validate credentials
            $user = new User();
            $hashedPassword = $user->getPassword($username); // Retrieve the hashed password

            if ($hashedPassword && password_verify($password, $hashedPassword)) {
                // Successful login
                $_SESSION['is_logged_in'] = true; // Set login state
                $_SESSION['user_id'] = $username; // Set user identifier
                $_SESSION['login_attempts'] = 0; // Reset login attempts

                // Redirect to welcome page
                header("Location: /dashboard");
                exit();
            } else {
                $_SESSION['login_attempts']++;
                $errors[] = "Invalid username or password.";

                if ($_SESSION['login_attempts'] >= 3) {
                    $errors[] = "Too many failed login attempts. The form is now disabled.";
                }

                return $this->render('login-form', ['errors' => $errors, 'disabled' => $_SESSION['login_attempts'] >= 3]);
            }
        } catch (Exception $e) {
            $errors[] = "An unexpected error occurred: " . $e->getMessage();
            return $this->render('login-form', ['errors' => $errors, 'disabled' => $_SESSION['login_attempts'] >= 3]);
        }
    }

    public function showDashboard()
    {
        $patientsModel = new Patients();
    
        // Fetch total patients
        $totalPatients = count($patientsModel->getAll());
    
        // Fetch new patients added today
        $newPatientsToday = $patientsModel->getNewPatientsToday(); // Method to implement
    
        // Fetch recent patients (e.g., last 5)
        $recentPatients = array_slice($patientsModel->getAll(), -5);
    
        // Prepare data for patient trends (e.g., last 7 days)
        $trendDates = [];
        $trendValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $trendDates[] = $date;
            $trendValues[] = $patientsModel->getCountByDate($date); // Implement this method in your model
        }
    
        // Pass data to the Mustache view
        echo $this->render('dashboard', [
            'total_patients' => $totalPatients,
            'new_patients_today' => $newPatientsToday,
            'recent_patients' => $recentPatients,
            'trend_dates' => json_encode($trendDates),
            'trend_values' => json_encode($trendValues),
        ]);
    }
    

    public function logout() {
        session_start();

        // Destroy session variables and the session itself
        session_unset();
        session_destroy();

        // Redirect to the login form
        header("Location: /login-form");
        exit();
    }
}

