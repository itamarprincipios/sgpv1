<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/School.php';

class AdminController extends Controller {
    
    public function dashboard() {
        checkAuth('admin');
        
        $userModel = new User();
        $schoolModel = new School();
        
        // Stats for cards
        $stats = [
            'semed' => count($userModel->getByRole('semed')),
            'coordinators' => count($userModel->getByRole('coordinator')),
            'directors' => count($userModel->getByRole('director')),
            'professors' => count($userModel->getByRole('professor')),
            'schools' => count($schoolModel->all())
        ];
        
        // Only SEMED users needed for main dashboard now
        $semedUsers = $userModel->getByRole('semed');
        $schools = $schoolModel->getAvailableSchools(); // Only show unassigned schools for new registration
        
        $this->view('admin/dashboard', [
            'stats' => $stats,
            'semedUsers' => $semedUsers,
            'schools' => $schools
        ]);
    }

    public function settings() {
        checkAuth('admin');
        $this->view('admin/settings');
    }

    public function schools() {
        checkAuth(['admin', 'semed']); // Allow SEMED to view all schools
        $schoolModel = new School();
        $schools = $schoolModel->all();
        $this->view('admin/schools', ['schools' => $schools]);
    }

    public function coordinators() {
        checkAuth('admin');
        $userModel = new User();
        $coordinators = $userModel->getByRole('coordinator');
        $this->view('admin/coordinators', ['coordinators' => $coordinators]);
    }

    public function directors() {
        checkAuth('admin');
        $userModel = new User();
        $directors = $userModel->getByRole('director');
        $this->view('admin/directors', ['directors' => $directors]);
    }

    public function semedUsers() {
        checkAuth(['admin', 'semed']); // Allow SEMED to view/manage SEMED users (self-management or others)
        $userModel = new User();
        $schoolModel = new School();
        
        $semedUsers = $userModel->getByRole('semed');
        $schools = $schoolModel->getAvailableSchools(); // For the registration form
        
        $this->view('admin/semed_users', [
            'semedUsers' => $semedUsers, 
            'schools' => $schools
        ]);
    }

    public function professors() {
        checkAuth(['admin', 'semed']); // Allow SEMED to view all professors
        $userModel = new User();
        $schoolModel = new School();
        
        // Filters
        $filters = [
            'school_id' => $_GET['school_id'] ?? null,
            'class_id' => $_GET['class_id'] ?? null,
            'function' => $_GET['function'] ?? null,
            'search' => $_GET['search'] ?? null
        ];
        
        // Data for dropdowns
        $schools = $schoolModel->all();
        $classes = [];
        
        if ($filters['school_id']) {
            require_once __DIR__ . '/../Models/ClassModel.php';
            $classModel = new ClassModel();
            $classes = $classModel->getBySchoolId($filters['school_id']);
        }
        
        // Use new method with filters
        $professors = $userModel->getProfessorsWithFilters($filters);
        
        $this->view('admin/professors', [
            'professors' => $professors,
            'schools' => $schools,
            'classes' => $classes,
            'filters' => $filters
        ]);
    }
    
    public function storeUser() {
        checkAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $data = $_POST;
            
            // Password handling
            if (empty($data['password'])) {
                $data['password'] = '123456'; // Default
            }
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Extract schools
            $schools = $data['schools'] ?? [];
            unset($data['schools']);

            // Create user (returns PDOStatement, need ID. But model returns bool/stmt. 
            // Standard PDO insert doesn't return ID easily unless we use lastInsertId on connection.
            // Assuming User model extends Model which has access to db.
            // Let's modify User::create to return ID or handle it here.
            // Given current Model::create usually returns bool/stmt, we might need to find by email to get ID or fix Model.
            // Checking User::create... it returns $stmt.
            $userModel->create($data);
            
            // Hacky way to get ID if create doesn't return it: fetch by email
            $newUser = $userModel->findByEmail($data['email']);
            if ($newUser && !empty($schools)) {
                $userModel->assignSchools($newUser['id'], $schools);
            }

            $_SESSION['success'] = "Usuário criado com sucesso!";
        }
        redirect('admin/dashboard');
    }
    
    public function editUser() {
        checkAuth('admin');
        $id = $_GET['id'] ?? null;
        if (!$id) redirect('admin/dashboard');
        
        $userModel = new User();
        $user = $userModel->findById($id);
        if (!$user) redirect('admin/dashboard');
        
        $schoolModel = new School();
        // Get all schools available for assignment + schools ALREADY assigned to this user
        $availableSchools = $schoolModel->getAvailableSchools($id);
        
        // Get currently assigned school IDs for pre-selection
        $assignedSchoolIds = $userModel->getAssignedSchoolIds($id);
        
        $this->view('admin/user_edit', [
            'user' => $user,
            'schools' => $availableSchools,
            'assignedSchoolIds' => $assignedSchoolIds
        ]);
    }
    
    public function updateUser() {
        checkAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $id = $_POST['id'];
            $data = $_POST;
            
            // Schools
            $schools = $data['schools'] ?? [];
            unset($data['schools']);

            // Do not update password here unless specific route/logic, mostly just profile info
            unset($data['password']); 
            unset($data['id']);
            
            $userModel->update($id, $data);
            
            // Always update assignments if it's a SEMED user (or others if we expand)
            // Ideally check role, but assigning empty array clears it which is fine.
            $userModel->assignSchools($id, $schools);

            $_SESSION['success'] = "Usuário atualizado com sucesso!";
        }
        redirect('admin/dashboard');
    }
    
    public function deleteUser() {
        checkAuth('admin');
        $id = $_GET['id'] ?? null;
        $redirect = 'admin/dashboard';
        
        if ($id) {
            $userModel = new User();
            $user = $userModel->findById($id);
            if ($user) {
                if ($user['role'] == 'coordinator') $redirect = 'admin/coordinators';
                elseif ($user['role'] == 'professor') $redirect = 'admin/professors';
                
                $userModel->delete($id);
                $_SESSION['success'] = "Usuário excluído com sucesso!";
            }
        }
        redirect($redirect);
    }
    
    public function resetPassword() {
        checkAuth('admin');
        $id = $_GET['id'] ?? null;
        $redirect = 'admin/dashboard';
        
        if ($id) {
            $userModel = new User();
            $user = $userModel->findById($id);
            if ($user) {
                if ($user['role'] == 'coordinator') $redirect = 'admin/coordinators';
                elseif ($user['role'] == 'professor') $redirect = 'admin/professors';
                
                $userModel->update($id, ['password' => password_hash('123456', PASSWORD_DEFAULT)]);
                $_SESSION['success'] = "Senha resetada para '123456' com sucesso!";
            }
        }
        redirect($redirect);
    }
    
    // --- School Management (mirrored capability) ---
    // --- School Management (mirrored capability) ---
    public function storeSchool() {
        checkAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                error_log("School creation attempt - Data: " . json_encode($_POST));
                
                $schoolModel = new School();
                $result = $schoolModel->create($_POST);
                
                error_log("School creation result: " . ($result ? "SUCCESS" : "FAILED"));
                
                if ($result) {
                    $_SESSION['success'] = "Escola criada com sucesso!";
                } else {
                    $_SESSION['error'] = "Erro ao criar escola. Verifique os dados e tente novamente.";
                    error_log("School creation returned false");
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Erro de banco de dados: " . $e->getMessage();
                error_log("School creation PDO error: " . $e->getMessage());
            } catch (Exception $e) {
                $_SESSION['error'] = "Erro ao criar escola: " . $e->getMessage();
                error_log("School creation error: " . $e->getMessage());
            }
        }
        redirect('admin/schools');
    }

    public function editSchool() {
        checkAuth('admin');
        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('admin/schools');
        }
        
        $schoolModel = new School();
        $school = $schoolModel->findById($id);
        
        if (!$school) {
            $_SESSION['error'] = "Escola não encontrada.";
            redirect('admin/schools');
        }
        
        $this->view('admin/school_edit', ['school' => $school]);
    }

    public function updateSchool() {
        checkAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $schoolModel = new School();
            $schoolModel->update($id, $_POST);
            $_SESSION['success'] = "Escola atualizada com sucesso!";
        }
        redirect('admin/schools');
    }
    
    public function deleteSchool() {
        checkAuth('admin');
        $id = $_GET['id'] ?? null;
        if ($id) {
            $schoolModel = new School();
            $schoolModel->delete($id);
             $_SESSION['success'] = "Escola excluída com sucesso!";
        }
        redirect('admin/schools');
    }

    public function reports() {
        checkAuth(['admin', 'semed']); // Allow SEMED to view global reports
        
        $type = $_GET['type'] ?? 'general';
        $id = $_GET['id'] ?? null;
        
        $userModel = new User();
        $schoolModel = new School();
        
        $reportData = [];
        $title = "Relatórios de Gestão";
        
        if ($type === 'school' && $id) {
            $school = $schoolModel->findById($id);
            if ($school) {
                $title = "Relatório: " . $school['name'];
                $reportData['school'] = $school;
                
                // Try to find the director user
                $directorUser = $userModel->getByRole('director'); 
                // Filter manually or query DB. getByRole returns array. 
                // Faster: Query directly.
                $directorUser = null;
                // We don't have a direct method for this in User model yet, let's just use raw query here or add method.
                // Or loop through all directors (not efficient but works for MVP if few users).
                // Better: simple query.
                $db = $userModel->getDb(); // Hack access or use a new method?
                // Let's add a quick findDirectorBySchool method or just query here if simple.
                // Actually, User::getByRole returns all directors. We can filter.
                
                // Let's rely on adding a method to School or User model to keep controller clean?
                // Or just use getByRole filtered.
                $allDirectors = $userModel->getByRole('director');
                foreach($allDirectors as $d) {
                    // Check logic: director linked to school via pivot or school_id?
                    // Usually director is 1-to-1 with school via school_id in users table or director_name in schools.
                    // Implementation of Director User linking is: school_id column in users table (mostly).
                    if (($d['school_id'] == $id) || (isset($d['school_ids_raw']) && in_array($id, explode(',', $d['school_ids_raw'])))) {
                         $directorUser = $d;
                         break;
                    }
                }
                $reportData['director_user'] = $directorUser;

                $reportData['semed_users'] = $schoolModel->getSemedUsers($id);
                $reportData['coordinators'] = $schoolModel->getCoordinators($id);
                // $reportData['professors'] = $schoolModel->getProfessors($id); // REMOVED per user request
            }
        } elseif ($type === 'semed_user' && $id) {
            $user = $userModel->findById($id);
            if ($user) {
                $title = "Portfólio: " . $user['name'];
                $reportData['user'] = $user;
                $reportData['schools'] = $userModel->getManagedSchools($id);
            }
        } elseif ($type === 'general') {
            // General Lists
            $reportData['schools'] = $schoolModel->all(); 
            foreach ($reportData['schools'] as &$s) {
                $s['managers'] = $schoolModel->getSemedUsers($s['id']);
            }
            
            $reportData['semed_users'] = $userModel->getByRole('semed');
            foreach ($reportData['semed_users'] as &$u) {
                $u['school_count'] = count($userModel->getAssignedSchoolIds($u['id']));
            }
        }
        
        $allSchools = $schoolModel->all();
        $allSemedUsers = $userModel->getByRole('semed');
        
        $this->view('admin/reports', [
            'type' => $type,
            'reportData' => $reportData,
            'title' => $title,
            'allSchools' => $allSchools,
            'allSemedUsers' => $allSemedUsers,
            'selectedId' => $id
        ]);
    }

    public function resetSchoolYear() {
        checkAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Logic to reset school year:
            // 1. Unlink all professors from their classes (set class_id = NULL)
            // 2. Keep documents (they are linked to periods and users, so history is preserved)
            // 3. Keep periods (they have dates, so they become "past")
            
            $db = (new User())->getDb();
            
            try {
                $db->beginTransaction();
                
                // Update all users with role 'professor' to have class_id = NULL
                $sql = "UPDATE users SET class_id = NULL WHERE role = 'professor'";
                $db->query($sql);
                
                $db->commit();
                $_SESSION['success'] = "Ano letivo reiniciado com sucesso! Todos os professores foram desvinculados de suas turmas.";
            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['error'] = "Erro ao reiniciar ano letivo: " . $e->getMessage();
            }
        }
        redirect('admin/dashboard');
    }

    // --- Supervisora Ed. Física Management ---
    public function supervisorEdFis() {
        checkAuth('admin');
        $userModel = new User();
        $supervisors = $userModel->getByRole('supervisor_edfis');
        $this->view('admin/supervisor_edfis', ['supervisors' => $supervisors]);
    }

    public function storeSupervisorEdFis() {
        checkAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $data = $_POST;
            
            // Forçar role supervisor_edfis
            $data['role'] = 'supervisor_edfis';
            
            // Password padrão
            if (empty($data['password'])) {
                $data['password'] = 'supervisor123';
            }
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Supervisora não tem escola específica (vê todas)
            $data['school_id'] = null;
            
            $userModel->create($data);
            $_SESSION['success'] = "Supervisora de Educação Física cadastrada com sucesso!";
        }
        redirect('admin/supervisor-edfis');
    }

    public function editSupervisorEdFis() {
        checkAuth('admin');
        $id = $_GET['id'] ?? null;
        if (!$id) redirect('admin/supervisor-edfis');
        
        $userModel = new User();
        $supervisor = $userModel->findById($id);
        
        if (!$supervisor || $supervisor['role'] !== 'supervisor_edfis') {
            $_SESSION['error'] = "Supervisora não encontrada.";
            redirect('admin/supervisor-edfis');
        }
        
        $this->view('admin/supervisor_edfis_edit', ['supervisor' => $supervisor]);
    }

    public function updateSupervisorEdFis() {
        checkAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $id = $_POST['id'];
            $data = $_POST;
            
            unset($data['password']); // Não atualizar senha aqui
            unset($data['id']);
            unset($data['role']); // Não permitir mudar role
            
            $userModel->update($id, $data);
            $_SESSION['success'] = "Supervisora atualizada com sucesso!";
        }
        redirect('admin/supervisor-edfis');
    }

    public function deleteSupervisorEdFis() {
        checkAuth('admin');
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $userModel = new User();
            $supervisor = $userModel->findById($id);
            
            if ($supervisor && $supervisor['role'] === 'supervisor_edfis') {
                $userModel->delete($id);
                $_SESSION['success'] = "Supervisora excluída com sucesso!";
            }
        }
        redirect('admin/supervisor-edfis');
    }

    public function resetSupervisorPassword() {
        checkAuth('admin');
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $userModel = new User();
            $supervisor = $userModel->findById($id);
            
            if ($supervisor && $supervisor['role'] === 'supervisor_edfis') {
                $userModel->update($id, ['password' => password_hash('supervisor123', PASSWORD_DEFAULT)]);
                $_SESSION['success'] = "Senha resetada para 'supervisor123' com sucesso!";
            }
        }
        redirect('admin/supervisor-edfis');
    }
}
