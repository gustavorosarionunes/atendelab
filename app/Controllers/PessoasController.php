<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Middleware/auth.php';

// ============================================================
// AtendeLab - PessoasController
// ============================================================

class PessoasController
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
    // GET: listar todas as pessoas
    // ----------------------------------------------------------
    public function listar(): void
    {
        exigirAutenticacao();

        try {
            $pdo  = conectar();
            $stmt = $pdo->query(
                "SELECT id, nome, documento, telefone, email,
                        curso, periodo, observacoes, status,
                        criado_em, atualizado_em
                   FROM pessoas
                  ORDER BY nome ASC"
            );
            $this->json(['pessoas' => $stmt->fetchAll()]);
        } catch (PDOException $e) {
            $this->erro('Erro ao listar pessoas.', 500);
        }
    }

    // ----------------------------------------------------------
    // GET: buscar pessoa por id (aceita action=buscar ou buscarPorId)
    // ----------------------------------------------------------
    public function buscar(): void
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
                "SELECT id, nome, documento, telefone, email,
                        curso, periodo, observacoes, status
                   FROM pessoas
                  WHERE id = :id
                  LIMIT 1"
            );
            $stmt->execute([':id' => $id]);
            $pessoa = $stmt->fetch();

            if (!$pessoa) {
                $this->erro('Pessoa não encontrada.', 404);
                return;
            }

            $this->json(['pessoa' => $pessoa]);
        } catch (PDOException $e) {
            $this->erro('Erro ao buscar pessoa.', 500);
        }
    }

    // ----------------------------------------------------------
    // POST: criar pessoa
    // ----------------------------------------------------------
    public function criar(): void
    {
        exigirAutenticacao();

        $nome       = trim($_POST['nome']      ?? '');
        $documento  = trim($_POST['documento'] ?? '');
        $email      = trim($_POST['email']     ?? '');
        $telefone   = trim($_POST['telefone']  ?? '');
        $curso      = trim($_POST['curso']     ?? '');
        $periodo    = trim($_POST['periodo']   ?? '');
        $observacoes= trim($_POST['observacoes'] ?? '');
        $status     = $_POST['status'] ?? 'ativo';

        if ($nome === '' || $documento === '' || $email === '') {
            $this->erro('Nome, documento e e-mail são obrigatórios.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->erro('E-mail inválido.');
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $status = 'ativo';
        }

        try {
            $pdo  = conectar();
            $stmt = $pdo->prepare(
                "INSERT INTO pessoas
                    (nome, documento, telefone, email, curso, periodo, observacoes, status)
                 VALUES
                    (:nome, :documento, :telefone, :email, :curso, :periodo, :observacoes, :status)"
            );
            $stmt->execute([
                ':nome'        => $nome,
                ':documento'   => $documento,
                ':telefone'    => $telefone ?: null,
                ':email'       => $email,
                ':curso'       => $curso ?: null,
                ':periodo'     => $periodo ?: null,
                ':observacoes' => $observacoes ?: null,
                ':status'      => $status,
            ]);

            $this->json(['mensagem' => 'Pessoa cadastrada com sucesso.', 'id' => (int) $pdo->lastInsertId()], 201);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $this->erro('Documento já cadastrado.');
            } else {
                $this->erro('Erro ao cadastrar pessoa.', 500);
            }
        }
    }

    // ----------------------------------------------------------
    // POST: atualizar pessoa
    // ----------------------------------------------------------
    public function atualizar(): void
    {
        exigirAutenticacao();

        $id         = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome       = trim($_POST['nome']      ?? '');
        $documento  = trim($_POST['documento'] ?? '');
        $email      = trim($_POST['email']     ?? '');
        $telefone   = trim($_POST['telefone']  ?? '');
        $curso      = trim($_POST['curso']     ?? '');
        $periodo    = trim($_POST['periodo']   ?? '');
        $observacoes= trim($_POST['observacoes'] ?? '');
        $status     = $_POST['status'] ?? 'ativo';

        if (!$id) {
            $this->erro('ID inválido.');
            return;
        }

        if ($nome === '' || $documento === '' || $email === '') {
            $this->erro('Nome, documento e e-mail são obrigatórios.');
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $status = 'ativo';
        }

        try {
            $pdo  = conectar();
            $stmt = $pdo->prepare(
                "UPDATE pessoas
                    SET nome        = :nome,
                        documento   = :documento,
                        telefone    = :telefone,
                        email       = :email,
                        curso       = :curso,
                        periodo     = :periodo,
                        observacoes = :observacoes,
                        status      = :status
                  WHERE id = :id"
            );
            $stmt->execute([
                ':nome'        => $nome,
                ':documento'   => $documento,
                ':telefone'    => $telefone ?: null,
                ':email'       => $email,
                ':curso'       => $curso ?: null,
                ':periodo'     => $periodo ?: null,
                ':observacoes' => $observacoes ?: null,
                ':status'      => $status,
                ':id'          => $id,
            ]);

            $this->json(['mensagem' => 'Pessoa atualizada com sucesso.']);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $this->erro('Documento já cadastrado para outra pessoa.');
            } else {
                $this->erro('Erro ao atualizar pessoa.', 500);
            }
        }
    }

    // ----------------------------------------------------------
    // POST: inativar pessoa (exclusão lógica - RN11)
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
                "UPDATE pessoas SET status = 'inativo' WHERE id = :id"
            );
            $stmt->execute([':id' => $id]);

            $this->json(['mensagem' => 'Pessoa inativada com sucesso.']);
        } catch (PDOException $e) {
            $this->erro('Erro ao inativar pessoa.', 500);
        }
    }
}
