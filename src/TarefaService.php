<?php
namespace App;

use InvalidArgumentException;
use PDO;

class TarefaService
{
    public function __construct(private PDO $pdo) {}

    public function listar(): array
    {
        return $this->pdo->query('SELECT * FROM tarefas ORDER BY id')->fetchAll();
    }

    public function buscar(int $id): ?array
    {
        $st = $this->pdo->prepare('SELECT * FROM tarefas WHERE id = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    public function criar(array $dados): array
    {
        $titulo = $this->validarTitulo($dados['titulo'] ?? '');
        $st = $this->pdo->prepare('INSERT INTO tarefas (titulo, descricao) VALUES (?, ?)');
        $st->execute([$titulo, $dados['descricao'] ?? null]);
        return $this->buscar((int) $this->pdo->lastInsertId());
    }

    public function atualizar(int $id, array $dados): ?array
    {
        if ($this->buscar($id) === null) {
            return null;
        }
        $titulo = $this->validarTitulo($dados['titulo'] ?? '', $id);
        $st = $this->pdo->prepare('UPDATE tarefas SET titulo = ?, descricao = ?, concluida = ? WHERE id = ?');
        $st->execute([$titulo, $dados['descricao'] ?? null, !empty($dados['concluida']) ? 1 : 0, $id]);
        return $this->buscar($id);
    }

    public function excluir(int $id): bool
    {
        $st = $this->pdo->prepare('DELETE FROM tarefas WHERE id = ?');
        $st->execute([$id]);
        return $st->rowCount() > 0;
    }

    private function validarTitulo(string $titulo, ?int $idAtual = null): string
    {
        $titulo = trim($titulo);
        if ($titulo === '') {
            throw new InvalidArgumentException('O título é obrigatório');
        }
        if (mb_strlen($titulo) > 100) {
            throw new InvalidArgumentException('O título deve ter no máximo 100 caracteres');
        }
        $st = $this->pdo->prepare('SELECT id FROM tarefas WHERE titulo = ? AND id != ?');
        $st->execute([$titulo, $idAtual ?? 0]);
        if ($st->fetch()) {
            throw new InvalidArgumentException('Já existe uma tarefa com esse título');
        }
        return $titulo;
    }
}