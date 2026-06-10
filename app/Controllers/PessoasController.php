<?php

// Controller da entidade de pessoas - Atividade prática
class PessoasController 
{
    // Conexão PDO reutilizada em todos os métodos
    private PDO $pdo;

    public function __construct()
    {
        // Importa o arquivo que inicializa o objeto $pdo
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    // LISTAR PESSOAS (GET)
    public function listar(): void
    {
        header("Content-Type: application/json; charset=utf-8");

        $sql = 'SELECT id, nome, cpf, telefone, tipo, status, criado_em FROM pessoas ORDER BY id DESC';
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

        $sql = 'SELECT id, nome, cpf, telefone, tipo, status, criado_em FROM pessoas WHERE id = :id';
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

        // Coleta dados vindos via formulário (POST)
        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $tipo = $_POST['tipo'] ?? 'aluno';
        $status = $_POST['status'] ?? 'ativo';

        // Regras mínimas de validação adaptadas para a tabela pessoas
        if ($nome === '' || $cpf === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Nome e CPF são obrigatórios.']);
            return;
        }

        // Whitelist para o campo 'tipo' da tabela pessoas
        if (!in_array($tipo, ['aluno', 'professor', 'servidor'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Tipo inválido. Escolha entre aluno, professor ou servidor.']);
            return;
        }

        // Whitelist para o campo 'status'
        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            $sql = 'INSERT INTO pessoas (nome, cpf, telefone, tipo, status) 
                    VALUES (:nome, :cpf, :telefone, :tipo, :status)';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':tipo', $tipo);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Pessoa cadastrada com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            // Trata erro de duplicidade se o CPF for uma chave UNIQUE no banco
            echo json_encode(['erro' => 'Erro ao cadastrar pessoa. Verifique se o CPF já está registrado.']);
        }
    }

    // ATUALIZAR (POST)
    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $tipo = $_POST['tipo'] ?? 'aluno';
        $status = $_POST['status'] ?? 'ativo';

        if (!$id || $nome === '' || $cpf === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'ID, nome e CPF são obrigatórios para atualização.']);
            return;
        }

        try {
            $sql = 'UPDATE pessoas 
                    SET nome = :nome, cpf = :cpf, telefone = :telefone, tipo = :tipo, status = :status 
                    WHERE id = :id';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':tipo', $tipo);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Pessoa atualizada com sucesso.'], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar dados da pessoa.']);
        }
    }

    // EXCLUIR (POST)
    public function excluir(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        try {
            $sql = 'DELETE FROM pessoas WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Pessoa excluída com sucesso.'], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao excluir pessoa do banco de dados.']);
        }
    }
}