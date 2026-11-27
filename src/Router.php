<?php
namespace App;

use InvalidArgumentException;
use Throwable;

class Router
{
    public function __construct(private TarefaService $service) {}

    public function handle(string $metodo, string $caminho, array $corpo = []): array
    {
        try {
            if ($caminho === '/health' && $metodo === 'GET') {
                return [200, ['status' => 'ok', 'version' => getenv('APP_VERSION') ?: 'dev']];
            }

            if ($caminho === '/tarefas') {
                if ($metodo === 'GET') {
                    return [200, $this->service->listar()];
                }
                if ($metodo === 'POST') {
                    $tarefa = $this->service->criar($corpo);
                    Logger::info("Tarefa criada: id {$tarefa['id']}");
                    return [201, $tarefa];
                }
            }

            if (preg_match('#^/tarefas/(\d+)$#', $caminho, $m)) {
                $id = (int) $m[1];
                $naoEncontrada = [404, ['erro' => 'Tarefa não encontrada']];

                if ($metodo === 'GET') {
                    $tarefa = $this->service->buscar($id);
                    return $tarefa ? [200, $tarefa] : $naoEncontrada;
                }
                if ($metodo === 'PUT') {
                    $tarefa = $this->service->atualizar($id, $corpo);
                    return $tarefa ? [200, $tarefa] : $naoEncontrada;
                }
                if ($metodo === 'DELETE') {
                    if ($this->service->excluir($id)) {
                        Logger::info("Tarefa excluída: id {$id}");
                        return [204, null];
                    }
                    return $naoEncontrada;
                }
            }

            return [404, ['erro' => 'Rota não encontrada']];
        } catch (InvalidArgumentException $e) {
            Logger::warning('Validação recusou: ' . $e->getMessage());
            return [422, ['erro' => $e->getMessage()]];
        } catch (Throwable $e) {
            Logger::error('Erro inesperado: ' . $e->getMessage());
            return [500, ['erro' => 'Erro interno']];
        }
    }
}