<?php

require_once __DIR__ . '/../Core/Model.php';

class ClassModel extends Model {
    public function create($schoolId, $name) {
        $sql = "INSERT INTO classes (school_id, name) VALUES (:school_id, :name)";
        return $this->db->query($sql, ['school_id' => $schoolId, 'name' => $name]);
    }

    public function getBySchoolId($schoolId) {
        $sql = "SELECT * FROM classes WHERE school_id = :school_id ORDER BY name ASC";
        return $this->db->query($sql, ['school_id' => $schoolId])->fetchAll();
    }

    public function getBySchoolIdWithProfessor($schoolId) {
        $sql = "SELECT c.*, u.name as professor_name 
                FROM classes c
                LEFT JOIN users u ON u.class_id = c.id AND u.role = 'professor' AND (u.is_physical_education = 0 OR u.is_physical_education IS NULL)
                WHERE c.school_id = :school_id 
                ORDER BY c.name ASC";
        return $this->db->query($sql, ['school_id' => $schoolId])->fetchAll();
    }
    public function findById($id) {
        return $this->db->query("SELECT * FROM classes WHERE id = :id", ['id' => $id])->fetch();
    }

    public function delete($id) {
        $sql = "DELETE FROM classes WHERE id = :id";
        return $this->db->query($sql, ['id' => $id]);
    }

    public function update($id, $name) {
        $sql = "UPDATE classes SET name = :name WHERE id = :id";
        return $this->db->query($sql, ['id' => $id, 'name' => $name]);
    }

    /**
     * Get all classes with allocation status (professor assigned or vacant)
     * @param int|null $schoolId Filter by school, null for all schools
     * @param array $schoolIds Filter by multiple schools (for SEMED users)
     * @return array Classes with school_name, professor_name, and is_vacant flag
     */
    public function getAllWithAllocation($schoolId = null, $schoolIds = []) {
        $sql = "SELECT c.id, c.name as class_name, c.school_id,
                       s.name as school_name,
                       u.id as professor_id,
                       u.name as professor_name,
                       CASE WHEN u.id IS NULL THEN 1 ELSE 0 END as is_vacant
                FROM classes c
                INNER JOIN schools s ON c.school_id = s.id
                LEFT JOIN users u ON u.class_id = c.id 
                    AND u.role = 'professor' 
                    AND (u.is_physical_education = 0 OR u.is_physical_education IS NULL)
                    AND (u.is_monitor = 0 OR u.is_monitor IS NULL)
                WHERE 1=1";
        
        $params = [];
        
        if ($schoolId) {
            $sql .= " AND c.school_id = :school_id";
            $params['school_id'] = $schoolId;
        } elseif (!empty($schoolIds)) {
            $placeholders = implode(',', array_map('intval', $schoolIds));
            $sql .= " AND c.school_id IN ($placeholders)";
        }
        
        $sql .= " ORDER BY s.name ASC, c.name ASC";
        
        return $this->db->query($sql, $params)->fetchAll();
    }

    /**
     * Count total vacant classes (classes without assigned professor)
     * @param array $schoolIds Filter by schools (for SEMED users)
     * @return int Number of vacant classes
     */
    public function countVacantClasses($schoolIds = []) {
        $sql = "SELECT COUNT(DISTINCT c.id) as count
                FROM classes c
                LEFT JOIN users u ON u.class_id = c.id 
                    AND u.role = 'professor'
                    AND (u.is_physical_education = 0 OR u.is_physical_education IS NULL)
                    AND (u.is_monitor = 0 OR u.is_monitor IS NULL)
                WHERE u.id IS NULL";
        
        if (!empty($schoolIds)) {
            $placeholders = implode(',', array_map('intval', $schoolIds));
            $sql .= " AND c.school_id IN ($placeholders)";
        }
        
        return $this->db->query($sql)->fetch()['count'];
    }
}
