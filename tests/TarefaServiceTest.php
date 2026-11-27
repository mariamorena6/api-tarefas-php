<?php
namespace Tests;

use App\Database;
use App\TarefaService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TarefaServiceTest extends TestCase
{
    private TarefaService $service;

    protected function setUp(): void
    {
        // banco em memória: novo e vazio a cada teste
        $this->service = new TarefaService(Database::conectar(':memory:'));
    }

    public function testCriarTarefaValida(): void
    {
        $tarefa = $this->service->criar(['titulo' => 'Estudar']);
        $this->assertSame('Estudar', $tarefa['titulo']);
        $this->assertNotEmpty($tarefa['id']);
    }

    public function testCriarComTituloVazioLancaErro(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->criar(['titulo' => '   ']);
    }

    public function testCriarTituloDuplicadoLancaErro(): void
    {
        $this->service->criar(['titulo' => 'Estudar']);
        $this->expectException(InvalidArgumentException::class);
        $this->service->criar(['titulo' => 'Estudar']);
    }

    public function testExcluirTarefaInexistenteRetornaFalse(): void
    {
        $this->assertFalse($this->service->excluir(999));
    }
}