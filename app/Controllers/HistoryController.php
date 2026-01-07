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
            $assignedSchools = $userModel->getAssignedSchoolIds($user['id']);
            if ($schoolId && !in_array($schoolId, $assignedSchools)) {
                $schoolId = null; // Reset if trying to access unauthorized school
            }
            // If no school selected, maybe default to first? Or allow searching all assigned.
            // Search logic in Document model handles logic if we pass list of schools.
            // For MVP simplicity: If schoolId is empty for SEMED, limit query to assigned schools.
            // But Document::search logic currently takes single school_id.
            // Let's rely on Semed users picking a school from dropdown. 
            
            // Get schools managed by this SEMED user
            $schools = $userModel->getManagedSchools($user['id']);
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
