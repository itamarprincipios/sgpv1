<?php
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Helpers/functions.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Controller.php';
require_once __DIR__ . '/../app/Core/Model.php';

// Autoload simples (poderia ser composer, mas manual para MVP)
spl_autoload_register(function ($class) {
    if (file_exists(__DIR__ . '/../app/Controllers/' . $class . '.php')) {
        require_once __DIR__ . '/../app/Controllers/' . $class . '.php';
    } elseif (file_exists(__DIR__ . '/../app/Models/' . $class . '.php')) {
        require_once __DIR__ . '/../app/Models/' . $class . '.php';
    }
});

// Router dinâmico
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$scriptName = str_replace('\\', '/', $scriptName); // Normalizar windows

// Se o script estiver rodando em subpasta (ex: /sgp/public), remover da URI
if (strpos($uri, $scriptName) === 0 && $scriptName !== '/') {
    $uri = substr($uri, strlen($scriptName));
}

$uri = trim(str_replace('/public', '', $uri), '/'); 


// Rotas
$routes = [
    '' => 'AuthController@login',
    'login' => 'AuthController@login',
    'auth/verify' => 'AuthController@verify',
    'logout' => 'AuthController@logout',
    'professor/dashboard' => 'ProfessorController@dashboard',
    'professor/upload' => 'ProfessorController@upload',
    'school/dashboard' => 'SchoolController@dashboard',
    'school/planning/create' => 'SchoolController@createPlanning',
    'school/planning/store' => 'SchoolController@storePlanning',
    'school/planning/view' => 'SchoolController@viewPlanning',
    'school/class/store' => 'SchoolController@storeClass',
    'school/class/edit' => 'SchoolController@editClass',
    'school/class/update' => 'SchoolController@updateClass',
    'school/class/delete' => 'SchoolController@deleteClass',
    'school/planning/edit' => 'SchoolController@editPlanning',
    'school/planning/update' => 'SchoolController@updatePlanning',
    'school/planning/delete' => 'SchoolController@deletePlanning',

    'school/professor/store' => 'SchoolController@storeProfessor',
    'school/professor/edit' => 'SchoolController@editProfessor',
    'school/professor/update' => 'SchoolController@updateProfessor',
    'school/professor/delete' => 'SchoolController@deleteProfessor',
    'school/professor/reset-password' => 'SchoolController@resetProfessorPassword',
    'school/planning/associate-bimester' => 'SchoolController@associateToBimester',
    'school/document/review' => 'SchoolController@reviewDocument',
    'semed/dashboard' => 'SemedController@dashboard',
    'semed/schools' => 'SemedController@schools',
    'semed/school/store' => 'SemedController@storeSchool',
    'semed/school/edit' => 'SemedController@editSchool',
    'semed/school/update' => 'SemedController@updateSchool',
    'semed/school/delete' => 'SemedController@deleteSchool',
    'semed/photo/upload' => 'SemedController@uploadPhoto', // NEW
    'semed/coordinators' => 'SemedController@coordinators',
    'semed/coordinator/store' => 'SemedController@storeCoordinator',
    'semed/coordinator/edit' => 'SemedController@editCoordinator',
    'semed/coordinator/update' => 'SemedController@updateCoordinator',
    'semed/coordinator/unlink-school' => 'SemedController@unlinkSchoolFromCoordinator',
    'semed/coordinator/link-school' => 'SemedController@linkSchoolToCoordinator',
    'semed/directors' => 'SemedController@directors', // NEW
    'semed/director/store' => 'SemedController@storeDirector', // NEW
    'semed/director/edit' => 'SemedController@editDirector', // NEW
    'semed/director/update' => 'SemedController@updateDirector', // NEW
    'semed/director/delete' => 'SemedController@deleteDirector', // NEW
    'semed/password/reset' => 'SemedController@resetPassword',
    'semed/plannings' => 'SemedController@plannings',
    'semed/reports' => 'SemedController@reports',
    'semed/password/change' => 'SemedController@changePassword',
    'professor/password/change' => 'ProfessorController@changePassword',
    'professor/photo/upload' => 'ProfessorController@uploadPhoto', // NEW
    'professor/upload/delete' => 'ProfessorController@deleteUpload',
    'school/mark-viewed' => 'SchoolController@markUploadsAsViewed',
    'school/password/change' => 'SchoolController@changePassword',
    'school/photo/upload' => 'SchoolController@uploadPhoto', // NEW
    'school/coordinator/store' => 'SchoolController@storeCoordinator', // NEW (Director)
    'school/coordinator/edit' => 'SchoolController@editCoordinator', // NEW (Director)
    'school/coordinator/update' => 'SchoolController@updateCoordinator', // NEW (Director)
    'school/coordinator/delete' => 'SchoolController@deleteCoordinator', // NEW (Director)
    'school/coordinator/reset-password' => 'SchoolController@resetCoordinatorPassword', // NEW (Director)
    'school/reports' => 'SchoolController@reports', // NEW (Director)

    // Admin Routes
    'admin/dashboard' => 'AdminController@dashboard',
    'admin/user/store' => 'AdminController@storeUser',
    'admin/user/edit' => 'AdminController@editUser',
    'admin/user/update' => 'AdminController@updateUser',
    'admin/user/delete' => 'AdminController@deleteUser',
    'admin/user/reset-password' => 'AdminController@resetPassword',
    'admin/school/store' => 'AdminController@storeSchool',
    'admin/school/edit' => 'AdminController@editSchool',
    'admin/school/update' => 'AdminController@updateSchool',
    'admin/school/delete' => 'AdminController@deleteSchool',
    'admin/schools' => 'AdminController@schools',
    'admin/coordinators' => 'AdminController@coordinators',
    'admin/professors' => 'AdminController@professors',
    'admin/reports' => 'AdminController@reports',
    'admin/history' => 'HistoryController@index', // NEW
    'admin/reset-year' => 'AdminController@resetSchoolYear', // NEW
    'semed/history' => 'HistoryController@index', // NEW

    // Supervisor Ed. Física Routes
    'supervisor-edfis/dashboard' => 'SupervisorEdFisController@dashboard',
    'supervisor-edfis/planning/view' => 'SupervisorEdFisController@viewPlanning',
    'supervisor-edfis/punctuality_report' => 'SupervisorEdFisController@punctualityReport',
    'supervisor-edfis/professors' => 'SupervisorEdFisController@professors',
    'supervisor-edfis/photo/upload' => 'SupervisorEdFisController@uploadPhoto',

    // Admin - Supervisor Ed. Física management
    'admin/supervisor-edfis' => 'AdminController@supervisorEdFis',
    'admin/supervisor-edfis/store' => 'AdminController@storeSupervisorEdFis',
    'admin/supervisor-edfis/edit' => 'AdminController@editSupervisorEdFis',
    'admin/supervisor-edfis/update' => 'AdminController@updateSupervisorEdFis',
    'admin/supervisor-edfis/delete' => 'AdminController@deleteSupervisorEdFis',
    'admin/supervisor-edfis/reset-password' => 'AdminController@resetSupervisorPassword',
];

if (array_key_exists($uri, $routes)) {
    $parts = explode('@', $routes[$uri]);
    $controllerName = $parts[0];
    $methodName = $parts[1];

    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $methodName)) {
            $controller->$methodName();
        } else {
            die("Method $methodName not found in $controllerName");
        }
    } else {
        die("Controller $controllerName not found");
    }
} else {
    // 404
    echo "404 - Página não encontrada (" . htmlspecialchars($uri) . ")";
}
