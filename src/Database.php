<?php
namespace App;

use PDO;

class Database
{
    public static function conectar(?string $caminho = null): PDO
    {
        $caminho ??= getenv('DB_PATH') ?: __DIR__ . '/../data/tarefas.sqlite';
        $pdo = new PDO('sqlite:' . $caminho);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('CREATE TABLE IF NOT EXISTS tarefas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            titulo TEXT NOT NULL UNIQUE,
            descricao TEXT,
            concluida INTEGER NOT NULL DEFAULT 0
        )');
        return $pdo;
    }
}