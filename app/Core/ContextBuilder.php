<?php
/**
 * ContextBuilder - Constrói contexto pedagógico para alimentar a IA
 * 
 * Esta classe recupera dados relevantes do banco de dados e formata
 * em um pacote de contexto estruturado para a IA analisar.
 */

require_once __DIR__ . '/../Core/Model.php';
require_once __DIR__ . '/../Models/Document.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/School.php';

class ContextBuilder {
    
    /**
     * Recupera contexto completo de uma escola
     * @param int $schoolId ID da escola
     * @return array Contexto estruturado da escola
     */
    public function getSchoolContext($schoolId) {
        $schoolModel = new School();
        $documentModel = new Document();
        $userModel = new User();
        
        // Buscar dados da escola
        $school = $schoolModel->findById($schoolId);
        if (!$school) {
            throw new Exception("Escola não encontrada");
        }
        
        // Buscar planejamentos da escola
        $documents = $documentModel->getBySchoolId($schoolId);
        
        // Buscar professores da escola
        $professors = $userModel->getBySchoolId($schoolId, 'professor');
        
        // Buscar coordenadores da escola
        $coordinators = $userModel->getBySchoolId($schoolId, 'coordinator');
        
        return [
            'tipo' => 'escola',
            'escola' => [
                'nome' => $school['name'],
                'endereco' => $school['address'] ?? 'Não informado',
                'codigo_inep' => $school['inep_code'] ?? 'N/A',
                'diretor' => $school['director_name'] ?? 'Não informado',
                'telefone_diretor' => $school['director_phone'] ?? 'Não informado'
            ],
            'coordenadores' => $this->extractCoordinatorsInfo($coordinators),
            'estatisticas' => [
                'total_professores' => count($professors),
                'total_coordenadores' => count($coordinators),
                'total_planejamentos' => count($documents),
                'planejamentos_enviados' => $this->countByStatus($documents, 'enviado'),
                'planejamentos_aprovados' => $this->countByStatus($documents, 'aprovado'),
                'planejamentos_rejeitados' => $this->countByStatus($documents, 'rejeitado')
            ],
            'professores' => $this->extractProfessorsInfo($professors),
            'planejamentos_recentes' => $this->extractPlanningsInfo(array_slice($documents, 0, 10))
        ];
    }
    
    /**
     * Recupera contexto de um professor específico
     * @param int $professorId ID do professor
     * @param int|null $periodId ID do período (opcional)
     * @return array Contexto estruturado do professor
     */
    public function getProfessorContext($professorId, $periodId = null) {
        $userModel = new User();
        $documentModel = new Document();
        
        // Buscar dados do professor
        $professor = $userModel->findById($professorId);
        if (!$professor) {
            throw new Exception("Professor não encontrado");
        }
        
        // Buscar planejamentos do professor
        $documents = $documentModel->getByUserId($professorId);
        
        // Filtrar por período se especificado
        if ($periodId) {
            $documents = array_filter($documents, function($doc) use ($periodId) {
                return $doc['period_id'] == $periodId;
            });
        }
        
        return [
            'tipo' => 'professor',
            'professor' => [
                'nome' => $professor['name'],
                'email' => $professor['email'],
                'whatsapp' => $professor['whatsapp'] ?? 'Não informado',
                'turma' => $professor['class_name'] ?? 'N/A',
                'escola' => $professor['school_name'] ?? 'N/A',
                'educacao_fisica' => $professor['is_physical_education'] ? 'Sim' : 'Não'
            ],
            'estatisticas' => [
                'total_planejamentos' => count($documents),
                'enviados' => $this->countByStatus($documents, 'enviado'),
                'aprovados' => $this->countByStatus($documents, 'aprovado'),
                'rejeitados' => $this->countByStatus($documents, 'rejeitado')
            ],
            'planejamentos' => $this->extractPlanningsInfo($documents)
        ];
    }
    
    /**
     * Recupera contexto de uma turma específica
     * @param int $classId ID da turma
     * @return array Contexto estruturado da turma
     */
    public function getClassContext($classId) {
        $userModel = new User();
        $documentModel = new Document();
        
        // Buscar professores da turma
        $professors = $userModel->getByClassId($classId);
        
        $plannings = [];
        foreach ($professors as $prof) {
            $docs = $documentModel->getByUserId($prof['id']);
            $plannings = array_merge($plannings, $docs);
        }
        
        return [
            'tipo' => 'turma',
            'turma' => [
                'nome' => $professors[0]['class_name'] ?? 'N/A',
                'escola' => $professors[0]['school_name'] ?? 'N/A'
            ],
            'professores' => $this->extractProfessorsInfo($professors),
            'planejamentos' => $this->extractPlanningsInfo($plannings)
        ];
    }
    
    /**
     * Recupera contexto agregado de múltiplas escolas
     * @param array $schoolIds Array de IDs das escolas
     * @return array Contexto estruturado multi-escola
     */
    public function getMultiSchoolContext($schoolIds) {
        $schoolModel = new School();
        $documentModel = new Document();
        $userModel = new User();
        
        $schools = [];
        $allProfessors = [];
        $allDocuments = [];
        $allCoordinators = [];
        
        foreach ($schoolIds as $schoolId) {
            $school = $schoolModel->findById($schoolId);
            if (!$school) continue;
            
            $schools[] = $school;
            
            // Professores da escola
            $professors = $userModel->getBySchoolId($schoolId, 'professor');
            foreach ($professors as &$prof) {
                $prof['school_name'] = $school['name'];
            }
            $allProfessors = array_merge($allProfessors, $professors);
            
            // Coordenadores da escola
            $coordinators = $userModel->getBySchoolId($schoolId, 'coordinator');
            foreach ($coordinators as &$coord) {
                $coord['school_name'] = $school['name'];
            }
            $allCoordinators = array_merge($allCoordinators, $coordinators);
            
            // Documentos da escola
            $docs = $documentModel->getBySchoolId($schoolId);
            foreach ($docs as &$doc) {
                $doc['school_name'] = $school['name'];
            }
            $allDocuments = array_merge($allDocuments, $docs);
        }
        
        return [
            'tipo' => 'multi_escola',
            'descricao' => 'Contexto agregado de ' . count($schools) . ' escola(s)',
            'escolas' => $this->extractSchoolsInfo($schools),
            'estatisticas' => [
                'total_escolas' => count($schools),
                'total_professores' => count($allProfessors),
                'total_coordenadores' => count($allCoordinators),
                'total_planejamentos' => count($allDocuments),
                'planejamentos_enviados' => $this->countByStatus($allDocuments, 'enviado'),
                'planejamentos_aprovados' => $this->countByStatus($allDocuments, 'aprovado'),
                'planejamentos_rejeitados' => $this->countByStatus($allDocuments, 'rejeitado')
            ],
            'professores' => $this->extractProfessorsInfo($allProfessors),
            'coordenadores' => $this->extractCoordinatorsInfo($allCoordinators),
            'planejamentos_recentes' => $this->extractPlanningsInfo(array_slice($allDocuments, 0, 20))
        ];
    }
    
    /**
     * Recupera contexto global da rede municipal
     * @return array Contexto estruturado da rede
     */
    public function getNetworkContext() {
        $documentModel = new Document();
        $schoolModel = new School();
        $userModel = new User();
        
        $stats = $documentModel->getGlobalStats();
        $schools = $schoolModel->all();
        
        // Buscar todos os coordenadores
        $coordinators = $userModel->getByRole('coordinator');
        
        // Buscar todos os professores
        $professors = $userModel->getByRole('professor');
        
        return [
            'tipo' => 'rede_municipal',
            'estatisticas_globais' => [
                'total_escolas' => $stats['total_schools'],
                'total_professores' => $stats['total_professors'],
                'total_coordenadores' => count($coordinators),
                'total_planejamentos' => $stats['total_docs'],
                'total_periodos' => $stats['total_plannings']
            ],
            'escolas' => $this->extractSchoolsInfo($schools),
            'coordenadores' => $this->extractCoordinatorsInfo($coordinators),
            'professores_resumo' => array_slice($this->extractProfessorsInfo($professors), 0, 20)
        ];
    }
    
    /**
     * Recupera contexto específico de Educação Física da rede
     * @return array Contexto estruturado de Educação Física
     */
    public function getPhysicalEducationContext() {
        $userModel = new User();
        $documentModel = new Document();
        
        // Buscar professores de Ed. Física
        $professors = $userModel->getPhysicalEducationProfessors();
        
        // Buscar planejamentos de Ed. Física
        $plannings = $documentModel->getByPhysicalEducation();
        
        // Estatísticas específicas
        $totalProfessors = count($professors);
        $totalPlannings = count($plannings);
        $enviados = $this->countByStatus($plannings, 'enviado');
        $atrasados = $this->countByStatus($plannings, 'atrasado');
        $aprovados = $this->countByStatus($plannings, 'aprovado');
        
        return [
            'tipo' => 'rede_educacao_fisica',
            'descricao' => 'Contexto filtrado apenas para professores e planejamentos de Educação Física da rede municipal.',
            'estatisticas_edfis' => [
                'total_professores' => $totalProfessors,
                'total_planejamentos' => $totalPlannings,
                'enviados' => $enviados,
                'atrasados' => $atrasados,
                'aprovados' => $aprovados,
                'taxa_entrega' => $totalProfessors > 0 ? round(($enviados / $totalProfessors) * 100, 1) . '%' : '0%'
            ],
            'professores' => $this->extractProfessorsInfo($professors),
            'planejamentos_recentes' => $this->extractPlanningsInfo(array_slice($plannings, 0, 20))
        ];
    }
    
    /**
     * Extrai informações resumidas dos professores
     * @param array $professors Lista de professores
     * @return array Informações formatadas
     */
    private function extractProfessorsInfo($professors) {
        return array_map(function($prof) {
            return [
                'nome' => $prof['name'],
                'email' => $prof['email'] ?? 'Não informado',
                'whatsapp' => $prof['whatsapp'] ?? 'Não informado',
                'escola' => $prof['school_name'] ?? 'N/A',
                'turma' => $prof['class_name'] ?? 'N/A',
                'educacao_fisica' => isset($prof['is_physical_education']) && $prof['is_physical_education'] ? 'Sim' : 'Não'
            ];
        }, $professors);
    }
    
    /**
     * Extrai informações dos coordenadores
     * @param array $coordinators Lista de coordenadores
     * @return array Informações formatadas
     */
    private function extractCoordinatorsInfo($coordinators) {
        return array_map(function($coord) {
            return [
                'nome' => $coord['name'],
                'email' => $coord['email'] ?? 'Não informado',
                'whatsapp' => $coord['whatsapp'] ?? 'Não informado',
                'escola' => $coord['school_name'] ?? 'Múltiplas escolas'
            ];
        }, $coordinators);
    }
    
    /**
     * Extrai informações completas das escolas
     * @param array $schools Lista de escolas
     * @return array Informações formatadas
     */
    private function extractSchoolsInfo($schools) {
        return array_map(function($school) {
            return [
                'nome' => $school['name'],
                'endereco' => $school['address'] ?? 'Não informado',
                'codigo_inep' => $school['inep_code'] ?? 'N/A',
                'diretor' => $school['director_name'] ?? 'Não informado',
                'telefone_diretor' => $school['director_phone'] ?? 'Não informado'
            ];
        }, $schools);
    }
    
    /**
     * Extrai informações resumidas dos planejamentos
     * @param array $documents Lista de documentos
     * @return array Informações formatadas
     */
    private function extractPlanningsInfo($documents) {
        return array_map(function($doc) {
            $info = [
                'titulo' => $doc['title'],
                'periodo' => $doc['period_name'] ?? 'N/A',
                'professor' => $doc['professor_name'] ?? $doc['user_name'] ?? 'N/A',
                'status' => $doc['status'],
                'data_envio' => date('d/m/Y', strtotime($doc['submitted_at']))
            ];
            
            // Incluir conteúdo do documento se disponível (limitado para não sobrecarregar)
            if (!empty($doc['content_text'])) {
                // Limitar a 1500 caracteres para não exceder limite de tokens da IA
                $content = substr($doc['content_text'], 0, 1500);
                if (strlen($doc['content_text']) > 1500) {
                    $content .= '... [conteúdo truncado]';
                }
                $info['conteudo'] = $content;
            }
            
            return $info;
        }, $documents);
    }
    
    /**
     * Conta documentos por status
     * @param array $documents Lista de documentos
     * @param string $status Status a contar
     * @return int Quantidade
     */
    private function countByStatus($documents, $status) {
        return count(array_filter($documents, function($doc) use ($status) {
            return $doc['status'] === $status;
        }));
    }
}
