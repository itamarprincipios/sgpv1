<?php
/**
 * SupervisorMonitorController - Controlador para Supervisora SEMED de Monitores
 * 
 * Funcionalidades:
 * - Dashboard com visão de TODOS os professores Monitores da rede
 * - Estatísticas de envios e pontualidade
 * - Visualização de planejamentos (somente leitura)
 * - Sugestões pedagógicas via WhatsApp
 */

require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Document.php';
require_once __DIR__ . '/../Models/School.php';

class SupervisorMonitorController extends Controller {
    
    /**
     * Dashboard principal da Supervisora de Monitores
     */
    public function dashboard() {
        $user = auth();
        
        // Verificar se é supervisor_monitor
        if (!$user || $user['role'] !== 'supervisor_monitor') {
            redirect('login');
        }
        
        $userModel = new User();
        $documentModel = new Document();
        $schoolModel = new School();
        
        // Buscar TODOS professores Monitores
        $professors = $userModel->getMonitorProfessors();
        
        // Buscar TODOS planejamentos de professores Monitores
        $plannings = $documentModel->getByMonitor();
        
        // Calcular estatísticas
        $stats = $this->calculateStats($professors, $plannings);
        
        // Buscar escolas para agrupamento
        $schools = $schoolModel->all();
        
        // Agrupar professores por escola
        $professorsBySchool = [];
        foreach ($professors as $prof) {
            $schoolId = $prof['school_id'] ?? 0;
            if (!isset($professorsBySchool[$schoolId])) {
                $professorsBySchool[$schoolId] = [
                    'school_name' => $prof['school_name'] ?? 'Sem escola',
                    'professors' => []
                ];
            }
            $professorsBySchool[$schoolId]['professors'][] = $prof;
        }
        
        // Importar RankingModel para buscar rankings
        require_once __DIR__ . '/../Models/RankingModel.php';
        $rankingModel = new RankingModel();
        
        // Obter filtro (annual, bimestral, monthly)
        $filter = $_GET['filter'] ?? 'annual';
        
        // Obter IDs das escolas com professores Monitores
        $schoolIds = array_unique(array_column($professors, 'school_id'));
        
        // Buscar rankings filtrados para Monitores
        $rankSchools = $rankingModel->getSchoolRanking($filter, null, $schoolIds);
        $rankProfessors = $rankingModel->getProfessorRanking($filter, null, $schoolIds, 'monitor');
        $rankCoordinators = $rankingModel->getCoordinatorRanking($filter, null, $schoolIds);
        
        require __DIR__ . '/../Views/supervisor_monitor/dashboard.php';
    }

    /**
     * Página dedicada de Listagem de Professores Monitores
     */
    public function professors() {
        $user = auth();
        
        if (!$user || $user['role'] !== 'supervisor_monitor') {
            redirect('login');
        }
        
        $userModel = new User();
        $schoolModel = new School();
        
        // Filtros
        $search = $_GET['search'] ?? '';
        $schoolId = $_GET['school_id'] ?? '';
        
        // 1. Buscar Professores Monitores
        $professors = $userModel->getMonitorProfessors();
        
        // 2. Aplicar Filtros (Search e School)
        if ($search || $schoolId) {
            $professors = array_filter($professors, function($p) use ($search, $schoolId) {
                // Filtro por Escola
                if ($schoolId && (!isset($p['school_id']) || $p['school_id'] != $schoolId)) {
                    return false;
                }
                
                // Filtro por Nome (Busca)
                if ($search) {
                    $nameMatch = stripos($p['name'], $search) !== false;
                    $emailMatch = stripos($p['email'], $search) !== false;
                    
                    // Busca também no nome da escola (se já não filtrou por ID)
                    $schoolNameMatch = isset($p['school_name']) && stripos($p['school_name'], $search) !== false;
                    
                    if (!$nameMatch && !$emailMatch && !$schoolNameMatch) {
                        return false;
                    }
                }
                
                return true;
            });
        }
        
        // 3. Buscar Coordenadores (Mapa)
        $coordinatorsMap = $userModel->getCoordinatorsMap();
        
        // 4. Buscar Escolas para o select
        $schools = $schoolModel->all();
        
        require __DIR__ . '/../Views/supervisor_monitor/professors_list.php';
    }
    
    /**
     * Calcula estatísticas dos professores Monitores
     */
    private function calculateStats($professors, $plannings) {
        $totalProfessors = count($professors);
        
        // Contar status dos planejamentos
        $sent = 0;
        $late = 0;
        $pending = 0;
        $approved = 0;
        
        foreach ($plannings as $plan) {
            if ($plan['status'] === 'enviado') $sent++;
            if ($plan['status'] === 'atrasado') $late++;
            if ($plan['status'] === 'pendente') $pending++;
            if ($plan['status'] === 'aprovado') $approved++;
        }
        
        // Calcular taxa de pontualidade
        $onTime = 0;
        foreach ($plannings as $plan) {
            if (isset($plan['submitted_at']) && isset($plan['deadline'])) {
                if (strtotime($plan['submitted_at']) <= strtotime($plan['deadline'])) {
                    $onTime++;
                }
            }
        }
        
        $punctualityRate = count($plannings) > 0 
            ? round(($onTime / count($plannings)) * 100, 1) 
            : 0;
        
        return [
            'total_professors' => $totalProfessors,
            'total_plannings' => count($plannings),
            'sent' => $sent,
            'late' => $late,
            'pending' => $pending,
            'approved' => $approved,
            'punctuality_rate' => $punctualityRate
        ];
    }
    
    /**
     * Visualizar detalhes de um planejamento
     */
    public function viewPlanning() {
        $user = auth();
        
        if (!$user || $user['role'] !== 'supervisor_monitor') {
            redirect('login');
        }
        
        $planningId = $_GET['id'] ?? null;
        
        if (!$planningId) {
            $_SESSION['error'] = 'Planejamento não especificado.';
            redirect('supervisor-monitor/dashboard');
        }
        
        $documentModel = new Document();
        $planning = $documentModel->findById($planningId);
        
        if (!$planning) {
            $_SESSION['error'] = 'Planejamento não encontrado.';
            redirect('supervisor-monitor/dashboard');
        }
        
        // Verificar se é de professor Monitor
        $userModel = new User();
        $professor = $userModel->findById($planning['user_id']);
        
        if (!$professor || !$professor['is_monitor']) {
            $_SESSION['error'] = 'Acesso negado. Este planejamento não é de um Monitor.';
            redirect('supervisor-monitor/dashboard');
        }
        
        require __DIR__ . '/../Views/supervisor_monitor/planning_detail.php';
    }
    
    /**
     * Página dedicada de Listagem de Planejamentos com filtros
     */
    public function plannings() {
        $user = auth();
        if (!$user || $user['role'] !== 'supervisor_monitor') redirect('login');
        
        $docModel = new Document();
        $schoolModel = new School();
        require_once __DIR__ . '/../Models/Planning.php';
        $planningModel = new Planning();

        // Filtros
        $schoolId = $_GET['school_id'] ?? '';
        $periodName = $_GET['period_name'] ?? '';
        $status = $_GET['status'] ?? '';

        // Carregar opções
        $schools = $schoolModel->all();
        $periods = $planningModel->getUniqueNamesMonitor(); 
        
        // Buscar Tudo
        $allDocs = $docModel->getByMonitor();
        
        // Filtrar no PHP
        $plannings = array_filter($allDocs, function($doc) use ($schoolId, $periodName, $status) {
            // Filtro por Escola
            if ($schoolId && (!isset($doc['school_id']) || $doc['school_id'] != $schoolId)) {
                return false;
            }
            // Filtro por Nome do Periodo
            if ($periodName && (!isset($doc['period_name']) || $doc['period_name'] != $periodName)) {
                return false;
            }
            // Filtro por Status
            if ($status && isset($doc['status']) && strtolower($doc['status']) != strtolower($status)) {
                return false;
            }
            return true;
        });

        require __DIR__ . '/../Views/supervisor_monitor/plannings_list.php';
    }

    /**
     * Obter relatório de pontualidade dos professores Monitores
     */
    public function punctualityReport() {
        $user = auth();
        
        if (!$user || $user['role'] !== 'supervisor_monitor') {
            redirect('login');
        }
        
        $documentModel = new Document();
        $userModel = new User();
        $schoolModel = new School();
        
        // Buscar todas as escolas para o filtro
        $schools = $schoolModel->all();
        
        // Capturar filtros
        $schoolId = $_GET['school_id'] ?? '';
        $professorId = $_GET['professor_id'] ?? '';
        $period = $_GET['period'] ?? 'annual';
        
        // Buscar professores monitores da escola selecionada (para o dropdown)
        $professors = [];
        if ($schoolId) {
            $allMonitors = $userModel->getMonitorProfessors();
            $professors = array_filter($allMonitors, function($p) use ($schoolId) {
                return isset($p['school_id']) && $p['school_id'] == $schoolId;
            });
        }
        
        // Se professor específico selecionado, mostrar dashboard individual
        if ($professorId) {
            // Buscar dados do professor
            $selectedProf = $userModel->findById($professorId);
            
            // Validar se é monitor
            if (!$selectedProf || !$selectedProf['is_monitor']) {
                $_SESSION['error'] = 'Professor não é monitor.';
                redirect('supervisor-monitor/punctuality_report');
            }
            
            // Buscar todos os documentos do professor
            $allSubmissions = $documentModel->getByUserId($professorId);
            
            // Filtrar por período se necessário
            $submissions = $allSubmissions;
            if ($period === 'monthly') {
                $currentMonth = date('m');
                $currentYear = date('Y');
                $submissions = array_filter($allSubmissions, function($sub) use ($currentMonth, $currentYear) {
                    return date('m', strtotime($sub['submitted_at'])) == $currentMonth 
                        && date('Y', strtotime($sub['submitted_at'])) == $currentYear;
                });
            } elseif ($period === 'bimonthly') {
                $currentBimester = ceil(date('m') / 2);
                $currentYear = date('Y');
                $submissions = array_filter($allSubmissions, function($sub) use ($currentBimester, $currentYear) {
                    $subBimester = ceil(date('m', strtotime($sub['submitted_at'])) / 2);
                    return $subBimester == $currentBimester 
                        && date('Y', strtotime($sub['submitted_at'])) == $currentYear;
                });
            }
            
            // Calcular estatísticas
            $stats = [
                'total_sent' => count($submissions),
                'on_time' => 0,
                'late_docs' => 0,
                'approved' => 0,
                'adjusted' => 0,
                'rejected' => 0
            ];
            
            foreach ($submissions as $sub) {
                // Pontualidade
                if (isset($sub['submitted_at']) && isset($sub['deadline'])) {
                    if (strtotime($sub['submitted_at']) <= strtotime($sub['deadline'])) {
                        $stats['on_time']++;
                    } else {
                        $stats['late_docs']++;
                    }
                }
                
                // Status
                if (isset($sub['status'])) {
                    if ($sub['status'] === 'aprovado') $stats['approved']++;
                    elseif ($sub['status'] === 'ajustado') $stats['adjusted']++;
                    elseif ($sub['status'] === 'rejeitado') $stats['rejected']++;
                }
            }
            
            $data = [
                'stats' => $stats,
                'submissions' => $submissions
            ];
            
            require __DIR__ . '/../Views/supervisor_monitor/punctuality_report.php';
            return;
        }
        
        // Buscar todos professores Monitores (para listagem geral)
        $allProfessors = $userModel->getMonitorProfessors();
        
        if ($schoolId) {
            $allProfessors = array_filter($allProfessors, function($p) use ($schoolId) {
                return isset($p['school_id']) && $p['school_id'] == $schoolId;
            });
        }
        
        $report = [];
        
        foreach ($allProfessors as $prof) {
            $planningsData = $documentModel->getByUserId($prof['id']);
            
            $onTime = 0;
            $total = count($planningsData);
            
            foreach ($planningsData as $plan) {
                if (isset($plan['submitted_at']) && isset($plan['deadline'])) {
                    if (strtotime($plan['submitted_at']) <= strtotime($plan['deadline'])) {
                        $onTime++;
                    }
                }
            }
            
            $rate = $total > 0 ? round(($onTime / $total) * 100, 1) : 0;
            
            $report[] = [
                'professor_id' => $prof['id'],
                'professor_name' => $prof['name'],
                'school_name' => $prof['school_name'] ?? 'N/A',
                'total_plannings' => $total,
                'on_time' => $onTime,
                'punctuality_rate' => $rate,
                'whatsapp' => $prof['whatsapp'] ?? null
            ];
        }
        
        // Ordenar por taxa (menor primeiro)
        usort($report, function($a, $b) {
            return $a['punctuality_rate'] <=> $b['punctuality_rate'];
        });
        
        require __DIR__ . '/../Views/supervisor_monitor/punctuality_report.php';
    }

    /**
     * Upload de foto de perfil
     */
    public function uploadPhoto() {
        $user = auth();
        
        if (!$user || $user['role'] !== 'supervisor_monitor') {
            redirect('login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
            
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $file = $_FILES['photo'];
            $fileName = time() . '_' . $user['id'] . '_' . basename($file['name']);
            $targetFile = $uploadDir . $fileName;
            
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (in_array($file['type'], $allowedTypes)) {
                if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                    $userModel = new User();
                    $userModel->updateProfilePhoto($user['id'], $fileName);
                    
                    $_SESSION['user']['profile_photo'] = $fileName;
                    $_SESSION['success'] = 'Foto atualizada com sucesso!';
                }
            }
        }
        
        redirect('supervisor-monitor/dashboard');
    }

}
