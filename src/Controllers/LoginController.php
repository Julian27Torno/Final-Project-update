<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Patients;

class LoginController extends BaseController
{
    public function showForm()
    {
        // Ensure session is started for showing the login form (for error display)
        session_start();
        return $this->render('login-form');
    }

    public function login()
    {
        // Initialize session and validation errors
        session_start();
        $errors = [];

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

            // If validation fails, return to the login form with errors
            if (!empty($errors)) {
                return $this->render('login-form', [
                    'errors' => $errors,
                    'disabled' => $_SESSION['login_attempts'] >= 3, // Disable after 3 failed attempts
                ]);
            }

            // Validate credentials
            $userModel = new User();
            $user = $userModel->getUserByUsername($username); // Retrieve the user row by username

            // Check if user exists and password is correct
            if ($user && password_verify($password, $user['password_hash'])) {
                // Successful login, reset attempts and create session variables
                $_SESSION['is_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['login_attempts'] = 0; // Reset login attempts

                // Regenerate session ID to avoid session fixation attacks
                session_regenerate_id(true);

                // Redirect to the dashboard
                header("Location: /dashboard");
                exit();
            } else {
                // Invalid credentials, increment login attempts
                $_SESSION['login_attempts']++;
                $errors[] = "Invalid username or password.";

                // If there are too many failed attempts, disable the form
                if ($_SESSION['login_attempts'] >= 3) {
                    $errors[] = "Too many failed login attempts. The form is now disabled.";
                }

                return $this->render('login-form', [
                    'errors' => $errors,
                    'disabled' => $_SESSION['login_attempts'] >= 3,
                ]);
            }
        } catch (\Exception $e) {
            $errors[] = "An unexpected error occurred: " . $e->getMessage();
            return $this->render('login-form', [
                'errors' => $errors,
                'disabled' => $_SESSION['login_attempts'] >= 3,
            ]);
        }
    }

    public function showDashboard()
    {
        // Start session to verify login status
        session_start();

        // If user is not logged in, redirect to login
        if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
            header("Location: /dashboard");
            exit();
        }

        // Fetch patient data
        $patientsModel = new Patients();
        $totalPatients = count($patientsModel->getAll());
        $newPatientsToday = $patientsModel->getNewPatientsToday(); // Implement this method in your model
        $recentPatients = array_slice($patientsModel->getAll(), -5);
        
        // Patient trends for the last 7 days
        $trendDates = [];
        $trendValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $trendDates[] = $date;
            $trendValues[] = $patientsModel->getCountByDate($date); // Implement this method in your model
        }

        // Render the dashboard view with the necessary data
        echo $this->render('dashboard', [
            'username' => $_SESSION['username'] ?? 'Guest',
            'total_patients' => $totalPatients,
            'new_patients_today' => $newPatientsToday,
            'recent_patients' => $recentPatients,
            'trend_dates' => json_encode($trendDates),
            'trend_values' => json_encode($trendValues),
        ]);
    }

    public function logout()
    {
        session_start();

        // Destroy session variables and the session itself
        session_unset();
        session_destroy();

        // Redirect to the login form
        header("Location: /login-form");
        exit();
    }
}
