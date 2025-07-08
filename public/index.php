<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\ClienteController;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if($uri === 'criar'){
        $controller = new ClienteController();
        $controller->criar();

    }
    
    if($uri === 'editar'){
        $controller = new ClienteController();
        $controller->editar();
    }

    if($uri === 'inativar'){
        $controller = new ClienteController();
        $controller->inativar();
    }

    exit;
}

$controller = new ClienteController();
//$clientes = $controller->listarTodos();

//Rota GET principal da interface
require_once __DIR__ . '/../App/Views/home.php';