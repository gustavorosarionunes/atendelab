<?php

class AtendimentosController 
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    // LISTAR COM JOIN (Exigência da atividade)
    public function listar(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        
        // O JOIN junta as 4 tabelas para trazer nomes em vez de apenas IDs
        $sql = 'SELECT a.id, a.descricao, a.status, a.data_criacao, 
                       p.nome as pessoa_nome, 
                       t.nome as tipo_atendimento, 
                       u.nome as atendente_nome 
                FROM atendimentos a
                JOIN pessoas p ON a.pessoa_id = p.id
                JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
                JOIN usuarios u ON a.usuario_id = u.id
                ORDER BY a.id DESC';
                
        $stmt = $this->pdo->query($sql);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // VISUALIZAR (Buscar por ID com JOIN)
    public function visualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        $sql = 'SELECT a.*, p.nome as pessoa_nome, t.nome as tipo_atendimento, u.nome as atendente_nome 
                FROM atendimentos a
                JOIN pessoas p ON a.pessoa_id = p.id
                JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
                JOIN usuarios u ON a.usuario_id = u.id
                WHERE a.id = :id';
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            http_response_code(404);
            echo json_encode(['erro' => 'Atendimento não encontrado.']);
            return;
        }
        echo json_encode($atendimento, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $pessoa_id = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $tipo_id = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
        $descricao = trim($_POST['descricao'] ?? '');

        if (!$pessoa_id || !$tipo_id || !$usuario_id || $descricao === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'pessoa_id, tipo_atendimento_id, usuario_id e descricao são obrigatórios.']);
            return;
        }

        try {
            $sql = 'INSERT INTO atendimentos (pessoa_id, tipo_atendimento_id, usuario_id, descricao) 
                    VALUES (:p_id, :t_id, :u_id, :desc)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':p_id', $pessoa_id, PDO::PARAM_INT);
            $stmt->bindValue(':t_id', $tipo_id, PDO::PARAM_INT);
            $stmt->bindValue(':u_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(':desc', $descricao);
            $stmt->execute();

            http_response_code(201);
            echo json_encode(['mensagem' => 'Atendimento criado.', 'id' => $this->pdo->lastInsertId()], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao criar atendimento. Verifique se os IDs informados existem.']);
        }
    }

    // ATUALIZAR STATUS (Exigência da atividade)
    public function atualizarStatus(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';

        if (!$id || !in_array($status, ['pendente', 'em_andamento', 'concluido', 'cancelado'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido ou Status incorreto. Use: pendente, em_andamento, concluido, cancelado.']);
            return;
        }

        try {
            $sql = 'UPDATE atendimentos SET status = :status WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(['mensagem' => 'Status do atendimento atualizado.'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar status.']);
        }
    }
}