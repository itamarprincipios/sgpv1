<?php
/**
 * SupervisorEdFisController - Controlador para Supervisora SEMED de Educação Física
 * 
 * Funcionalidades:
 * - Dashboard com visão de TODOS os professores de Ed. Física da rede
 * - Estatísticas de envios e pontualidade
 * - Visualização de planejamentos (somente leitura)
 * - Sugestões pedagógicas via WhatsApp
 */

require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Document.php';
require_once __DIR__ . '/../Models/School.php';

class SupervisorEdFisController extends Controller {
    
    /**
     * Dashboard principal da Supervisora Ed. Física
     */
    public function dashboard() {
        $user = auth();
        
        // Verificar se é supervisor_edfis
        if (!$user || $user['role'] !== 'supervisor_edfis') {
            redirect('login');
        }
        
        $userModel = new User();
        $documentModel = new Document();
        $schoolModel = new School();
        
        // Buscar TODOS professores de Ed. Física
        $professors = $userModel->getPhysicalEducationProfessors();
        
        // Buscar TODOS planejamentos de professores Ed. Física
        $plannings = $documentModel->getByPhysicalEducation();
        
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
        
        require __DIR__ . '/../Views/supervisor_edfis/dashboard.php';
    }
    
    /**
     * Calcula estatísticas dos professores Ed. Física
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
        
        if (!$user || $user['role'] !== 'supervisor_edfis') {
            redirect('login');
        }
        
        $planningId = $_GET['id'] ?? null;
        
        if (!$planningId) {
            $_SESSION['error'] = 'Planejamento não especificado.';
            redirect('supervisor-edfis/dashboard');
        }
        
        $documentModel = new Document();
        $planning = $documentModel->findById($planningId);
        
        if (!$planning) {
            $_SESSION['error'] = 'Planejamento não encontrado.';
            redirect('supervisor-edfis/dashboard');
        }
        
        // Verificar se é de professor Ed. Física
        $userModel = new User();
        $professor = $userModel->findById($planning['user_id']);
        
        if (!$professor || !$professor['is_physical_education']) {
            $_SESSION['error'] = 'Acesso negado. Este planejamento não é de Educação Física.';
            redirect('supervisor-edfis/dashboard');
        }
        
        require __DIR__ . '/../Views/supervisor_edfis/planning_detail.php';
    }
    
    /**
     * Obter relatório de pontualidade dos professores Ed. Física
     */
    public function punctualityReport() {
        $user = auth();
        
        if (!$user || $user['role'] !== 'supervisor_edfis') {
            redirect('login');
        }
        
        $documentModel = new Document();
        $userModel = new User();
        
        // Buscar todos professores Ed. Física
        $professors = $userModel->getPhysicalEducationProfessors();
        
        $report = [];
        
        foreach ($professors as $prof) {
            $plannings = $documentModel->getByUserId($prof['id']);
            
            $onTime = 0;
            $total = count($plannings);
            
            foreach ($plannings as $plan) {
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
        
        // Ordenar por taxa de pontualidade (menor primeiro - quem precisa de mais atenção)
        usort($report, function($a, $b) {
            return $a['punctuality_rate'] <=> $b['punctuality_rate'];
        });
        
        require __DIR__ . '/../Views/supervisor_edfis/punctuality_report.php';
    }
}
