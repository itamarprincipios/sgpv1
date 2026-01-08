<?php

require_once __DIR__ . '/../Core/Model.php';

class User extends Model {
    public function findByEmail($email) {
        $stmt = $this->db->query("SELECT * FROM users WHERE email = :email", ['email' => $email]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->query("SELECT * FROM users WHERE id = :id", ['id' => $id]);
        return $stmt->fetch();
    }

    public function createProfessor($data) {
        $sql = "INSERT INTO users (school_id, name, email, password, role, whatsapp, class_id, is_physical_education) 
                VALUES (:school_id, :name, :email, :password, 'professor', :whatsapp, :class_id, :is_physical_education)";
        // Password should be hashed before passed here or here. Assuming hashed.
        return $this->db->query($sql, $data);
    }

    public function getProfessorsBySchoolWithClass($schoolId) {
        $sql = "SELECT u.*, c.name as class_name 
                FROM users u 
                LEFT JOIN classes c ON u.class_id = c.id 
                WHERE u.school_id = :school_id AND u.role = 'professor' 
                ORDER BY c.name ASC, u.name ASC";
        return $this->db->query($sql, ['school_id' => $schoolId])->fetchAll();
    }

    public function getCoordinatorsBySchool($schoolId) {
        $sql = "SELECT u.* FROM users u 
                LEFT JOIN user_schools us ON u.id = us.user_id
                WHERE (u.school_id = :sid OR us.school_id = :sid) 
                AND u.role = 'coordinator'
                GROUP BY u.id
                ORDER BY u.name ASC";
        return $this->db->query($sql, ['sid' => $schoolId])->fetchAll();
    }
    
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        return $this->db->query($sql, ['id' => $id]);
    }

    public function getByRole($role) {
        // Updated to support multiple schools via user_schools OR single school_id (legacy/hybrid)
        $sql = "SELECT u.*, 
                       GROUP_CONCAT(s.name SEPARATOR ', ') as school_name,
                       GROUP_CONCAT(s.id SEPARATOR ',') as school_ids
                FROM users u 
                LEFT JOIN schools s ON u.school_id = s.id 
                WHERE u.role = :role 
                GROUP BY u.id
                ORDER BY u.name ASC";
        
        // Correct query to join efficiently. 
        // We need to join user_schools primarily, but also respect u.school_id if user_schools is empty?
        // Let's assume we want to show ALL linked schools.
        // Complexity: A user might have school_id (main) AND user_schools entries.
        // Ideally we migrate everyone to use user_schools, but for now let's UNION or LEFT JOIN smart.
        
        $sql = "SELECT u.*, 
                    TRIM(BOTH ', ' FROM CONCAT(
                        COALESCE(s_main.name, ''), 
                        CASE WHEN COUNT(s_extra.id) > 0 THEN ', ' ELSE '' END,
                        COALESCE(GROUP_CONCAT(s_extra.name SEPARATOR ', '), '')
                    )) as school_name,
                     TRIM(BOTH ',' FROM CONCAT(
                        COALESCE(s_main.id, ''), 
                        CASE WHEN COUNT(s_extra.id) > 0 THEN ',' ELSE '' END,
                        COALESCE(GROUP_CONCAT(s_extra.id SEPARATOR ','), '')
                    )) as school_ids_raw
                FROM users u 
                LEFT JOIN schools s_main ON u.school_id = s_main.id
                LEFT JOIN user_schools us ON u.id = us.user_id
                LEFT JOIN schools s_extra ON us.school_id = s_extra.id
                WHERE u.role = :role 
                GROUP BY u.id
                ORDER BY u.name ASC";
                
         // Simplified approach involves checking if we are using the new system exclusively. 
         // Strategy: If user_schools has entries, use them. If not, use school_id.
         // Actually, let's just show everything.
         
         return $this->db->query($sql, ['role' => $role])->fetchAll();
    }

    public function create($data) {
        $fields = array_keys($data);
        $placeholders = array_map(function($f) { return ":$f"; }, $fields);
        
        $sql = "INSERT INTO users (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        return $this->db->query($sql, $data);
    }

    public function update($id, $data) {
        $fields = [];
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "$key = :$key";
            }
        }
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $data['id'] = $id;
        return $this->db->query($sql, $data);
    }

    public function getBySchoolId($schoolId, $role = null) {
        $sql = "SELECT * FROM users WHERE school_id = :school_id";
        $params = ['school_id' => $schoolId];
        
        if ($role) {
            $sql .= " AND role = :role";
            $params['role'] = $role;
        }
        
        $sql .= " ORDER BY name ASC";
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getBySchoolIds(array $schoolIds, $role = null) {
        if (empty($schoolIds)) return [];
        
        $placeholders = implode(',', array_fill(0, count($schoolIds), '?'));
        
        // Modified query: JOIN schools only for the coordinator's actual links
        $sql = "SELECT u.*, 
                       GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ') as school_name,
                       GROUP_CONCAT(DISTINCT s.id SEPARATOR ',') as school_ids_raw
                FROM users u 
                LEFT JOIN user_schools us ON u.id = us.user_id 
                LEFT JOIN schools s ON (s.id = us.school_id OR s.id = u.school_id)
                WHERE (u.school_id IN ($placeholders) OR us.school_id IN ($placeholders))";
        
        $params = array_merge($schoolIds, $schoolIds); // Duplicate for both clauses
        
        if ($role) {
            $sql .= " AND u.role = ?";
            $params[] = $role;
        }
        
        $sql .= " GROUP BY u.id ORDER BY u.name ASC";
        
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function assignSchools($userId, $schoolIds) {
        // Clear existing
        $this->db->query("DELETE FROM user_schools WHERE user_id = :user_id", ['user_id' => $userId]);
        
        // Add new
        if (!empty($schoolIds)) {
            $sql = "INSERT INTO user_schools (user_id, school_id) VALUES (:user_id, :school_id)";
            foreach ($schoolIds as $sid) {
                if (!empty($sid) && is_numeric($sid)) {
                    $this->db->query($sql, ['user_id' => $userId, 'school_id' => $sid]);
                }
            }
        }
    }

    public function getAssignedSchoolIds($userId) {
        $stmt = $this->db->query("SELECT school_id FROM user_schools WHERE user_id = :user_id", ['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Returns simple array [1, 5, 8]
    }

    public function updateProfilePhoto($userId, $fileName) {
        $sql = "UPDATE users SET profile_photo = :photo WHERE id = :id";
        return $this->db->query($sql, ['photo' => $fileName, 'id' => $userId]);
    }

    public function getManagedSchools($userId) {
        $sql = "SELECT s.* FROM schools s 
                JOIN user_schools us ON s.id = us.school_id 
                WHERE us.user_id = :uid 
                ORDER BY s.name ASC";
        return $this->db->query($sql, ['uid' => $userId])->fetchAll();
    }
}
