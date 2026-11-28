<?php
namespace Tests;

use App\Database;
use App\Router;
use App\TarefaService;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router(new TarefaService(Database::conectar(':memory:')));
    }

    public function testHealthRetornaOk(): void
    {
        [$status, $dados] = $this->router->handle('GET', '/health');
        $this->assertSame(200, $status);
        $this->assertSame('ok', $dados['status']);
    }

    public function testPostValidoRetorna201(): void
    {
        [$status, $dados] = $this->router->handle('POST', '/tarefas', ['titulo' => 'Nova tarefa']);
        $this->assertSame(201, $status);
        $this->assertSame('Nova tarefa', $dados['titulo']);
    }

    public function testBuscarInexistenteRetorna404(): void
    {
        [$status] = $this->router->handle('GET', '/tarefas/999');
        $this->assertSame(404, $status);
    }
}