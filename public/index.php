<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Database;
use App\Router;
use App\TarefaService;

$metodo  = $_SERVER['REQUEST_METHOD'];
$caminho = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$corpo   = json_decode(file_get_contents('php://input'), true);
$corpo   = is_array($corpo) ? $corpo : [];

$router = new Router(new TarefaService(Database::conectar()));
[$status, $dados] = $router->handle($metodo, $caminho, $corpo);

http_response_code($status);
header('Content-Type: application/json; charset=utf-8');
if ($dados !== null) {
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
}