<?php

// Autoload simples para não precisarmos usar 'require' em todos os arquivos
spl_autoload_register(function ($class) {
    // Converte os namespaces para caminhos de diretório no Linux (barras invertidas para barras normais)
    $path = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

use Controllers\PedidoController;

// Configurações de CORS para permitir que o Frontend (que rodará em outra porta) acesse a API
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Roteamento básico
if ($uri === '/pedidos' || $uri === '/backend/pedidos') {
    $controller = new PedidoController();

    if ($method === 'GET') {
        $controller->listar();
    } elseif ($method === 'POST') {
        // Pega o JSON enviado pelo frontend e converte para Array
        $input = json_decode(file_get_contents('php://input'), true);
        $controller->criar($input);
    } elseif ($method === 'OPTIONS') {
        // Resposta para requisições de preflight do navegador
        http_response_code(200);
    } else {
        http_response_code(405); // Method Not Allowed
        echo json_encode(["erro" => "Método não permitido."]);
    }
} else {
    http_response_code(404);
    echo json_encode(["erro" => "Rota não encontrada."]);
}