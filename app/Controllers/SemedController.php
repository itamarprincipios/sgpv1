<?php

require_once __DIR__ . '/../Models/Document.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/School.php';

class SemedController extends Controller {
    public function dashboard() {
        checkAuth('semed');
        $user = auth();
        
        // Get assigned schools
        $userModel = new User();
        $assignedSchoolIds = $userModel->getAssignedSchoolIds($user['id']);

        $docModel = new Document();
        $stats = $docModel->getGlobalStats($assignedSchoolIds);
        
        // Add directors count
        // Add directors count
        $stats['total_directors'] = $userModel->countDirectors($assignedSchoolIds);

        require_once __DIR__ . '/../Models/RankingModel.php';
        $rankingModel = new RankingModel();
        
        $filter = $_GET['filter'] ?? 'annual';
        $rankSchools = $rankingModel->getSchoolRanking($filter, null, $assignedSchoolIds);
        $rankProfessors = $rankingModel->getProfessorRanking($filter, null, $assignedSchoolIds, 'regular');
        $rankMonitors = $rankingModel->getProfessorRanking($filter, null, $assignedSchoolIds, 'monitor');
        $rankCoordinators = $rankingModel->getCoordinatorRanking($filter, null, $assignedSchoolIds);
        
        $chartData = $docModel->getDocumentStatsBySchool($assignedSchoolIds);
        $monthlyData = $docModel->getMonthlyStats($assignedSchoolIds);
        
        $this->view('dashboard/semed', [
            'user' => $user,
            'stats' => $stats,
            'rankSchools' => $rankSchools,
            'rankProfessors' => $rankProfessors,
            'rankMonitors' => $rankMonitors,
            'rankCoordinators' => $rankCoordinators,
            'chartData' => $chartData,
            'monthlyData' => $monthlyData,
            'filter' => $filter
        ]);
    }
    public function schools() {
        checkAuth('semed');
        $user = auth();
        $userModel = new User();
        // Only show schools managed by this user
        $schools = $userModel->getManagedSchools($user['id']);
        $this->view('dashboard/semed_schools', ['schools' => $schools]);
    }

    public function storeSchool() {
        // ... (storeSchool logic remains, but maybe should optionally auto-link to creator? 
        // For now, let's assume Admin creates schools, SEMED just views/edits if allowed. 
        // Original logic allowed create. Let's keep it but ideally restrict or link.)
        checkAuth('semed');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $schoolModel = new School();
            $schoolModel->create($_POST);
            // Ideally link to current user, but schema separate for now.
             redirect('semed/schools');
        }
        $this->view('dashboard/semed_school_form');
    }

    public function registrations() {
        checkAuth('semed');
        $user = auth();
        $userModel = new User();
        $docModel = new Document();
        
        $assignedSchoolIds = $userModel->getAssignedSchoolIds($user['id']);
        $stats = $docModel->getGlobalStats($assignedSchoolIds);
        
        // Add specific registration counts
        $stats['total_directors'] = $userModel->countDirectors($assignedSchoolIds);
        $stats['total_coordinators'] = $userModel->countCoordinators($assignedSchoolIds);
        $stats['total_semed'] = $userModel->countSemedUsers();
        
        $this->view('dashboard/registrations', ['stats' => $stats]);
    }

    public function allocation() {
        checkAuth('semed');
        $user = auth();
        $userModel = new User();
        $schoolModel = new School();
        
        require_once __DIR__ . '/../Models/ClassModel.php';
        $classModel = new ClassModel();
        
        // Get assigned schools for filtering
        $assignedSchoolIds = $userModel->getAssignedSchoolIds($user['id']);
        $schools = $schoolModel->all(); // For filter dropdown
        
        // Apply school filter if provided
        $schoolId = $_GET['school_id'] ?? null;
        
        // Get allocation data
        if ($schoolId) {
            $allocations = $classModel->getAllWithAllocation($schoolId);
        } else {
            $allocations = $classModel->getAllWithAllocation(null, $assignedSchoolIds);
        }
        
        // Count vacant classes for alert badge
        $vacantCount = $classModel->countVacantClasses($assignedSchoolIds);
        
        $this->view('dashboard/allocation', [
            'allocations' => $allocations,
            'schools' => $schools,
            'selectedSchoolId' => $schoolId,
            'vacantCount' => $vacantCount
        ]);
    }

    public function editSchool() {
        checkAuth('semed');
        $id = $_GET['id'] ?? null;
        $user = auth();
        $userModel = new User();
        $assignedIds = $userModel->getAssignedSchoolIds($user['id']);
        
        if (!in_array($id, $assignedIds)) {
             $_SESSION['error'] = "Acesso negado a esta escola.";
             redirect('semed/schools');
             return;
        }
        
        $schoolModel = new School();
        $school = $schoolModel->findById($id);
        $this->view('dashboard/semed_school_edit', ['school' => $school]);
    }

    public function updateSchool() {
        checkAuth('semed');
         // Check permission?
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
             // Verify ID is in assigned list to prevent IDOR
            $user = auth();
            $userModel = new User();
            $assignedIds = $userModel->getAssignedSchoolIds($user['id']);
            if (in_array($id, $assignedIds)) {
                $schoolModel = new School();
                $schoolModel->update($id, $_POST);
                $_SESSION['success'] = "Escola atualizada com sucesso!";
            }
        }
        redirect('semed/schools');
    }

    public function deleteSchool() {
        checkAuth('semed');
        $id = $_GET['id'] ?? null;
         // Verify ID matches assigned schools
        $user = auth();
        $userModel = new User();
        $assignedIds = $userModel->getAssignedSchoolIds($user['id']);
        
        if ($id && in_array($id, $assignedIds)) {
            $schoolModel = new School();
            // Check if there are users associated with this school
            $usersInSchool = $userModel->getBySchoolId($id);
            
            if (!empty($usersInSchool)) {
                $_SESSION['error'] = "Não é possível excluir esta escola pois existem usuários vinculados a ela.";
            } else {
                $schoolModel->delete($id);
                $_SESSION['success'] = "Escola excluída com sucesso!";
            }
        }
        redirect('semed/schools');
    }

    // --- COORDINATOR MANAGEMENT ---
    public function coordinators() {
        checkAuth('semed');
        $user = auth();
        $userModel = new User();
        
        // 1. Get Schools Managed by SEMED User
        $schools = $userModel->getManagedSchools($user['id']);
        $schoolIds = array_column($schools, 'id');
        
        // 2. Get Coordinators linked to these schools
        $coordinators = $userModel->getBySchoolIds($schoolIds, 'coordinator');
        
        $this->view('dashboard/semed_coordinators', [
            'coordinators' => $coordinators,
            'schools' => $schools
        ]);
    }

    public function storeCoordinator() {
        checkAuth('semed');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $data = $_POST;
            $data['role'] = 'coordinator';
            $data['password'] = password_hash('123456', PASSWORD_DEFAULT); // Default password
            $userModel->create($data);
            $_SESSION['success'] = "Coordenador cadastrado com sucesso! Senha padrão: 123456";
        }
        redirect('semed/coordinators');
    }

    public function editCoordinator() {
        checkAuth('semed');
        $id = $_GET['id'] ?? null;
        $user = auth();
        $userModel = new User();
        
        // Security check: Is this coordinator in one of my schools?
        // For now, let's assume if I can see them in the list (filtered), I can edit them.
        // But the School List dropdown MUST be filtered.
        
        $coordinator = $userModel->findById($id);
        $schools = $userModel->getManagedSchools($user['id']); // Only my schools
        
        $this->view('dashboard/semed_coordinator_edit', [
            'coordinator' => $coordinator,
            'schools' => $schools
        ]);
    }

    public function updateCoordinator() {
        checkAuth('semed');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $schoolId = $_POST['school_id'] ?? null;
            
            $userModel = new User();
            $userModel->update($id, $_POST);
            
            // Fix: REPLACE all school links instead of adding
            if ($schoolId) {
                $db = $userModel->getDb();
                
                // Step 1: Remove ALL existing school links for this coordinator
                $db->query("DELETE FROM user_schools WHERE user_id = :uid", [
                    'uid' => $id
                ]);
                
                // Step 2: Add the new school link
                $db->query("INSERT INTO user_schools (user_id, school_id) VALUES (:uid, :sid)", [
                    'uid' => $id,
                    'sid' => $schoolId
                ]);
            }
            
            $_SESSION['success'] = "Coordenador atualizado com sucesso!";
        }
        redirect('semed/coordinators');
    }

    public function linkSchoolToCoordinator() {
        checkAuth('semed');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'];
            $schoolId = $_POST['school_id'];
            
            $userModel = new User();
            
            // Check if already linked
            $existing = $userModel->getAssignedSchoolIds($userId);
            if (!in_array($schoolId, $existing)) {
                
                // If this is the FIRST school, we might want to also update the legacy 'school_id' column 
                // just to keep things consistent for single-school logic, OR we rely on aggregation.
                // For safety, let's just insert into pivot.
                
                // BUT, if user has school_id set, we should probs migrate that to pivot if it's not there.
                // Or just append.
                
                $db = $userModel->getDb(); // Assuming we can get DB instance or creating raw query via model
                $db->query("INSERT IGNORE INTO user_schools (user_id, school_id) VALUES (:uid, :sid)", [
                    'uid' => $userId,
                    'sid' => $schoolId
                ]);
                
                $_SESSION['success'] = "Escola vinculada com sucesso!";
            } else {
                $_SESSION['error'] = "Esta escola já está vinculada a este coordenador.";
            }
        }
        redirect('semed/coordinators');
    }
    
    public function unlinkSchoolFromCoordinator() {
        checkAuth('semed');
        $userId = $_GET['user_id'];
        $schoolId = $_GET['school_id'];
        
        $userModel = new User();
        // Don't allow removing the "Main" school if it's the only one? 
        // Or if it matches the legacy column? 
        // For MVP flexibility: Allow removing from pivot.
        
        $db = $userModel->getDb();
        $db->query("DELETE FROM user_schools WHERE user_id = :uid AND school_id = :sid", [
            'uid' => $userId,
            'sid' => $schoolId
        ]);
        
        $_SESSION['success'] = "Vínculo removido com sucesso!";
        redirect('semed/coordinators');
    }

    // --- Director Management ---
    public function directors() {
        checkAuth('semed');
        $user = auth();
        $userModel = new User();
        $schoolModel = new School();
        
        $assignedSchoolIds = $userModel->getAssignedSchoolIds($user['id']);
        $directors = $userModel->getByRole('director');
        
        // Filter directors belonging to my schools (optional, currently SEMED sees all or we filter)
        // Ideally filter by $assignedSchoolIds if multi-tenant SEMED, but usually SEMED sees all.
        // Let's filter if assignedSchoolIds is restricted.
        
        $schools = $schoolModel->all(); // Get all schools for dropdown
        
        $this->view('dashboard/semed_directors', [
            'directors' => $directors,
            'schools' => $schools
        ]);
    }

    public function storeDirector() {
        checkAuth('semed');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'school_id' => $_POST['school_id'],
                'whatsapp' => $_POST['whatsapp'],
                'address' => $_POST['address'],
                'role' => 'director',
                'password' => password_hash('123456', PASSWORD_DEFAULT)
            ];
            
            // Check email
            if ($userModel->findByEmail($data['email'])) {
                $_SESSION['error'] = "E-mail já cadastrado!";
            } else {
                $userModel->create($data);
                $_SESSION['success'] = "Diretor cadastrado com sucesso! Senha padrão: 123456";
            }
        }
        redirect('semed/directors');
    }

    public function editDirector() {
        checkAuth('semed');
        $id = $_GET['id'] ?? null;
        $user = auth();
        $userModel = new User();
        
        $director = $userModel->findById($id);
        $schools = $userModel->getManagedSchools($user['id']);
        
        $this->view('dashboard/semed_director_edit', [
            'director' => $director,
            'schools' => $schools
        ]);
    }

    public function updateDirector() {
        checkAuth('semed');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'whatsapp' => $_POST['whatsapp'],
                'address' => $_POST['address'],
                'school_id' => $_POST['school_id']
            ];
            
            // Note: For Directors we might not be using pivot table `user_schools` yet, primarily `school_id`.
            // But consistency with Coordinator suggests we should.
            // However, Director usually has ONE school. Let's stick to `school_id` column for simplicity as per request "Escola" (singular).
            
            $userModel = new User();
            $userModel->update($id, $data);
            $_SESSION['success'] = "Diretor atualizado com sucesso!";
        }
        redirect('semed/directors');
    }

    public function deleteDirector() {
        checkAuth('semed');
        $id = $_GET['id'] ?? null;
        if ($id) {
            $userModel = new User();
            $userModel->delete($id);
            $_SESSION['success'] = "Diretor excluído com sucesso!";
        }
        redirect('semed/directors');
    }

    public function resetPassword() {
        checkAuth('semed');
        $id = $_GET['id'] ?? null;
        if ($id) {
            $userModel = new User();
            $userModel->update($id, ['password' => password_hash('123456', PASSWORD_DEFAULT)]);
            $_SESSION['success'] = "Senha redefinida para '123456' com sucesso!";
        }
        $role = $_GET['role'] ?? 'coordinator';
        redirect('semed/' . ($role == 'coordinator' ? 'coordinators' : 'professors'));
    }

    public function plannings() {
        checkAuth('semed');
        $docModel = new Document();
        $schoolModel = new School();
        $userModel = new User();
        
        $user = auth();
        // 1. Get Assigned Schools
        $assignedSchoolIds = $userModel->getAssignedSchoolIds($user['id']);
        
        // Fallback: If no schools assigned in user_schools table, get all schools
        // This maintains backward compatibility for SEMED users not yet migrated to user_schools
        if (empty($assignedSchoolIds)) {
            $allSchools = $schoolModel->all();
            $assignedSchoolIds = array_column($allSchools, 'id');
        }
        
        $filters = [
            'school_id' => $_GET['school_id'] ?? null,
            'bimester' => $_GET['bimester'] ?? null,
            'status' => $_GET['status'] ?? null,
            'professor_id' => $_GET['professor_id'] ?? null,
            'allowed_school_ids' => $assignedSchoolIds
        ];

        // Security: If specific school requested, ensure it's assigned
        if ($filters['school_id'] && !in_array($filters['school_id'], $assignedSchoolIds)) {
            $filters['school_id'] = null; 
        }
        
        // 2. Fetch filtered documents
        $documents = $docModel->getAllWithFilters($filters);
        
        // 3. Filter School List for Dropdown
        $schools = $schoolModel->all();
        if (!empty($assignedSchoolIds)) {
            $schools = array_filter($schools, function($s) use ($assignedSchoolIds) {
                return in_array($s['id'], $assignedSchoolIds);
            });
        }
        
        // 4. Filter Professor List
        $professors = [];
        if (!empty($filters['school_id'])) {
             $professors = $userModel->getBySchoolId($filters['school_id'], 'professor');
        } else {
             $professors = $userModel->getBySchoolIds($assignedSchoolIds, 'professor');
        }

        // Calculate statistics for the chart
        $statusCounts = [
            'aprovado' => 0,
            'ajustado' => 0,
            'rejeitado' => 0,
            'enviado' => 0, // Aguardando/Enviado
            'total' => 0
        ];

        foreach ($documents as $doc) {
            $status = $doc['status'];
            if (isset($statusCounts[$status])) {
                $statusCounts[$status]++;
            } else {
                // Determine if it fits in fallback categories if exact status match fails
                // Assuming standard statuses are used, but handling weird cases just in case
                if ($status == 'entregue') $statusCounts['enviado']++;
                else $statusCounts['enviado']++; // Fallback for pending
            }
            $statusCounts['total']++;
        }
        
        $this->view('dashboard/semed_plannings', [
            'documents' => $documents,
            'schools' => $schools,
            'professors' => $professors,
            'filters' => $filters,
            'statusCounts' => $statusCounts
        ]);
    }

    public function reports() {
        checkAuth('semed');
        $type = $_GET['type'] ?? 'submissions';
        $schoolId = $_GET['school_id'] ?? null;
        $professorId = $_GET['professor_id'] ?? null;
        $period = $_GET['period'] ?? 'annual';
        
        $docModel = new Document();
        $schoolModel = new School();
        $userModel = new User();

        $schools = $schoolModel->all();
        
        // Filter schools for the current SEMED user
        $user = auth();
        $assignedSchoolIds = $userModel->getAssignedSchoolIds($user['id']);
        
        // Fallback: If no schools assigned in user_schools table, get all schools
        // This maintains backward compatibility for SEMED users not yet migrated to user_schools
        if (empty($assignedSchoolIds)) {
            $allSchools = $schoolModel->all();
            $assignedSchoolIds = array_column($allSchools, 'id');
        }
        
        if (!empty($assignedSchoolIds)) {
            $schools = array_filter($schools, function($s) use ($assignedSchoolIds) {
                return in_array($s['id'], $assignedSchoolIds);
            });
            
            // Security: If schoolId param is requested but not in assigned list, clear it
            if ($schoolId && !in_array($schoolId, $assignedSchoolIds)) {
                $schoolId = null; 
            }
        }
        
        $professors = [];
        
        $data = [];
        
        if ($schoolId) {
            $professors = $userModel->getBySchoolId($schoolId, 'professor');
        } else {
             // If no specific school selected, limit professors/data to assigned schools
             // This part might need deep Model refactoring for strict data security, 
             // but visually we are limiting the scope.
        }

        if ($professorId) {
            // Detailed professor report
            $data = $docModel->getProfessorStats($professorId, $period);
        } elseif ($type === 'pendencies') {
            // Get professors with pending/delayed documents
            $data = $docModel->getGlobalPendencies($schoolId);
        } elseif ($type === 'punctuality') {
            // Get averaging scores per school
            $data = $docModel->getSchoolPunctuality();
        } else {
            // Default: Submissions summary
            $data = $docModel->getSubmissionsReport($schoolId);
        }
        
        $this->view('dashboard/semed_reports', [
            'type' => $type,
            'data' => $data,
            'schools' => $schools,
            'professors' => $professors,
            'schoolId' => $schoolId,
            'professorId' => $professorId,
            'period' => $period
        ]);
    }

    public function changePassword() {
         checkAuth('semed');
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             $newPass = $_POST['password'];
             $user = auth();
             
             require_once __DIR__ . '/../Models/User.php';
             $userModel = new User();
             $userModel->updatePassword($user['id'], password_hash($newPass, PASSWORD_DEFAULT));
             
             $_SESSION['success'] = "Sua senha foi alterada com sucesso!";
             redirect('semed/dashboard');
         } else {
             // If accessed via GET, redirect to dashboard
             redirect('semed/dashboard');
         }
    }

    public function uploadPhoto() {
        checkAuth('semed');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
            $user = auth();
            $file = $_FILES['photo'];
            
            // Validation
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file['type'], $allowedTypes)) {
                $_SESSION['error'] = "Apenas imagens JPG e PNG são permitidas.";
                redirect('semed/dashboard');
            }

            if ($file['size'] > 2 * 1024 * 1024) { // 2MB
                $_SESSION['error'] = "A imagem deve ter no máximo 2MB.";
                redirect('semed/dashboard');
            }

            // Upload
            $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = 'user_' . $user['id'] . '_' . time() . '.' . $ext;
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                // Remove old photo
                try {
                    $userModel = new User();
                    $freshUser = $userModel->findById($user['id']);
                    
                    if (!empty($freshUser['profile_photo'])) {
                        $oldFilePath = $uploadDir . $freshUser['profile_photo'];
                        if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                } catch (Exception $e) { }

                // Update DB
                $userModel->updateProfilePhoto($user['id'], $fileName);
                
                // Update Session
                $_SESSION['user']['profile_photo'] = $fileName;
                $_SESSION['success'] = "Foto de perfil atualizada com sucesso!";
            } else {
                $_SESSION['error'] = "Erro ao fazer upload da imagem.";
            }
        }
        redirect('semed/dashboard');
    }
}
