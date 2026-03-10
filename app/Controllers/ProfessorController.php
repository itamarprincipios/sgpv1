<?php

require_once __DIR__ . '/../Models/Document.php';


require_once __DIR__ . '/../Models/Planning.php';

class ProfessorController extends Controller {
    public function dashboard() {
        checkAuth('professor');
        $sessionUser = auth();
        
        // Fetch fresh user data to get profile_photo
        require_once __DIR__ . '/../Models/User.php';
        $userModel = new User();
        $user = $userModel->findById($sessionUser['id']);
        
        $docModel = new Document();
        $documents = $docModel->getByUserId($user['id']);
        
        $planningModel = new Planning();
        
        // --- PROFILE SWITCH LOGIC ---
        $activeProfile = $_SESSION['active_profile'] ?? 'titular';
        
        // If teacher is NOT a monitor but active_profile is set to monitor (shouldn't happen), force titular
        if ($activeProfile === 'monitor' && empty($user['monitor_class_id'])) {
            $activeProfile = 'titular';
            $_SESSION['active_profile'] = 'titular';
        }

        if ($activeProfile === 'monitor') {
            // Monitor context
            $currentClassId = $user['monitor_class_id'];
            $isMonitorFlag = 1;
            $isPEFlag = 0; // Monitor usually isn't PE context simultaneously in MAE? Or keep user's flag?
            // User request implies distinct roles. Usually MAE is not PE.
            $isFirstGradeFlag = 0; 
        } else {
            // Titular context
            $currentClassId = $user['class_id'];
            $isMonitorFlag = 0; // Even if they are monitor, in titular view they see titular plannings
            $isPEFlag = $user['is_physical_education'] ?? 0;
            $isFirstGradeFlag = $user['is_first_grade'] ?? 0;
        }

        $periods = $planningModel->getReleasedBySchoolIdAndType(
            $user['school_id'], 
            $isPEFlag, 
            $isMonitorFlag, 
            $isFirstGradeFlag
        );

        require_once __DIR__ . '/../Models/RankingModel.php';
        $rankingModel = new RankingModel();
        $medals = $rankingModel->getMedalsForUser($user['id']);
        
        // Sum total points for this professor (including pending approval)
        $totalPoints = 0;
        foreach ($documents as $doc) {
            if (in_array($doc['status'], ['enviado', 'atrasado', 'aprovado', 'ajustado'])) {
                $totalPoints += (float)$doc['score_final'];
            }
        }

        // --- NEW: Fetch School, Class and Coordinator Info ---
        require_once __DIR__ . '/../Models/School.php';
        $schoolModel = new School();
        $schoolData = $schoolModel->findById($user['school_id']);
        
        $className = 'Não vinculada';
        
        if ($activeProfile === 'monitor' && !empty($user['monitor_class_id'])) {
            require_once __DIR__ . '/../Models/ClassModel.php';
            $classModel = new ClassModel();
            $classData = $classModel->findById($user['monitor_class_id']);
            if ($classData) {
                $className = $classData['name'] . ' (Monitoria M.A.E)';
            }
        } elseif (!empty($user['is_physical_education'])) {
            $className = 'Educação Física';
        } elseif (!empty($user['class_id'])) {
            require_once __DIR__ . '/../Models/ClassModel.php';
            $classModel = new ClassModel();
            $classData = $classModel->findById($user['class_id']);
            if ($classData) {
                $className = $classData['name'];
            }
        }

        // Get Coordinator Phone (First found for this school)
        $coordinators = $schoolModel->getCoordinators($user['school_id']);
        $coordinatorPhone = null;
        foreach ($coordinators as $coord) {
            if (!empty($coord['whatsapp'])) {
                $coordinatorPhone = $coord['whatsapp'];
                break;
            }
        }

        $this->view('dashboard/professor', [
            'user' => $user,
            'documents' => $documents,
            'periods' => $periods,
            'medals' => $medals,
            'totalPoints' => $totalPoints,
            'schoolData' => $schoolData,
            'className' => $className,
            'coordinatorPhone' => $coordinatorPhone,
            'activeProfile' => $activeProfile
        ]);
    }

    public function switchProfile() {
        checkAuth('professor');
        $target = $_GET['to'] ?? 'titular';
        
        if (in_array($target, ['titular', 'monitor'])) {
            $_SESSION['active_profile'] = $target;
        }
        
        redirect('professor/dashboard');
    }


    public function upload() {
        checkAuth('professor');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = auth();
            $title = $_POST['title'];
            $type = $_POST['type'];
            $period_id = $_POST['period_id'];
            
            // Upload logica
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                // Ensure dir exists
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $fileName = time() . '_' . basename($_FILES['file']['name']);
                $targetFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                    $docModel = new Document();
                    $planningModel = new Planning();
                    $period = $planningModel->findById($period_id);

                    // ======================================
                    // SUBSTITUIÇÃO AUTOMÁTICA
                    // Verificar se já existe documento para este período
                    // ======================================
                    $existingDoc = $docModel->findByUserAndPeriod($user['id'], $period_id);
                    $wasReplaced = false;
                    
                    if ($existingDoc) {
                        // Deletar arquivo físico antigo
                        $oldFilePath = $uploadDir . $existingDoc['file_path'];
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                        
                        // Deletar registro do banco
                        $docModel->delete($existingDoc['id']);
                        $wasReplaced = true;
                    }

                    $score_base = 10;
                    $penalty_delay = 0;
                    
                    if ($period) {
                        $now = time();
                        $deadline = strtotime($period['deadline']);
                        $opening = strtotime($period['opening_date']);
                        
                        // 1. Cálculo da Pontuação Base (Decrescente 20 -> 10)
                        $T_total = 10080; // 7 dias em minutos
                        if ($now <= $deadline) {
                            $diff_seconds = $deadline - $now;
                            $T_restante = floor($diff_seconds / 60);
                            
                            // Se estiver dentro da janela de 7 dias (ou ate antes, embora a release bloqueie)
                            if ($T_restante > $T_total) $T_restante = $T_total;
                            
                            $score_base = floor(10 + ($T_restante / $T_total) * 10);
                        } else {
                            // Atrasado
                            $score_base = 10;
                            
                            // 2. Cálculo da Penalidade por Atraso
                            $diff_delay_seconds = $now - $deadline;
                            $days_delay = ceil($diff_delay_seconds / 86400);
                            
                            if ($days_delay == 1) $penalty_delay = 2;
                            elseif ($days_delay == 2) $penalty_delay = 5;
                            elseif ($days_delay >= 3) $penalty_delay = 10;
                        }
                    }

                    $score_final = $score_base - $penalty_delay;

                    $documentId = $docModel->create([
                        'user_id' => $user['id'],
                        'period_id' => $period_id,
                        'title' => $title,
                        'type' => $type,
                        'file_path' => $fileName,
                        'status' => ($penalty_delay > 0) ? 'atrasado' : 'enviado',
                        'score_base' => $score_base,
                        'penalty_delay' => $penalty_delay,
                        'score_final' => $score_final
                    ]);
                    
                    // Extração automática do documento
                    try {
                        require_once __DIR__ . '/../Core/DocumentExtractor.php';
                        $extractor = new DocumentExtractor();
                        $extractor->extractAndSave($documentId);
                    } catch (Exception $e) {
                        error_log("Erro na extração automática do documento $documentId: " . $e->getMessage());
                    }
                    
                    // Feedback de sucesso
                    $_SESSION['success'] = $wasReplaced 
                        ? 'Documento anterior substituído com sucesso!' 
                        : 'Documento enviado com sucesso!';
                    
                    redirect('professor/dashboard');
                } else {
                    echo "Erro ao mover arquivo.";
                }
            } else {
               echo "Erro no upload.";
            }
        }
    }

    public function changePassword() {
         checkAuth('professor');
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             $newPass = $_POST['password'];
             $user = auth();
             
             require_once __DIR__ . '/../Models/User.php';
             $userModel = new User();
             $userModel->updatePassword($user['id'], password_hash($newPass, PASSWORD_DEFAULT));
             
             redirect('professor/dashboard');
         } else {
             // If accessed via GET (e.g. browser reload or direct link), redirect to dashboard
             redirect('professor/dashboard');
         }
    }

    public function deleteUpload() {
        checkAuth('professor');
        $id = $_GET['id'] ?? null;
        if (!$id) redirect('professor/dashboard');

        $user = auth();
        $docModel = new Document();
        
        $doc = $docModel->findById($id);
        
        if ($doc && $doc['user_id'] == $user['id']) {
            $filePath = __DIR__ . '/../../public/uploads/' . $doc['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $docModel->delete($id);
        }

        redirect('professor/dashboard');
    }

    public function uploadPhoto() {
        checkAuth('professor');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
            $user = auth();
            $file = $_FILES['photo'];
            
            // Validation
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file['type'], $allowedTypes)) {
                $_SESSION['error'] = "Apenas imagens JPG e PNG são permitidas.";
                redirect('professor/dashboard');
            }

            if ($file['size'] > 2 * 1024 * 1024) { // 2MB
                $_SESSION['error'] = "A imagem deve ter no máximo 2MB.";
                redirect('professor/dashboard');
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
                    require_once __DIR__ . '/../Models/User.php';
                    $userModel = new User();
                    $freshUser = $userModel->findById($user['id']);
                    
                    if (!empty($freshUser['profile_photo'])) {
                        $oldFilePath = $uploadDir . $freshUser['profile_photo'];
                        if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                } catch (Exception $e) {
                    // Fail silently on deletion to allow update to proceed
                }

                // Update DB
                $userModel->updateProfilePhoto($user['id'], $fileName);
                
                // Update Session
                $_SESSION['user']['profile_photo'] = $fileName;

                $_SESSION['success'] = "Foto de perfil atualizada!";
            } else {
                $_SESSION['error'] = "Erro ao salvar o arquivo.";
            }
        }
        redirect('professor/dashboard');
    }
}

