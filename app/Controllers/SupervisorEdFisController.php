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
     * Página dedicada de Listagem de Professores
     */
    public function professors() {
        $user = auth();
        
        if (!$user || $user['role'] !== 'supervisor_edfis') {
            redirect('login');
        }
        
        $userModel = new User();
        $schoolModel = new School();
        
        // Filtros
        $search = $_GET['search'] ?? '';
        $schoolId = $_GET['school_id'] ?? '';
        
        // 1. Buscar Professores
        $professors = $userModel->getPhysicalEducationProfessors();
        
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
        
        require __DIR__ . '/../Views/supervisor_edfis/professors_list.php';
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
     * Página dedicada de Listagem de Planejamentos com filtros
     */
    public function plannings() {
        $user = auth();
        if (!$user || $user['role'] !== 'supervisor_edfis') redirect('login');
        
        $docModel = new Document();
        $schoolModel = new School();
        require_once __DIR__ . '/../Models/Planning.php';
        $planningModel = new Planning();

        // Filtros
        $schoolId = $_GET['school_id'] ?? '';
        $periodId = $_GET['period_id'] ?? ''; // Aqui usaremos ID para simplificar se o select mandar ID, mas idealmente seria Name se quisermos multi-school. 
        // OBS: No view anterior eu coloquei value="<?= $period['id'] ?>".
        // Se eu quiser filtrar por nome (multiescola), tenho que receber o NOME ou mudar a lógica.
        // Vamos ajustar para receber o NOME caso o filtro select mande o nome? Nao, os IDs sao unicos.
        // Mas se eu selecionar "1 Bimestre" no select (value=ID do 1 Bimestre da Escola X), e filtrar só por ID, vou ver só da Escola X.
        // Se eu quiser ver de TODAS, o select tem que ser de nomes unicos.
        
        // Vamos usar nomes únicos no select e filtrar por nome.
        // Alteração no View será necessária? Sim, value="<?= $period['name'] ?>"
        
        $periodName = $_GET['period_name'] ?? ''; // Mudança de estrategia
        $status = $_GET['status'] ?? '';

        // Carregar opções
        $schools = $schoolModel->all();
        $periods = $planningModel->getUniqueNamesPhysicalEducation(); // Retorna DISTINCT names
        
        // Buscar Tudo
        $allDocs = $docModel->getByPhysicalEducation();
        
        // Filtrar
        $plannings = array_filter($allDocs, function($doc) use ($schoolId, $periodName, $status) {
            if ($schoolId && $doc['school_id'] != $schoolId) return false;
            
            // Filtro por Nome do Periodo (para pegar de todas as escolas)
            if ($periodName && $doc['period_name'] != $periodName) return false;
            
            if ($status && strtolower($doc['status']) != strtolower($status)) return false;
            
            return true;
        });

        require __DIR__ . '/../Views/supervisor_edfis/plannings_list.php';
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
        $schoolModel = new School();
        
        // Buscar todas as escolas para o filtro
        $schools = $schoolModel->all();
        
        // Capturar filtro
        $schoolIdFilter = $_GET['school_id'] ?? '';
        
        // Buscar todos professores Ed. Física
        // Se tiver filtro, buscamos todos e filtramos no PHP ou poderíamos criar um método específico no Model
        // Pela simplicidade e volume atual, vamos filtrar aqui o array de professores
        $professors = $userModel->getPhysicalEducationProfessors();
        
        if ($schoolIdFilter) {
            $professors = array_filter($professors, function($p) use ($schoolIdFilter) {
                return isset($p['school_id']) && $p['school_id'] == $schoolIdFilter;
            });
        }
        
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

    /**
     * Upload de foto de perfil
     */
    public function uploadPhoto() {
        $user = auth();
        
        if (!$user || $user['role'] !== 'supervisor_edfis') {
            redirect('login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
            
            // Criar diretório se não existir
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $file = $_FILES['photo'];
            $fileName = time() . '_' . $user['id'] . '_' . basename($file['name']);
            $targetFile = $uploadDir . $fileName;
            
            // Validar tipo de arquivo
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (in_array($file['type'], $allowedTypes)) {
                if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                    // Atualizar banco
                    $userModel = new User();
                    $userModel->updateProfilePhoto($user['id'], $fileName);
                    
                    // Atualizar sessão
                    $_SESSION['user']['profile_photo'] = $fileName;
                    $_SESSION['success'] = 'Foto atualizada com sucesso!';
                }
            }
        }
        
        redirect('supervisor-edfis/dashboard');
    }

}
