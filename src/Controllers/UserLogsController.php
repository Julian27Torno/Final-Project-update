<?php

namespace App\Controllers;

use App\Models\UserLogs;

class UserLogsController extends BaseController
{
    // Show the logs page
    public function index()
    {
        $userLogsModel = new UserLogs();
        $logs = $userLogsModel->getAllLogs();

        // Render the logs view
        echo $this->render('user-logs', ['logs' => $logs]);
    }

    // Add a new log entry
    public function logAction($userId, $action, $module, $details = null)
    {
        $userLogsModel = new UserLogs();

        $data = [
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'details' => $details
        ];

        $userLogsModel->addLog($data);
    }
}
