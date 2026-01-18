<?php

function url($path = '') {
    $config = require __DIR__ . '/../../config/config.php';
    return rtrim($config['app']['url'], '/') . '/' . ltrim($path, '/');
}

function redirect($path) {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    header("Location: " . url($path));
    exit;
}

function view($viewName, $data = []) {
    extract($data);
    $viewPath = __DIR__ . '/../Views/' . $viewName . '.php';
    
    if (file_exists($viewPath)) {
        require_once $viewPath;
    } else {
        die("View not found: $viewName");
    }
}

function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die;
}

function session($key, $default = null) {
    return $_SESSION[$key] ?? $default;
}

function auth() {
    return $_SESSION['user'] ?? null;
}

function log_debug($msg) {
    file_put_contents(__DIR__ . '/../../debug_auth.log', date('Y-m-d H:i:s') . " - " . $msg . "\n", FILE_APPEND);
}

function checkAuth($role = null) {
    if (!auth()) {
        log_debug("checkAuth: No auth, redirecting login");
        redirect('login');
    }

    $userRole = auth()['role'];
    log_debug("checkAuth: Requested " . json_encode($role) . ", User has '$userRole'");

    // Allow multiple roles
    if ($role) {
        if (!is_array($role)) {
            $role = [$role];
        }

        if (!in_array($userRole, $role)) {
            // Role Hierarchy Logic
            if (in_array('coordinator', $role) && $userRole === 'director') {
                return; // Director can access Coordinator areas
            }
            if (in_array('director', $role) && $userRole === 'admin') {
                 // Admin usually has own area, but if we wanted Admin to see Director view... 
                 // For now, only Director > Coordinator.
            }

            log_debug("checkAuth: Role mismatch. Redirecting based on user role '$userRole'.");
            if($userRole == 'semed') redirect('semed/dashboard');
            if($userRole == 'coordinator') redirect('school/dashboard'); 
            if($userRole == 'director') redirect('school/dashboard'); 
            if($userRole == 'professor') redirect('professor/dashboard');
            if($userRole == 'admin') redirect('admin/dashboard');
        }
    }
}
