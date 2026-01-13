<?php

class Document extends Model {
    public function create($data) {
        $sql = "INSERT INTO documents (user_id, period_id, title, type, file_path, status, score_base, penalty_delay, score_final) 
                VALUES (:user_id, :period_id, :title, :type, :file_path, :status, :score_base, :penalty_delay, :score_final)";
        $this->db->query($sql, $data);
        return $this->db->getConnection()->lastInsertId();
    }

    public function findById($id) {
        return $this->db->query("SELECT * FROM documents WHERE id = :id", ['id' => $id])->fetch();
    }

    public function findByUserAndPeriod($userId, $periodId) {
        $sql = "SELECT * FROM documents WHERE user_id = :user_id AND period_id = :period_id LIMIT 1";
        return $this->db->query($sql, ['user_id' => $userId, 'period_id' => $periodId])->fetch();
    }

    public function getByUserId($userId) {
        $sql = "SELECT d.*, p.name as period_name 
                FROM documents d 
                JOIN periods p ON d.period_id = p.id 
                WHERE d.user_id = :user_id 
                ORDER BY d.submitted_at DESC";
        return $this->db->query($sql, ['user_id' => $userId])->fetchAll();
    }

    public function getActivePeriods() {
        return $this->db->query("SELECT * FROM periods WHERE is_active = 1")->fetchAll();
    }

    public function getBySchoolId($schoolId) {
        $sql = "SELECT d.*, u.name as professor_name, p.name as period_name 
                FROM documents d 
                JOIN users u ON d.user_id = u.id 
                JOIN periods p ON d.period_id = p.id 
                WHERE u.school_id = :school_id 
                ORDER BY d.submitted_at DESC";
        return $this->db->query($sql, ['school_id' => $schoolId])->fetchAll();
        return $this->db->query($sql, ['school_id' => $schoolId])->fetchAll();
    }

    public function getBySchoolIdWithFilters($schoolId, $filters = []) {
        $sql = "SELECT d.*, u.name as professor_name, p.name as period_name, p.bimester 
                FROM documents d 
                JOIN users u ON d.user_id = u.id 
                JOIN periods p ON d.period_id = p.id 
                WHERE u.school_id = :school_id";
        
        $params = ['school_id' => $schoolId];

        if (!empty($filters['period_id'])) {
            $sql .= " AND d.period_id = :period_id";
            $params['period_id'] = $filters['period_id'];
        }

        if (!empty($filters['professor_id'])) {
            $sql .= " AND d.user_id = :professor_id";
            $params['professor_id'] = $filters['professor_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND d.status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY d.submitted_at DESC";

        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getGlobalStats($schoolIds = []) {
        $whereClause = "";
        $whereClauseSchool = "";
        
        if (!empty($schoolIds)) {
            $placeholders = implode(',', array_map(function($id) { return intval($id); }, $schoolIds));
            $whereClause = " AND u.school_id IN ($placeholders)";
            $whereClauseSchool = " WHERE id IN ($placeholders)";
            $whereClausePeriod = " WHERE school_id IN ($placeholders)";
            $whereClauseUser = " AND school_id IN ($placeholders)";
        }

        $stats = [
            'total_docs' => $this->db->query("SELECT COUNT(DISTINCT d.id) as count FROM documents d LEFT JOIN users u ON d.user_id = u.id WHERE 1=1 $whereClause")->fetch()['count'],
            'total_professors' => $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'professor' " . ($whereClauseUser ?? ""))->fetch()['count'],
            'total_schools' => $this->db->query("SELECT COUNT(*) as count FROM schools " . ($whereClauseSchool ?? ""))->fetch()['count'],
            'total_plannings' => $this->db->query("SELECT COUNT(*) as count FROM periods " . ($whereClausePeriod ?? ""))->fetch()['count'],
        ];
        return $stats;
    }

    public function getMonthlyStats($schoolIds = []) {
        $whereClause = "";
        if (!empty($schoolIds)) {
            $placeholders = implode(',', array_map(function($id) { return intval($id); }, $schoolIds));
            $whereClause = "AND u.school_id IN ($placeholders)";
        }

        return $this->db->query("
            SELECT DATE_FORMAT(d.submitted_at, '%Y-%m') as month, COUNT(*) as count 
            FROM documents d
            LEFT JOIN users u ON d.user_id = u.id
            WHERE d.submitted_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            $whereClause
            GROUP BY month 
            ORDER BY month ASC
        ")->fetchAll();
    }

    public function getRankingSchools($schoolIds = []) {
        $whereClause = "";
        if (!empty($schoolIds)) {
            $placeholders = implode(',', array_map(function($id) { return intval($id); }, $schoolIds));
            // Explicitly qualify 's.id' to avoid ambiguity if joins are added later, though here it is 's' alias
            $whereClause = "WHERE s.id IN ($placeholders)";
        }

        // Ranking baseado em % de entrega em relacao ao prazo (SIMPLIFICADO para MVP: Count of on-time)
        // Precisamos comparar submitted_at com deadline.
        $sql = "SELECT s.name as school_name, 
                       COUNT(d.id) as total_docs,
                       SUM(CASE WHEN d.status = 'enviado' AND d.submitted_at <= p.deadline THEN 1 ELSE 0 END) as on_time_docs
                FROM schools s
                LEFT JOIN users u ON s.id = u.school_id
                LEFT JOIN documents d ON u.id = d.user_id
                LEFT JOIN periods p ON d.period_id = p.id
                $whereClause
                GROUP BY s.id
                ORDER BY on_time_docs DESC, total_docs DESC
                LIMIT 3";
        return $this->db->query($sql)->fetchAll();
    }

    public function getDocumentStatsBySchool($schoolIds = []) {
        $whereClause = "";
        if (!empty($schoolIds)) {
            $placeholders = implode(',', array_map(function($id) { return intval($id); }, $schoolIds));
            $whereClause = "WHERE s.id IN ($placeholders)";
        }

        $sql = "SELECT 
                    s.name as school_name,
                    COUNT(d.id) as total_docs
                FROM schools s
                LEFT JOIN users u ON s.id = u.school_id
                LEFT JOIN documents d ON u.id = d.user_id
                $whereClause
                GROUP BY s.id, s.name
                ORDER BY total_docs DESC
                LIMIT 10";
        return $this->db->query($sql)->fetchAll();
    }

    public function updateStatus($id, $data) {
        $sql = "UPDATE documents SET 
                status = :status, 
                score_final = :score_final";
        
        $params = [
            'id' => $id,
            'status' => $data['status'],
            'score_final' => $data['score_final']
        ];

        if (isset($data['rejection_count'])) {
            $sql .= ", rejection_count = :rejection_count, 
                       rejection_reason = :rejection_reason, 
                       rejected_at = :rejected_at, 
                       penalty_resubmission = :penalty_resubmission";
            $params['rejection_count'] = $data['rejection_count'];
            $params['rejection_reason'] = $data['rejection_reason'] ?? null;
            $params['rejected_at'] = $data['rejected_at'];
            $params['penalty_resubmission'] = $data['penalty_resubmission'];
        }

        $sql .= " WHERE id = :id";
        return $this->db->query($sql, $params);
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM documents WHERE id = :id", ['id' => $id]);
    }

    public function getAllWithFilters($filters = []) {
        $sql = "SELECT d.*, u.name as professor_name, s.name as school_name, p.name as planning_name, p.bimester 
                FROM documents d 
                JOIN users u ON d.user_id = u.id 
                JOIN schools s ON u.school_id = s.id 
                JOIN periods p ON d.period_id = p.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['school_id'])) {
            $sql .= " AND u.school_id = :school_id";
            $params['school_id'] = $filters['school_id'];
        }
        
        if (!empty($filters['bimester'])) {
            $sql .= " AND p.bimester = :bimester";
            $params['bimester'] = $filters['bimester'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND d.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['professor_id'])) {
            $sql .= " AND d.user_id = :professor_id";
            $params['professor_id'] = $filters['professor_id'];
        }
        
        $sql .= " ORDER BY d.submitted_at DESC";
        
        return $this->db->query($sql, $params)->fetchAll();
    }


    public function getSubmissionsReport($schoolId = null) {
        $sql = "SELECT s.name as school_name, u.name as professor_name, u.id as professor_id,
                       COUNT(d.id) as total_sent,
                       SUM(CASE WHEN d.status = 'aprovado' THEN 1 ELSE 0 END) as approved,
                       SUM(CASE WHEN d.status = 'rejeitado' THEN 1 ELSE 0 END) as rejected,
                       SUM(CASE WHEN d.submitted_at > p.deadline THEN 1 ELSE 0 END) as late_docs
                FROM users u
                JOIN schools s ON u.school_id = s.id
                LEFT JOIN documents d ON u.id = d.user_id
                LEFT JOIN periods p ON d.period_id = p.id
                WHERE u.role = 'professor'";
        
        if ($schoolId) {
            $sql .= " AND s.id = :school_id";
            return $this->db->query($sql . " GROUP BY u.id ORDER BY s.name, u.name", ['school_id' => $schoolId])->fetchAll();
        }
        
        return $this->db->query($sql . " GROUP BY u.id ORDER BY s.name, u.name")->fetchAll();
    }

    public function getProfessorStats($professorId, $periodFilter = 'annual') {
        // Base query for stats
        $sqlStats = "SELECT 
                        COUNT(d.id) as total_sent,
                        SUM(CASE WHEN d.status = 'aprovado' THEN 1 ELSE 0 END) as approved,
                        SUM(CASE WHEN d.status = 'rejeitado' THEN 1 ELSE 0 END) as rejected,
                        SUM(CASE WHEN d.status = 'ajustado' THEN 1 ELSE 0 END) as adjusted,
                        SUM(CASE WHEN d.submitted_at <= p.deadline THEN 1 ELSE 0 END) as on_time,
                        SUM(CASE WHEN d.submitted_at > p.deadline THEN 1 ELSE 0 END) as late_docs
                     FROM documents d
                     JOIN periods p ON d.period_id = p.id
                     WHERE d.user_id = :user_id";

        // Base query for submissions list
         $sqlList = "SELECT d.*, p.name as period_name, p.deadline
                     FROM documents d
                     JOIN periods p ON d.period_id = p.id
                     WHERE d.user_id = :user_id";

        $params = ['user_id' => $professorId];

        if ($periodFilter === 'monthly') {
            $currentMonth = date('Y-m');
            $filterSql = " AND DATE_FORMAT(d.submitted_at, '%Y-%m') = :month";
            $sqlStats .= $filterSql;
            $sqlList .= $filterSql;
            $params['month'] = $currentMonth;
        } elseif ($periodFilter === 'bimonthly') {
             // Assuming bimester field in periods table or logic based on current date
             // For MVP, lets just use last 60 days or filter by specific bimester if param passed
             // Using simple date range for now as 'bimonthly' usually implies current bimester
        }

        $sqlList .= " ORDER BY d.submitted_at DESC";

        return [
            'stats' => $this->db->query($sqlStats, $params)->fetch(),
            'submissions' => $this->db->query($sqlList, $params)->fetchAll()
        ];
    }

    public function getGlobalPendencies($schoolId = null) {
        // This query finds periods where a professor HAS NOT submitted anything yet and the deadline is past
        $sql = "SELECT s.name as school_name, u.name as professor_name, p.name as period_name, p.deadline,
                       DATEDIFF(NOW(), p.deadline) as days_late
                FROM users u
                JOIN schools s ON u.school_id = s.id
                CROSS JOIN periods p
                LEFT JOIN documents d ON u.id = d.user_id AND p.id = d.period_id
                WHERE u.role = 'professor' 
                  AND p.deadline < NOW() 
                  AND d.id IS NULL
                  AND p.school_id = u.school_id";
        
        if ($schoolId) {
            $sql .= " AND s.id = :school_id";
            return $this->db->query($sql . " ORDER BY days_late DESC", ['school_id' => $schoolId])->fetchAll();
        }
        
        return $this->db->query($sql . " ORDER BY days_late DESC")->fetchAll();
    }

    public function getSchoolPunctuality() {
        $sql = "SELECT s.name as school_name, AVG(d.score_final) as avg_score, COUNT(d.id) as total_docs
                FROM schools s
                JOIN users u ON s.id = u.school_id
                JOIN documents d ON u.id = d.user_id
                WHERE d.status IN ('aprovado', 'ajustado', 'enviado')
                GROUP BY s.id
                ORDER BY avg_score DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getProfessorPunctualityBySchool($schoolId) {
        $sql = "SELECT u.name as professor_name, u.profile_photo, AVG(d.score_final) as avg_score, COUNT(d.id) as total_docs
                FROM users u
                JOIN documents d ON u.id = d.user_id
                WHERE u.school_id = :school_id
                  AND d.status IN ('aprovado', 'ajustado', 'enviado')
                GROUP BY u.id
                ORDER BY avg_score DESC";
        return $this->db->query($sql, ['school_id' => $schoolId])->fetchAll();
    }
    public function search($filters = []) {
        $sql = "SELECT d.*, u.name as professor_name, s.name as school_name, p.name as period_name, p.opening_date 
                FROM documents d 
                JOIN users u ON d.user_id = u.id 
                LEFT JOIN schools s ON u.school_id = s.id 
                JOIN periods p ON d.period_id = p.id 
                WHERE 1=1";
        
        $params = [];

        if (!empty($filters['year'])) {
            $sql .= " AND YEAR(p.opening_date) = :year";
            $params['year'] = $filters['year'];
        }

        if (!empty($filters['school_id'])) {
            $sql .= " AND u.school_id = :school_id";
            $params['school_id'] = $filters['school_id'];
        }

        if (!empty($filters['professor_id'])) {
            $sql .= " AND d.user_id = :professor_id";
            $params['professor_id'] = $filters['professor_id'];
        }

        if (!empty($filters['class_id'])) {
            $sql .= " AND u.class_id = :class_id";
            $params['class_id'] = $filters['class_id'];
        }

        $sql .= " ORDER BY d.submitted_at DESC";

        return $this->db->query($sql, $params)->fetchAll();
    }

    /**
     * Busca TODOS os planejamentos de professores de Educação Física
     * @return array Lista de documentos com dados do professor, escola e período
     */
    public function getByPhysicalEducation() {
        $sql = "SELECT d.*, 
                       u.name as professor_name,
                       u.school_id,
                       s.name as school_name,
                       p.name as period_name,
                       p.deadline
                FROM documents d
                JOIN users u ON d.user_id = u.id
                LEFT JOIN schools s ON u.school_id = s.id
                JOIN periods p ON d.period_id = p.id
                WHERE u.role = 'professor'
                AND u.is_physical_education = 1
                ORDER BY d.submitted_at DESC";
        return $this->db->query($sql)->fetchAll();
    }
}
