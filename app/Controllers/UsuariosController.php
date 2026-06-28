<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Middleware/auth.php';

// ============================================================
// AtendeLab - UsuariosController
// ============================================================

class UsuariosController
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

    private function exigirAdmin(): void
    {
        exigirAutenticacao();
        $usuario = usuarioAtual();
        if (($usuario['perfil'] ?? '') !== 'admin') {
            $this->erro('Acesso restrito ao administrador.', 403);
        }
    }

    // ----------------------------------------------------------
    // GET: listar usuarios
    // ----------------------------------------------------------
    public function listar(): void
    {
        $this->exigirAdmin();

        try {
            $pdo  = conectar();
            $stmt = $pdo->query(
                "SELECT id, nome, email, perfil, status, criado_em
                   FROM usuarios
                  ORDER BY nome ASC"
            );
            $this->json(['usuarios' => $stmt->fetchAll()]);
        } catch (PDOException $e) {
            $this->erro('Erro ao listar usuários.', 500);
        }
    }

    // ----------------------------------------------------------
    // POST: criar usuario
    // ----------------------------------------------------------
    public function criar(): void
    {
        $this->exigirAdmin();

        $nome   = trim($_POST['nome']   ?? '');
        $email  = trim($_POST['email']  ?? '');
        $senha  = $_POST['senha']       ?? '';
        $perfil = $_POST['perfil']      ?? 'atendente';
        $status = $_POST['status']      ?? 'ativo';

        if ($nome === '' || $email === '' || $senha === '') {
            $this->erro('Nome, e-mail e senha são obrigatórios.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->erro('E-mail inválido.');
            return;
        }

        if (!in_array($perfil, ['admin', 'atendente'], true)) {
            $perfil = 'atendente';
        }

        try {
            $pdo  = conectar();
            $hash = password_hash($senha, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare(
                "INSERT INTO usuarios (nome, email, senha, perfil, status)
                 VALUES (:nome, :email, :senha, :perfil, :status)"
            );
            $stmt->execute([
                ':nome'   => $nome,
                ':email'  => $email,
                ':senha'  => $hash,
                ':perfil' => $perfil,
                ':status' => $status,
            ]);

            $this->json(['mensagem' => 'Usuário criado com sucesso.', 'id' => (int) $pdo->lastInsertId()], 201);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $this->erro('E-mail já cadastrado.');
            } else {
                $this->erro('Erro ao criar usuário.', 500);
            }
        }
    }

    // ----------------------------------------------------------
    // POST: inativar usuario (RN09)
    // ----------------------------------------------------------
    public function inativar(): void
    {
        $this->exigirAdmin();

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->erro('ID inválido.');
            return;
        }

        $atual = usuarioAtual();
        if ((int) $atual['id'] === $id) {
            $this->erro('Você não pode inativar sua própria conta.');
            return;
        }

        try {
            $pdo  = conectar();
            $stmt = $pdo->prepare(
                "UPDATE usuarios SET status = 'inativo' WHERE id = :id"
            );
            $stmt->execute([':id' => $id]);
            $this->json(['mensagem' => 'Usuário inativado com sucesso.']);
        } catch (PDOException $e) {
            $this->erro('Erro ao inativar usuário.', 500);
        }
    }
}
