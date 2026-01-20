<?php

class School extends Model {
    public function all() {
        return $this->db->query("SELECT * FROM schools ORDER BY name ASC")->fetchAll();
    }

    public function findById($id) {
        return $this->db->query("SELECT * FROM schools WHERE id = :id", ['id' => $id])->fetch();
    }

    public function getAvailableSchools($includeUserId = null) {
        // Returns schools that are NOT in user_schools pivot table
        // If $includeUserId is provided, ALSO include schools assigned to that user
        
        $sql = "SELECT * FROM schools WHERE id NOT IN (SELECT school_id FROM user_schools)";
        
        if ($includeUserId) {
            $sql = "SELECT * FROM schools 
                    WHERE id NOT IN (SELECT school_id FROM user_schools WHERE user_id != :uid) 
                    ORDER BY name ASC";
            return $this->db->query($sql, ['uid' => $includeUserId])->fetchAll();
        }
        
        $sql .= " ORDER BY name ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO schools (name, address, inep_code, director_name, director_phone) 
                VALUES (:name, :address, :inep_code, :director_name, :director_phone)";
        
        return $this->db->query($sql, [
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
            'inep_code' => $data['inep_code'] ?? null,
            'director_name' => $data['director_name'] ?? null,
            'director_phone' => $data['director_phone'] ?? null
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE schools SET 
                    name = :name, 
                    address = :address,
                    inep_code = :inep_code, 
                    director_name = :director_name, 
                    director_phone = :director_phone 
                WHERE id = :id";
                
        return $this->db->query($sql, [
            'id' => $id,
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
            'inep_code' => $data['inep_code'] ?? null,
            'director_name' => $data['director_name'] ?? null,
            'director_phone' => $data['director_phone'] ?? null
        ]);
    }

    public function getSemedUsers($schoolId) {
        $sql = "SELECT u.* FROM users u 
                JOIN user_schools us ON u.id = us.user_id 
                WHERE us.school_id = :sid AND u.role = 'semed'
                ORDER BY u.name ASC";
        return $this->db->query($sql, ['sid' => $schoolId])->fetchAll();
    }

    public function getCoordinators($schoolId) {
        // Coordinators might be linked via pivot OR legacy school_id
        $sql = "SELECT DISTINCT u.* FROM users u 
                LEFT JOIN user_schools us ON u.id = us.user_id 
                WHERE (us.school_id = :sid OR u.school_id = :sid) 
                AND u.role = 'coordinator'
                ORDER BY u.name ASC";
        return $this->db->query($sql, ['sid' => $schoolId])->fetchAll();
    }

    public function getProfessors($schoolId) {
        // Professors usually use legacy school_id
        $sql = "SELECT * FROM users WHERE school_id = :sid AND role = 'professor' ORDER BY name ASC";
        return $this->db->query($sql, ['sid' => $schoolId])->fetchAll();
    }

    public function delete($id) {
        // Warning: Deleting a school will affect all related users and data.
        // Implementation might prefer 'deactivating' instead of hard delete.
        return $this->db->query("DELETE FROM schools WHERE id = :id", ['id' => $id]);
    }

    /**
     * Count total schools, optionally filtered by IDs
     * @param array $schoolIds List of school IDs to filter by (or empty for all)
     * @return int
     */
    public function countAll($schoolIds = []) {
        $sql = "SELECT COUNT(*) as count FROM schools";
        
        if (!empty($schoolIds)) {
            $placeholders = implode(',', array_map('intval', $schoolIds));
            $sql .= " WHERE id IN ($placeholders)";
        }
        
        return $this->db->query($sql)->fetch()['count'];
    }
}
