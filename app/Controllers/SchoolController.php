<?php

require_once __DIR__ . '/../Models/Document.php';
require_once __DIR__ . '/../Models/User.php';

require_once __DIR__ . '/../Models/Planning.php';
require_once __DIR__ . '/../Models/School.php';
require_once __DIR__ . '/../Models/ClassModel.php';

class SchoolController extends Controller {
    public function dashboard() {
        checkAuth(['coordinator', 'director']);
        $user = auth();
        
        $userModel = new User();
        // Get all assigned schools (pivot + main)
        // If main school_id is set, it's one. If pivot has rows, add them.
        $schoolIds = $userModel->getAssignedSchoolIds($user['id']);
        
        // Include the legacy/main one if not in pivot (migration safety)
        if (!in_array($user['school_id'], $schoolIds) && !empty($user['school_id'])) {
            $schoolIds[] = $user['school_id'];
        }
        
        // If no schools associated, fallback or error (but Coordinator usually has at least one)
        if (empty($schoolIds)) $schoolIds = [0];

        // LOGIC CHANGE: 
        // We need to fetch data for ALL these schools.
        // Existing models need to accept ARRAYS or we loop.
        // Best approach: Update models to accept Arrays (like we did for SEMED).
        
        // However, SchoolController is designed for "Context of a School".
        // Options:
        // 1. Show Aggregate (All Plannings from All Schools).
        // 2. Add a Dropdown to "Switch School Context" at the top.
        // Given complexity vs usability:
        // Aggregate is good for overview, but might be confusing if names clash.
        // Let's do Aggregate for MVP as requested "Coordinator seeing schools linked".
        
        // Initialize Models
        $schoolModel = new School();
        $docModel = new Document(); // Fix undefined variable
        
        // Fetch School Objects
        $schools = [];
        foreach($schoolIds as $sid) {
            $s = $schoolModel->findById($sid);
            if ($s) $schools[] = $s;
        }

        // Define Filters from GET
        $filters = [
            'period_id' => $_GET['period_id'] ?? null,
            'professor_id' => $_GET['professor_id'] ?? null,
            'status' => $_GET['status'] ?? null
        ];

        // Create Map for Name Injection
        $schoolsMap = [];
        foreach($schools as $s) {
            $schoolsMap[$s['id']] = $s['name'];
        }
        
        // Default single school context for view compatibility
        $school = !empty($schools) ? $schools[0] : null;

        // Tab 1: Plannings

        // Tab 1: Plannings
        $planningModel = new Planning();
        $plannings = [];
        $pendingSubmissions = [];
        foreach($schoolIds as $sid) {
            $name = $schoolsMap[$sid] ?? '';
            
            $p = $planningModel->getBySchoolId($sid);
            if($p) {
                foreach($p as &$val) $val['school_name'] = $name;
                $plannings = array_merge($plannings, $p);
            }
            
            $sub = $planningModel->getPendingSubmissions($sid);
            if($sub) {
                foreach($sub as &$val) $val['school_name'] = $name;
                $pendingSubmissions = array_merge($pendingSubmissions, $sub);
            }
        }
        
        // Tab 2: Recent Uploads
        $documents = [];
        foreach($schoolIds as $sid) {
             $docs = $docModel->getBySchoolIdWithFilters($sid, $filters);
             if($docs) {
                 $name = $schoolsMap[$sid] ?? '';
                 foreach($docs as &$val) $val['school_name'] = $name;
                 $documents = array_merge($documents, $docs);
             }
        }
        
        // Count New
        $newUploadsCount = 0;
        $lastViewed = $_SESSION['last_viewed_uploads'] ?? null;
        foreach ($documents as $d) {
            if (in_array($d['status'], ['enviado', 'atrasado'])) {
                if (!$lastViewed || strtotime($d['submitted_at']) > $lastViewed) {
                    $newUploadsCount++;
                }
            }
        }
        
        // Tab 3: Classes
        $classModel = new ClassModel();
        $classes = [];
        foreach($schoolIds as $sid) {
            $c = $classModel->getBySchoolIdWithProfessor($sid);
            if($c) {
                $name = $schoolsMap[$sid] ?? '';
                foreach($c as &$val) {
                    $val['school_id'] = $sid; 
                    $val['school_name'] = $name;
                }
                $classes = array_merge($classes, $c);
            }
        }

        // Tab 4: Professors
        $professors = [];
        foreach($schoolIds as $sid) {
            $p = $userModel->getProfessorsBySchoolWithClass($sid);
            if($p) {
                $name = $schoolsMap[$sid] ?? '';
                foreach($p as &$val) $val['school_name'] = $name;
                $professors = array_merge($professors, $p);
            }
        }

        // Tab 5: Coordinators (Director Only)
        $coordinators = [];
        if ($user['role'] == 'director') {
            foreach($schoolIds as $sid) {
                 $coords = $userModel->getCoordinatorsBySchool($sid);
                 if($coords) {
                     $name = $schoolsMap[$sid] ?? '';
                     foreach($coords as &$val) $val['school_name'] = $name;
                     $coordinators = array_merge($coordinators, $coords);
                 }
            }
        }

        // Tab 5: Coordinators (Director Only)
        $coordinators = [];
        if ($user['role'] == 'director') {
            foreach($schoolIds as $sid) {
                 $coords = $userModel->getCoordinatorsBySchool($sid);
                 if($coords) {
                     $name = $schoolsMap[$sid] ?? '';
                     foreach($coords as &$val) $val['school_name'] = $name;
                     $coordinators = array_merge($coordinators, $coords);
                 }
            }
        }

        $this->view('dashboard/school', [
            'user' => $user,
            'school' => $school, 
            'schools' => $schools, 
            'plannings' => $plannings,
            'documents' => $documents,
            'classes' => $classes,
            'professors' => $professors,
            'coordinators' => $coordinators, 
            'pendingSubmissions' => $pendingSubmissions,
            'newUploadsCount' => $newUploadsCount,
            'filters' => $filters,
            // Gamification Data
            'schoolIds' => $schoolIds,
            'docModel' => new Document()
        ]);
    }

    public function createPlanning() {
        checkAuth(['coordinator', 'director']);
        $user = auth();
        $userModel = new User();
        $schoolIds = $userModel->getAssignedSchoolIds($user['id']);
        if (!in_array($user['school_id'], $schoolIds) && !empty($user['school_id'])) {
            $schoolIds[] = $user['school_id'];
        }
        
        $schoolModel = new School();
        $schools = [];
        foreach($schoolIds as $sid) {
            $schools[] = $schoolModel->findById($sid);
        }

        $this->view('dashboard/planning_create', ['schools' => $schools]);
    }

    public function storePlanning() {
        checkAuth(['coordinator', 'director']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $start_date = $_POST['start_date']; 
            
            $deadline = date('Y-m-d 23:59:59', strtotime($start_date . ' - 1 day'));
            $opening_date = date('Y-m-d 00:00:00', strtotime($start_date . ' - 7 days'));

            // School ID Validation
            $user = auth();
            $targetSchoolId = $_POST['school_id'] ?? $user['school_id'];
            
            $userModel = new User();
            $assigned = $userModel->getAssignedSchoolIds($user['id']);
            if (!empty($user['school_id'])) $assigned[] = $user['school_id'];
            
            if (!in_array($targetSchoolId, $assigned)) {
                 $_SESSION['error'] = "Você não tem permissão para esta escola.";
                 redirect('school/dashboard');
                 return;
            }

            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'start_date' => $start_date . ' 00:00:00',
                'end_date' => $_POST['end_date'],
                'deadline' => $deadline,
                'opening_date' => $opening_date,
                'school_id' => $targetSchoolId,
                'is_physical_education' => isset($_POST['is_physical_education']) ? 1 : 0,
                'is_monitor' => isset($_POST['is_monitor']) ? 1 : 0,
                'is_first_grade' => isset($_POST['is_first_grade']) ? 1 : 0
            ];

            $planning = new Planning();
            $planning->create($data);

            redirect('school/dashboard'); 
        }
    }

    public function viewPlanning() {
        checkAuth(['coordinator', 'director']);
        $id = $_GET['id'] ?? null;
        if (!$id) redirect('school/dashboard');
        
        $planningModel = new Planning();
        $planning = $planningModel->findById($id);
        $schoolId = auth()['school_id'];
        
        // Security check
        if (!$planning || $planning['school_id'] != $schoolId) redirect('school/dashboard');

        // Get details (pass if it's PE planning or regular)
        $details = $planningModel->getPlanningStats($id, $schoolId, $planning['is_physical_education'] ?? 0, $planning['is_monitor'] ?? 0, $planning['is_first_grade'] ?? 0);

        // Group by Class
        $groupedData = [];
        foreach ($details as $row) {
            $groupedData[$row['class_name']][] = $row;
        }

        $this->view('dashboard/planning_detail', [
            'planning' => $planning,
            'groupedData' => $groupedData
        ]);
    }

    public function editPlanning() {
        checkAuth(['coordinator', 'director']);
        $id = $_GET['id'] ?? null;
        if (!$id) redirect('school/dashboard');

        $planningModel = new Planning();
        $planning = $planningModel->findById($id);

        if (!$planning || $planning['school_id'] != auth()['school_id']) redirect('school/dashboard');

        $this->view('dashboard/planning_edit', ['planning' => $planning]);
    }

    public function updatePlanning() {
        checkAuth(['coordinator', 'director']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $start_date = $_POST['start_date'];
            
            // Deadline: 1 dia antes da vigência, às 23:59:59
            $deadline = date('Y-m-d 23:59:59', strtotime($start_date . ' - 1 day'));
            // Abertura: 7 dias antes da vigência, às 00:00:00
            $opening_date = date('Y-m-d 00:00:00', strtotime($start_date . ' - 7 days'));

            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'deadline' => $deadline,
                'opening_date' => $opening_date,
                'start_date' => $start_date . ' 00:00:00',
                'is_physical_education' => isset($_POST['is_physical_education']) ? 1 : 0,
                'is_monitor' => isset($_POST['is_monitor']) ? 1 : 0,
                'is_first_grade' => isset($_POST['is_first_grade']) ? 1 : 0
            ];

            $planningModel = new Planning();
            $planningModel->update($id, $data);

            redirect('school/dashboard');
        }
    }

    public function deletePlanning() {
        checkAuth(['coordinator', 'director']);
        $id = $_GET['id'] ?? null;
        if ($id) {
            $planningModel = new Planning();
            $planning = $planningModel->findById($id);
            if ($planning && $planning['school_id'] == auth()['school_id']) {
                $planningModel->delete($id);
            }
        }
        redirect('school/dashboard');
    }

    // --- Classes CRUD ---
    public function storeClass() {
        checkAuth(['coordinator', 'director']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $user = auth();
            $targetSchoolId = $_POST['school_id'] ?? $user['school_id'];
            
            $userModel = new User();
            $assigned = $userModel->getAssignedSchoolIds($user['id']);
            if (!empty($user['school_id'])) $assigned[] = $user['school_id'];
            
            if (!in_array($targetSchoolId, $assigned)) {
                 $_SESSION['error'] = "Você não tem permissão para esta escola.";
                 redirect('school/classes');
            }

            $classModel = new ClassModel();
            $classModel->create($targetSchoolId, $name);
            redirect('school/classes');
        }
    }

    public function editClass() {
        checkAuth(['coordinator', 'director']);
        $id = $_GET['id'] ?? null;
        if (!$id) redirect('school/dashboard');

        $classModel = new ClassModel();
        $class = $classModel->findById($id);

        if (!$class || $class['school_id'] != auth()['school_id']) redirect('school/dashboard');

        $this->view('dashboard/class_edit', ['class' => $class]);
    }

    public function updateClass() {
        checkAuth(['coordinator', 'director']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];

            $classModel = new ClassModel();
            $class = $classModel->findById($id);
            
            if ($class && $class['school_id'] == auth()['school_id']) {
                $classModel->update($id, $name);
            }

            redirect('school/classes');
        }
    }

    public function deleteClass() {
        checkAuth(['coordinator', 'director']);
        $id = $_GET['id'] ?? null;
        if ($id) {
            $classModel = new ClassModel();
            $class = $classModel->findById($id);
            // Ensuring we compare values correctly
            if ($class && (int)$class['school_id'] === (int)auth()['school_id']) {
                $classModel->delete($id);
            }
        }
        redirect('school/classes');
    }

    // --- Professor CRUD ---
    public function storeProfessor() {
        checkAuth(['coordinator', 'director']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            
            $user = auth();
            $targetSchoolId = $_POST['school_id'] ?? $user['school_id'];
            
            $assigned = $userModel->getAssignedSchoolIds($user['id']);
            if (!empty($user['school_id'])) $assigned[] = $user['school_id'];
            
            if (!in_array($targetSchoolId, $assigned)) {
                 $_SESSION['error'] = "Você não tem permissão para esta escola.";
                 redirect('school/professors');
            }

            // Basic validation skipped for MVP
            $data = [
                'school_id' => $targetSchoolId,
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => password_hash('professor123', PASSWORD_DEFAULT), // Default password fixed
                'whatsapp' => $_POST['whatsapp'],
                'class_id' => !empty($_POST['class_id']) ? $_POST['class_id'] : null,
                'is_physical_education' => isset($_POST['is_physical_education']) ? 1 : 0,
                'is_monitor' => isset($_POST['is_monitor']) ? 1 : 0,
                'is_first_grade' => isset($_POST['is_first_grade']) ? 1 : 0
            ];
            $userModel->createProfessor($data);
            redirect('school/professors');
        }
    }

    public function editProfessor() {
        checkAuth(['coordinator', 'director']);
        $id = $_GET['id'] ?? null;
        if(!$id) redirect('school/professors');

        $userModel = new User();
        $professor = $userModel->findById($id);
        
        $classModel = new ClassModel();
        $classes = $classModel->getBySchoolId(auth()['school_id']);

        $this->view('dashboard/professor_edit', [
            'professor' => $professor,
            'classes' => $classes
        ]);
    }

    public function updateProfessor() {
        checkAuth(['coordinator', 'director']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $data = [
                'id' => $_POST['id'],
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'whatsapp' => $_POST['whatsapp'],
                'class_id' => !empty($_POST['class_id']) ? $_POST['class_id'] : null,
                'is_physical_education' => isset($_POST['is_physical_education']) ? 1 : 0,
                'is_monitor' => isset($_POST['is_monitor']) ? 1 : 0,
                'is_first_grade' => isset($_POST['is_first_grade']) ? 1 : 0
            ];
            $userModel->update($_POST['id'], $data);
            $_SESSION['success'] = "Professor atualizado com sucesso!";
            redirect('school/professors');
        }
    }

    public function deleteProfessor() {
        checkAuth(['coordinator', 'director']);
        if (isset($_GET['id'])) {
           $userModel = new User();
           $userModel->delete($_GET['id']); // Add security check
           redirect('school/professors');
        }
    }

    public function reviewDocument() {
        checkAuth(['coordinator', 'director']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $statusInput = $_POST['status']; // 'aprovado', 'ajustado', 'rejeitado'

            $docModel = new Document();
            $doc = $docModel->findById($id);

            if ($doc) {
                $updateData = ['id' => $id];
                $rejection_count = (int)$doc['rejection_count'];
                $penalty_resubmission = (int)$doc['penalty_resubmission'];

                if ($statusInput === 'rejeitado') {
                    $rejection_count++;
                    if ($rejection_count == 2) $penalty_resubmission = 2;
                    elseif ($rejection_count == 3) $penalty_resubmission = 7;
                    elseif ($rejection_count >= 4) $penalty_resubmission = 10;

                    $updateData['status'] = 'rejeitado';
                    $updateData['rejection_count'] = $rejection_count;
                    $updateData['rejected_at'] = date('Y-m-d H:i:s');
                    $updateData['penalty_resubmission'] = $penalty_resubmission;
                } else {
                    $status = ($statusInput === 'ajustado') ? 'ajustado' : 'aprovado';
                    $updateData['status'] = $status;
                }

                // Recalculate Final Score
                $score_base = (float)$doc['score_base'];
                $penalty_delay = (int)$doc['penalty_delay'];
                $updateData['score_final'] = max(0, $score_base - $penalty_delay - $penalty_resubmission);

                if ($statusInput === 'rejeitado') {
                    $_SESSION['success'] = "Planejamento devolvido para correção com sucesso!";
                } else {
                    $msg = ($statusInput === 'ajustado') ? "Planejamento aprovado com ajustes com sucesso!" : "Planejamento aprovado com sucesso!";
                    $_SESSION['success'] = $msg;
                }

                $docModel->updateStatus($id, $updateData);

                redirect('school/planning/view?id=' . $doc['period_id']);
            } else {
                // If document ID not found, redirect back
                redirect('school/dashboard');
            }
        }
    }

    public function associateToBimester() {
        checkAuth(['coordinator', 'director']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['planning_id'] ?? null;
            $bimester = $_POST['bimester'] ?? null;
            
            if ($id && $bimester !== null) {
                $planningModel = new Planning();
                $planningModel->updateBimester($id, $bimester);
                $_SESSION['success'] = "Planejamento organizado no " . $bimester . "º Bimestre!";
            }
        }
        
        redirect('school/dashboard?tab=bimesters');
    }

    public function resetProfessorPassword() {
        checkAuth(['coordinator', 'director']);
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $userModel = new User();
            $professor = $userModel->findById($id);
            
            // Security check: ensure the professor belongs to the coordinator's school
            $coordinator = auth();
            if ($professor && $professor['school_id'] == $coordinator['school_id'] && $professor['role'] == 'professor') {
                $userModel->update($id, ['password' => password_hash('professor123', PASSWORD_DEFAULT)]);
                $_SESSION['success'] = "Senha do professor resetada para 'professor123' com sucesso!";
            } else {
                $_SESSION['error'] = "Você não tem permissão para resetar a senha deste usuário.";
            }
        }
        
        redirect('school/dashboard');
    }

    public function markUploadsAsViewed() {
        checkAuth(['coordinator', 'director']);
        $_SESSION['last_viewed_uploads'] = time();
        echo json_encode(['status' => 'success']);
        exit;
    }

    public function changePassword() {
        checkAuth(['coordinator', 'director']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPass = $_POST['password'];
            $user = auth();
            
            $userModel = new User();
            $userModel->updatePassword($user['id'], password_hash($newPass, PASSWORD_DEFAULT));
            
            $_SESSION['success'] = "Sua senha foi alterada com sucesso!";
            redirect('school/dashboard');
        } else {
            // If accessed via GET, redirect to dashboard
            redirect('school/dashboard');
        }
    }

    public function uploadPhoto() {
        checkAuth('coordinator');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
            $user = auth();
            $file = $_FILES['photo'];
            
            // Validation
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file['type'], $allowedTypes)) {
                $_SESSION['error'] = "Apenas imagens JPG e PNG são permitidas.";
                redirect('school/dashboard');
            }

            if ($file['size'] > 2 * 1024 * 1024) { // 2MB
                $_SESSION['error'] = "A imagem deve ter no máximo 2MB.";
                redirect('school/dashboard');
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
        redirect('school/dashboard');
    }

    // --- AGENTIC CODE: Director Management Logic ---
    public function storeCoordinator() {
        checkAuth('director'); 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = auth();
            $userModel = new User();
            $targetSchoolId = $_POST['school_id'];

            // Validation: Director must own this school
            $assigned = $userModel->getAssignedSchoolIds($user['id']);
            if (!empty($user['school_id'])) $assigned[] = $user['school_id'];

            if (!in_array($targetSchoolId, $assigned)) {
                $_SESSION['error'] = "Permissão negada para esta escola.";
                redirect('school/dashboard'); 
                return;
            }

            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'school_id' => $targetSchoolId,
                'whatsapp' => $_POST['whatsapp'],
                'role' => 'coordinator',
                'password' => password_hash('123456', PASSWORD_DEFAULT)
            ];

            if ($userModel->findByEmail($data['email'])) {
                $_SESSION['error'] = "E-mail já cadastrado!";
            } else {
                $userModel->create($data);
                $_SESSION['success'] = "Coordenador cadastrado com sucesso! Senha padrão: 123456";
            }
        }
        redirect('school/dashboard?tab=coordinators');
    }

    public function editCoordinator() {
        checkAuth('director');
        $id = $_GET['id'] ?? null;
        if (!$id) redirect('school/dashboard');

        $userModel = new User();
        $coordinator = $userModel->findById($id);
        $schools = $userModel->getManagedSchools(auth()['id']);
        
        $this->view('dashboard/coordinator_edit', ['coordinator' => $coordinator, 'schools' => $schools]);
    }

    public function updateCoordinator() {
        checkAuth('director');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'whatsapp' => $_POST['whatsapp'],
                'school_id' => $_POST['school_id'] 
            ];
            
            $userModel = new User();
            $userModel->update($id, $data);
            $_SESSION['success'] = "Coordenador atualizado!";
        }
        redirect('school/dashboard?tab=coordinators');
    }

    public function resetCoordinatorPassword() {
        checkAuth('director');
        $id = $_GET['id'] ?? null;
        if (!$id) redirect('school/dashboard?tab=coordinators');

        $userModel = new User();
        // Security: Check if director owns this coordinator (via school)
        // MVP: Just reset
        $newPass = password_hash('123456', PASSWORD_DEFAULT);
        
        // Direct SQL update to avoid validations in update method if any
        $db = Database::getInstance();
        $db->query("UPDATE users SET password = :pass WHERE id = :id", ['pass' => $newPass, 'id' => $id]);
        
        $_SESSION['success'] = "Senha resetada para 123456 com sucesso!";
        redirect('school/dashboard?tab=coordinators');
    }

    public function deleteCoordinator() {
        checkAuth('director');
        $id = $_GET['id'];
        $userModel = new User();
        // Add security check here...
        $userModel->delete($id);
        redirect('school/dashboard?tab=coordinators');
    }

    // --- Director Reports ---
    public function reports() {
        checkAuth('director');
        $user = auth();
        $userModel = new User();
        $docModel = new Document();
        
        $type = $_GET['type'] ?? 'submissions';
        $professorId = $_GET['professor_id'] ?? null;
        $period = $_GET['period'] ?? 'annual';
        
        // Director only sees their own schools
        $assignedSchoolIds = $userModel->getAssignedSchoolIds($user['id']);
        if (!empty($user['school_id'])) {
             if (!in_array($user['school_id'], $assignedSchoolIds)) {
                 $assignedSchoolIds[] = $user['school_id'];
             }
        }
        
        $schoolId = $_GET['school_id'] ?? ($assignedSchoolIds[0] ?? null);
        
        if ($schoolId && !in_array($schoolId, $assignedSchoolIds)) {
            $_SESSION['error'] = "Acesso negado para esta escola.";
            redirect('school/dashboard');
            return;
        }

        $schoolModel = new School();
        
        // Get schools for this director
        if (empty($assignedSchoolIds)) {
            $schools = $schoolModel->all();
        } else {
            $allSchools = $schoolModel->all();
            $schools = array_filter($allSchools, function($school) use ($assignedSchoolIds) {
                return in_array($school['id'], $assignedSchoolIds);
            });
            $schools = array_values($schools); // Re-index array
        }
        
        $professors = $schoolId ? $userModel->getBySchoolId($schoolId, 'professor') : [];

        $data = [];
        if ($professorId) {
             $prof = $userModel->findById($professorId);
             if (!$prof || !in_array($prof['school_id'], $assignedSchoolIds)) {
                 $data = []; 
             } else {
                 $data = $docModel->getProfessorStats($professorId, $period);
             }
        } elseif ($type === 'pendencies') {
             $data = $docModel->getGlobalPendencies($schoolId);
        } elseif ($type === 'punctuality') {
             $data = $docModel->getProfessorPunctualityBySchool($schoolId); 
        } else {
             $data = $docModel->getSubmissionsReport($schoolId);
        }

        $this->view('dashboard/school_reports', [
            'type' => $type,
            'data' => $data,
            'schools' => $schools,
            'professors' => $professors,
            'schoolId' => $schoolId,
            'professorId' => $professorId,
            'period' => $period,
            'user' => $user
        ]);
    }

    // --- NEW: Separate Page Methods ---
    
    public function plannings() {
        checkAuth(['coordinator', 'director']);
        $user = auth();
        
        $userModel = new User();
        $schoolIds = $userModel->getAssignedSchoolIds($user['id']);
        if (!in_array($user['school_id'], $schoolIds) && !empty($user['school_id'])) {
            $schoolIds[] = $user['school_id'];
        }
        if (empty($schoolIds)) $schoolIds = [0];
        
        $schoolModel = new School();
        $schools = [];
        $schoolsMap = [];
        foreach($schoolIds as $sid) {
            $s = $schoolModel->findById($sid);
            if ($s) {
                $schools[] = $s;
                $schoolsMap[$sid] = $s['name'];
            }
        }
        
        $school = !empty($schools) ? $schools[0] : null;
        
        $planningModel = new Planning();
        $plannings = [];
        foreach($schoolIds as $sid) {
            $p = $planningModel->getBySchoolId($sid);
            if($p) {
                foreach($p as &$val) $val['school_name'] = $schoolsMap[$sid] ?? '';
                $plannings = array_merge($plannings, $p);
            }
        }
        
        $this->view('school/school_plannings', [
            'user' => $user,
            'school' => $school,
            'schools' => $schools,
            'plannings' => $plannings
        ]);
    }
    
    public function bimesters() {
        checkAuth(['coordinator', 'director']);
        $user = auth();
        
        $userModel = new User();
        $schoolIds = $userModel->getAssignedSchoolIds($user['id']);
        if (!in_array($user['school_id'], $schoolIds) && !empty($user['school_id'])) {
            $schoolIds[] = $user['school_id'];
        }
        if (empty($schoolIds)) $schoolIds = [0];
        
        $schoolModel = new School();
        $schools = [];
        $schoolsMap = [];
        foreach($schoolIds as $sid) {
            $s = $schoolModel->findById($sid);
            if ($s) {
                $schools[] = $s;
                $schoolsMap[$sid] = $s['name'];
            }
        }
        
        $school = !empty($schools) ? $schools[0] : null;
        
        $planningModel = new Planning();
        $plannings = [];
        foreach($schoolIds as $sid) {
            $p = $planningModel->getBySchoolId($sid);
            if($p) {
                foreach($p as &$val) $val['school_name'] = $schoolsMap[$sid] ?? '';
                $plannings = array_merge($plannings, $p);
            }
        }
        
        $this->view('school/school_bimesters', [
            'user' => $user,
            'school' => $school,
            'schools' => $schools,
            'plannings' => $plannings
        ]);
    }
    
    public function pending() {
        checkAuth(['coordinator', 'director']);
        $user = auth();
        
        $userModel = new User();
        $schoolIds = $userModel->getAssignedSchoolIds($user['id']);
        if (!in_array($user['school_id'], $schoolIds) && !empty($user['school_id'])) {
            $schoolIds[] = $user['school_id'];
        }
        if (empty($schoolIds)) $schoolIds = [0];
        
        $schoolModel = new School();
        $schools = [];
        $schoolsMap = [];
        foreach($schoolIds as $sid) {
            $s = $schoolModel->findById($sid);
            if ($s) {
                $schools[] = $s;
                $schoolsMap[$sid] = $s['name'];
            }
        }
        
        $school = !empty($schools) ? $schools[0] : null;
        
        $planningModel = new Planning();
        $pendingSubmissions = [];
        foreach($schoolIds as $sid) {
            $sub = $planningModel->getPendingSubmissions($sid);
            if($sub) {
                foreach($sub as &$val) $val['school_name'] = $schoolsMap[$sid] ?? '';
                $pendingSubmissions = array_merge($pendingSubmissions, $sub);
            }
        }
        
        $this->view('school/school_pending', [
            'user' => $user,
            'school' => $school,
            'schools' => $schools,
            'pendingSubmissions' => $pendingSubmissions
        ]);
    }
    
    public function uploads() {
        checkAuth(['coordinator', 'director']);
        $user = auth();
        
        $userModel = new User();
        $schoolIds = $userModel->getAssignedSchoolIds($user['id']);
        if (!in_array($user['school_id'], $schoolIds) && !empty($user['school_id'])) {
            $schoolIds[] = $user['school_id'];
        }
        if (empty($schoolIds)) $schoolIds = [0];
        
        $schoolModel = new School();
        $schools = [];
        $schoolsMap = [];
        foreach($schoolIds as $sid) {
            $s = $schoolModel->findById($sid);
            if ($s) {
                $schools[] = $s;
                $schoolsMap[$sid] = $s['name'];
            }
        }
        
        $school = !empty($schools) ? $schools[0] : null;
        
        $filters = [
            'period_id' => $_GET['period_id'] ?? null,
            'professor_id' => $_GET['professor_id'] ?? null,
            'status' => $_GET['status'] ?? null
        ];
        
        $docModel = new Document();
        $documents = [];
        foreach($schoolIds as $sid) {
            $docs = $docModel->getBySchoolIdWithFilters($sid, $filters);
            if($docs) {
                foreach($docs as &$val) $val['school_name'] = $schoolsMap[$sid] ?? '';
                $documents = array_merge($documents, $docs);
            }
        }
        
        $newUploadsCount = 0;
        $lastViewed = $_SESSION['last_viewed_uploads'] ?? null;
        foreach ($documents as $d) {
            if (in_array($d['status'], ['enviado', 'atrasado'])) {
                if (!$lastViewed || strtotime($d['submitted_at']) > $lastViewed) {
                    $newUploadsCount++;
                }
            }
        }
        
        $planningModel = new Planning();
        $plannings = [];
        foreach($schoolIds as $sid) {
            $p = $planningModel->getBySchoolId($sid);
            if($p) {
                foreach($p as &$val) $val['school_name'] = $schoolsMap[$sid] ?? '';
                $plannings = array_merge($plannings, $p);
            }
        }
        
        $professors = [];
        foreach($schoolIds as $sid) {
            $p = $userModel->getProfessorsBySchoolWithClass($sid);
            if($p) {
                foreach($p as &$val) $val['school_name'] = $schoolsMap[$sid] ?? '';
                $professors = array_merge($professors, $p);
            }
        }
        
        $this->view('school/school_uploads', [
            'user' => $user,
            'school' => $school,
            'schools' => $schools,
            'documents' => $documents,
            'plannings' => $plannings,
            'professors' => $professors,
            'filters' => $filters,
            'newUploadsCount' => $newUploadsCount
        ]);
    }
    
    public function classes() {
        checkAuth(['coordinator', 'director']);
        $user = auth();
        
        $userModel = new User();
        $schoolIds = $userModel->getAssignedSchoolIds($user['id']);
        if (!in_array($user['school_id'], $schoolIds) && !empty($user['school_id'])) {
            $schoolIds[] = $user['school_id'];
        }
        if (empty($schoolIds)) $schoolIds = [0];
        
        $schoolModel = new School();
        $schools = [];
        $schoolsMap = [];
        foreach($schoolIds as $sid) {
            $s = $schoolModel->findById($sid);
            if ($s) {
                $schools[] = $s;
                $schoolsMap[$sid] = $s['name'];
            }
        }
        
        $school = !empty($schools) ? $schools[0] : null;
        
        $classModel = new ClassModel();
        $classes = [];
        foreach($schoolIds as $sid) {
            $c = $classModel->getBySchoolIdWithProfessor($sid);
            if($c) {
                foreach($c as &$val) {
                    $val['school_id'] = $sid;
                    $val['school_name'] = $schoolsMap[$sid] ?? '';
                }
                $classes = array_merge($classes, $c);
            }
        }
        
        $this->view('school/school_classes', [
            'user' => $user,
            'school' => $school,
            'schools' => $schools,
            'classes' => $classes
        ]);
    }
    
    public function professors() {
        checkAuth(['coordinator', 'director']);
        $user = auth();
        
        $userModel = new User();
        $schoolIds = $userModel->getAssignedSchoolIds($user['id']);
        if (!in_array($user['school_id'], $schoolIds) && !empty($user['school_id'])) {
            $schoolIds[] = $user['school_id'];
        }
        if (empty($schoolIds)) $schoolIds = [0];
        
        $schoolModel = new School();
        $schools = [];
        $schoolsMap = [];
        foreach($schoolIds as $sid) {
            $s = $schoolModel->findById($sid);
            if ($s) {
                $schools[] = $s;
                $schoolsMap[$sid] = $s['name'];
            }
        }
        
        $school = !empty($schools) ? $schools[0] : null;
        
        $professors = [];
        foreach($schoolIds as $sid) {
            $p = $userModel->getProfessorsBySchoolWithClass($sid);
            if($p) {
                foreach($p as &$val) $val['school_name'] = $schoolsMap[$sid] ?? '';
                $professors = array_merge($professors, $p);
            }
        }
        
        $classModel = new ClassModel();
        $classes = [];
        foreach($schoolIds as $sid) {
            $c = $classModel->getBySchoolIdWithProfessor($sid);
            if($c) {
                foreach($c as &$val) {
                    $val['school_id'] = $sid;
                    $val['school_name'] = $schoolsMap[$sid] ?? '';
                }
                $classes = array_merge($classes, $c);
            }
        }
        
        $this->view('school/school_professors', [
            'user' => $user,
            'school' => $school,
            'schools' => $schools,
            'professors' => $professors,
            'classes' => $classes
        ]);
    }
    
    public function coordinators() {
        checkAuth('director');
        $user = auth();
        
        $userModel = new User();
        $schoolIds = $userModel->getAssignedSchoolIds($user['id']);
        if (!in_array($user['school_id'], $schoolIds) && !empty($user['school_id'])) {
            $schoolIds[] = $user['school_id'];
        }
        if (empty($schoolIds)) $schoolIds = [0];
        
        $schoolModel = new School();
        $schools = [];
        $schoolsMap = [];
        foreach($schoolIds as $sid) {
            $s = $schoolModel->findById($sid);
            if ($s) {
                $schools[] = $s;
                $schoolsMap[$sid] = $s['name'];
            }
        }
        
        $school = !empty($schools) ? $schools[0] : null;
        
        $coordinators = [];
        foreach($schoolIds as $sid) {
            $coords = $userModel->getCoordinatorsBySchool($sid);
            if($coords) {
                foreach($coords as &$val) $val['school_name'] = $schoolsMap[$sid] ?? '';
                $coordinators = array_merge($coordinators, $coords);
            }
        }
        
        $this->view('school/school_coordinators', [
            'user' => $user,
            'school' => $school,
            'schools' => $schools,
            'coordinators' => $coordinators
        ]);
    }
}


