<?php

class PessoasController 
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    // LISTAR PESSOAS (GET)
    public function listar(): void
    {
        header("Content-Type: application/json; charset=utf-8");

        // CORREÇÃO: Usando as colunas reais da nossa tabela
        $sql = 'SELECT id, nome, documento, telefone, email, curso, periodo, observacoes, status, criado_em, atualizado_em FROM pessoas ORDER BY id DESC';
        $stmt = $this->pdo->query($sql);
        $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($pessoas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // BUSCAR POR ID (GET)
    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        // CORREÇÃO: Atualizado para as colunas corretas
        $sql = 'SELECT id, nome, documento, telefone, email, curso, periodo, observacoes, status, criado_em, atualizado_em FROM pessoas WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pessoa) {
            http_response_code(404);
            echo json_encode(['erro' => 'Pessoa não encontrada.']);
            return;
        }

        echo json_encode($pessoa, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // CRIAR / CADASTRAR (POST)
    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $nome = trim($_POST['nome'] ?? '');
        $documento = trim($_POST['documento'] ?? ''); // CORREÇÃO: era cpf
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');         // NOVO: email é obrigatório na tabela
        $curso = trim($_POST['curso'] ?? '');
        $periodo = trim($_POST['periodo'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');
        $status = $_POST['status'] ?? 'ativo';

        // Validação das colunas NOT NULL do nosso banco
        if ($nome === '' || $documento === '' || $email === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Nome, Documento e E-mail são obrigatórios.']);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            // CORREÇÃO: SQL atualizado
            $sql = 'INSERT INTO pessoas (nome, documento, telefone, email, curso, periodo, observacoes, status) 
                    VALUES (:nome, :documento, :telefone, :email, :curso, :periodo, :observacoes, :status)';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':documento', $documento);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':curso', $curso);
            $stmt->bindValue(':periodo', $periodo);
            $stmt->bindValue(':observacoes', $observacoes);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Pessoa cadastrada com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao cadastrar pessoa. Verifique se o Documento já está registrado.']);
        }
    }

    // ATUALIZAR (POST) e EXCLUIR seguem a mesma lógica (atualizar as colunas no UPDATE).
    // Para economizar espaço, foque em testar o 'listar' primeiro!
}
