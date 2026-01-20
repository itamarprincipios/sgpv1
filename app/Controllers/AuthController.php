<?php

class AuthController extends Controller {
    public function login() {
        if (auth()) {
            $this->redirectBasedOnRole(auth()['role']);
        }
        $this->view('auth/login');
    }

    public function verify() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = $user;
                $this->redirectBasedOnRole($user['role']);
            } else {
                $_SESSION['error'] = "Credenciais inválidas.";
                redirect('login');
            }
        }
    }

    public function logout() {
        session_destroy();
        redirect('login');
    }

    private function redirectBasedOnRole($role) {
        file_put_contents(__DIR__ . '/../../public/debug_loop.log', date('Y-m-d H:i:s') . " - AuthController redirect user role: " . var_export($role, true) . "\n", FILE_APPEND);
        
        $role = strtolower(trim($role));
        
        switch ($role) {
            case 'semed':
                require_once __DIR__ . '/../Helpers/functions.php'; // Ensure helpers are loaded
                // Check direct property just in case helper behaves odd during login transition
                $user = auth() ?? $_SESSION['user'] ?? null;
                if ($user && !empty($user['is_admin_semed'])) {
                    redirect('adminsemed/dashboard');
                } else {
                    redirect('semed/dashboard');
                }
                break;
            case 'coordinator':
                redirect('school/dashboard');
                break;
            case 'director':
                redirect('school/dashboard');
                break;
            case 'professor':
                redirect('professor/dashboard');
                break;
            case 'admin':
            case 'administrador':
                redirect('admin/dashboard');
                break;
            case 'supervisor_edfis':
                redirect('supervisor-edfis/dashboard');
                break;
            case 'supervisor_monitor':
                redirect('supervisor-monitor/dashboard');
                break;
            default:
                file_put_contents(__DIR__ . '/../../public/debug_loop.log', date('Y-m-d H:i:s') . " - Role not matched, destroying session and redirecting to login.\n", FILE_APPEND);
                session_destroy(); // FORCE LOGOUT if role is weird
                redirect('login');
        }
    }
}
