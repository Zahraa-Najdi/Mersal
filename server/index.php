<?php 
require_once("./services/ResponseService.php");
include(__DIR__ . "/routes/routes.php");


$base_dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (strpos($request, $base_dir) === 0) {
    $request = substr($request, strlen($base_dir));
}

if ($request == '') {
    $request = '/';
}

$apis = $routes;

if (isset($apis[$request])) {
    $controller_name = $apis[$request]['controllers']; 
    $method = $apis[$request]['method'];
    if($controller_name == 'Analyze.php') {
        require_once "AI/{$controller_name}";
            exit;
    }
    require_once "controllers/{$controller_name}.php";
    
    $controller = new $controller_name();
    if (method_exists($controller, $method)) {
        $controller->$method();
    } else {
        echo ResponseService::response(500, "Error: Method {$method} not found in {$controller_name}");
    }
} else {
    echo ResponseService::response(404, "Route Not Found");
}