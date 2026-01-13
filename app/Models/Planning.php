<?php

require_once __DIR__ . '/../Core/Model.php';

class Planning extends Model {
    public function create($data) {
        $sql = "INSERT INTO periods (name, description, start_date, end_date, deadline, opening_date, is_active, school_id, is_physical_education) 
                VALUES (:name, :description, :start_date, :end_date, :deadline, :opening_date, 1, :school_id, :is_physical_education)";
        return $this->db->query($sql, $data);
    }

    public function getBySchoolId($schoolId) {
        $sql = "SELECT * FROM periods WHERE school_id = :school_id ORDER BY id DESC";
        return $this->db->query($sql, ['school_id' => $schoolId])->fetchAll();
    }

    public function getBySchoolIdAndType($schoolId, $isPhysicalEducation) {
        $sql = "SELECT * FROM periods 
                WHERE school_id = :school_id 
                AND is_physical_education = :is_pe 
                ORDER BY id DESC";
        return $this->db->query($sql, [
            'school_id' => $schoolId, 
            'is_pe' => $isPhysicalEducation ? 1 : 0
        ])->fetchAll();
    }

    public function getReleasedBySchoolIdAndType($schoolId, $isPhysicalEducation) {
        // Regra: Liberado apenas se a data atual for maior ou igual à data de abertura
        $sql = "SELECT * FROM periods 
                WHERE school_id = :school_id 
                AND is_physical_education = :is_pe 
                AND NOW() >= opening_date
                ORDER BY deadline DESC";
        return $this->db->query($sql, [
            'school_id' => $schoolId, 
            'is_pe' => $isPhysicalEducation ? 1 : 0
        ])->fetchAll();
    }

    public function update($id, $data) {
        $sql = "UPDATE periods SET 
                    name = :name, 
                    description = :description, 
                    deadline = :deadline, 
                    opening_date = :opening_date, 
                    start_date = :start_date,
                    is_physical_education = :is_physical_education 
                WHERE id = :id";
        $data['id'] = $id;
        return $this->db->query($sql, $data);
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM periods WHERE id = :id", ['id' => $id]);
    }

    public function findById($id) {
        return $this->db->query("SELECT * FROM periods WHERE id = :id", ['id' => $id])->fetch();
    }

    public function getPlanningStats($periodId, $schoolId, $isPE = 0) {
        if ($isPE) {
            // Caso Educação Física: Mostra apenas professores de Ed. Física
            $sql = "SELECT 
                        COALESCE(c.name, 'Educação Física') as class_name, 
                        u.name as professor_name, 
                        u.whatsapp,
                        d.status,
                        d.submitted_at,
                        d.file_path,
                        d.id
                    FROM users u
                    LEFT JOIN classes c ON u.class_id = c.id
                    LEFT JOIN documents d ON u.id = d.user_id AND d.period_id = :period_id
                    WHERE u.school_id = :school_id 
                    AND u.role = 'professor'
                    AND u.is_physical_education = 1
                    ORDER BY c.name, u.name";
        } else {
            // Caso Regular: Mantém original (centrado em turmas) mas ignora profs de Ed. Física
            $sql = "SELECT 
                        c.name as class_name, 
                        u.name as professor_name, 
                        u.whatsapp,
                        d.status,
                        d.submitted_at,
                        d.file_path,
                        d.id
                    FROM classes c
                    LEFT JOIN users u ON c.id = u.class_id 
                        AND u.role = 'professor' 
                        AND (u.is_physical_education = 0 OR u.is_physical_education IS NULL)
                    LEFT JOIN documents d ON u.id = d.user_id AND d.period_id = :period_id
                    WHERE c.school_id = :school_id
                    ORDER BY c.name, u.name";
        }
        
        return $this->db->query($sql, [
            'school_id' => $schoolId, 
            'period_id' => $periodId
        ])->fetchAll();
    }

    public function updateBimester($id, $bimester) {
        return $this->db->query("UPDATE periods SET bimester = :bimester WHERE id = :id", [
            'id' => $id,
            'bimester' => $bimester
        ]);
    }

    public function getPendingSubmissions($schoolId) {
        $sql = "SELECT 
                    p.name as planning_name,
                    p.deadline,
                    u.name as professor_name,
                    u.whatsapp,
                    COALESCE(c.name, 'Educação Física') as class_name
                FROM periods p
                JOIN users u ON u.school_id = p.school_id AND u.role = 'professor'
                     AND (
                         (p.is_physical_education = 1 AND u.is_physical_education = 1)
                         OR 
                         (p.is_physical_education = 0 AND (u.is_physical_education = 0 OR u.is_physical_education IS NULL))
                     )
                LEFT JOIN classes c ON u.class_id = c.id
                LEFT JOIN documents d ON d.period_id = p.id AND d.user_id = u.id
                WHERE p.school_id = :school_id
                  AND p.opening_date <= NOW()
                  AND d.id IS NULL
                ORDER BY p.deadline ASC, u.name";
        return $this->db->query($sql, ['school_id' => $schoolId])->fetchAll();
    }

    public function getUniqueNamesPhysicalEducation() {
        $sql = "SELECT DISTINCT name, id FROM periods WHERE is_physical_education = 1 GROUP BY name ORDER BY id DESC";
        return $this->db->query($sql)->fetchAll();
    }
}
