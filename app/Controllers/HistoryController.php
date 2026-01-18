<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/Document.php';
require_once __DIR__ . '/../Models/School.php';
require_once __DIR__ . '/../Models/User.php';

class HistoryController extends Controller {
    
    public function index() {
        $user = auth();
        $isSemed = $user['role'] === 'semed';
        $isAdmin = $user['role'] === 'admin';

        if (!$isSemed && !$isAdmin) {
            redirect('login');
        }

        $docModel = new Document();
        $schoolModel = new School();
        $userModel = new User();

        // Filters
        $year = $_GET['year'] ?? date('Y');
        $schoolId = $_GET['school_id'] ?? null;
        $professorId = $_GET['professor_id'] ?? null;

        // Restriction for SEMED
        if ($isSemed) {
            $assignedSchoolIds = $userModel->getAssignedSchoolIds($user['id']);
            
            // Fallback: If no schools assigned in user_schools table, get all schools
            // This maintains backward compatibility for SEMED users not yet migrated to user_schools
            if (empty($assignedSchoolIds)) {
                $schools = $schoolModel->all();
            } else {
                // Filter schools to only show assigned ones
                $allSchools = $schoolModel->all();
                $schools = array_filter($allSchools, function($school) use ($assignedSchoolIds) {
                    return in_array($school['id'], $assignedSchoolIds);
                });
                
                // Security: If trying to access unauthorized school, reset it
                if ($schoolId && !in_array($schoolId, $assignedSchoolIds)) {
                    $schoolId = null;
                }
            }
        } else {
            $schools = $schoolModel->all(); // Admin sees all
        }

        $filters = [
            'year' => $year,
            'school_id' => $schoolId,
            'professor_id' => $professorId
        ];

        // If Semed and no school selected, we might want to restrict search? 
        // Or if we leave school_id null in Document::search, it searches ALL.
        // We must prevent SEMED from seeing other schools' docs.
        
        $documents = [];
        if ($isAdmin || ($isSemed && $schoolId)) {
             $documents = $docModel->search($filters);
        } else if ($isSemed && !$schoolId) {
             // Semed without specific school filter -> Must filter by ALL assigned schools
             // Document::search supports single school_id.
             // We can iterate or modify search. For now, let's ask user to select school.
             $documents = []; // Force selection
        }

        // Get Professors for dropdown (reactive to school_id would be nice, but load all for now or filter)
        $professors = [];
        if ($schoolId) {
            $professors = $userModel->getBySchoolId($schoolId, 'professor');
        }

        // Get available years (simple hardcode or distinct query)
        $years = range(date('Y'), 2024); 

        $viewPath = $isAdmin ? 'admin/history' : 'semed/history';
        $this->view($viewPath, [
            'documents' => $documents,
            'schools' => $schools,
            'professors' => $professors,
            'years' => $years,
            'filters' => $filters,
            'user' => $user
        ]);
    }
}
