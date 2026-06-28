<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Middleware/auth.php';

// ============================================================
// AtendeLab - TiposAtendimentosController
// ============================================================

class TiposAtendimentosController
{
    private function json(mixed $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($dados);
        exit;
    }

    private function erro(string $mensagem, int $status = 400): void
    {
        $this->json(['erro' => $mensagem], $status);
    }

    // ----------------------------------------------------------
    // GET: listar todos os tipos
    // ----------------------------------------------------------
    public function listar(): void
    {
        exigirAutenticacao();

        try {
            $pdo  = conectar();
            $stmt = $pdo->query(
                "SELECT id, nome, descricao, status, criado_em, atualizado_em
                   FROM tipos_atendimentos
                  ORDER BY nome ASC"
            );
            $this->json(['tipos' => $stmt->fetchAll()]);
        } catch (PDOException $e) {
            $this->erro('Erro ao listar tipos de atendimento.', 500);
        }
    }

    // ----------------------------------------------------------
    // GET: buscar tipo por id (aceita action=buscar ou buscarPorId)
    // ----------------------------------------------------------
    public function buscarPorId(): void
    {
        exigirAutenticacao();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->erro('ID inválido.');
            return;
        }

        try {
            $pdo  = conectar();
            $stmt = $pdo->prepare(
                "SELECT id, nome, descricao, status
                   FROM tipos_atendimentos
                  WHERE id = :id
                  LIMIT 1"
            );
            $stmt->execute([':id' => $id]);
            $tipo = $stmt->fetch();

            if (!$tipo) {
                $this->erro('Tipo não encontrado.', 404);
                return;
            }

            $this->json(['tipo' => $tipo]);
        } catch (PDOException $e) {
            $this->erro('Erro ao buscar tipo.', 500);
        }
    }

    // ----------------------------------------------------------
    // POST: criar tipo
    // ----------------------------------------------------------
    public function criar(): void
    {
        exigirAutenticacao();

        $nome     = trim($_POST['nome']      ?? '');
        $descricao= trim($_POST['descricao'] ?? '');
        $status   = $_POST['status'] ?? 'ativo';

        if ($nome === '') {
            $this->erro('Nome é obrigatório.');
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $status = 'ativo';
        }

        try {
            $pdo  = conectar();
            $stmt = $pdo->prepare(
                "INSERT INTO tipos_atendimentos (nome, descricao, status)
                 VALUES (:nome, :descricao, :status)"
            );
            $stmt->execute([
                ':nome'      => $nome,
                ':descricao' => $descricao ?: null,
                ':status'    => $status,
            ]);

            $this->json(['mensagem' => 'Tipo cadastrado com sucesso.', 'id' => (int) $pdo->lastInsertId()], 201);
        } catch (PDOException $e) {
            $this->erro('Erro ao cadastrar tipo.', 500);
        }
    }

    // ----------------------------------------------------------
    // POST: atualizar tipo
    // ----------------------------------------------------------
    public function atualizar(): void
    {
        exigirAutenticacao();

        $id       = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome     = trim($_POST['nome']      ?? '');
        $descricao= trim($_POST['descricao'] ?? '');
        $status   = $_POST['status'] ?? 'ativo';

        if (!$id) {
            $this->erro('ID inválido.');
            return;
        }

        if ($nome === '') {
            $this->erro('Nome é obrigatório.');
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $status = 'ativo';
        }

        try {
            $pdo  = conectar();
            $stmt = $pdo->prepare(
                "UPDATE tipos_atendimentos
                    SET nome      = :nome,
                        descricao = :descricao,
                        status    = :status
                  WHERE id = :id"
            );
            $stmt->execute([
                ':nome'      => $nome,
                ':descricao' => $descricao ?: null,
                ':status'    => $status,
                ':id'        => $id,
            ]);

            $this->json(['mensagem' => 'Tipo atualizado com sucesso.']);
        } catch (PDOException $e) {
            $this->erro('Erro ao atualizar tipo.', 500);
        }
    }

    // ----------------------------------------------------------
    // POST: inativar tipo (exclusão lógica - RN11)
    // ----------------------------------------------------------
    public function inativar(): void
    {
        exigirAutenticacao();

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->erro('ID inválido.');
            return;
        }

        try {
            $pdo  = conectar();
            $stmt = $pdo->prepare(
                "UPDATE tipos_atendimentos SET status = 'inativo' WHERE id = :id"
            );
            $stmt->execute([':id' => $id]);

            $this->json(['mensagem' => 'Tipo inativado com sucesso.']);
        } catch (PDOException $e) {
            $this->erro('Erro ao inativar tipo.', 500);
        }
    }
}
