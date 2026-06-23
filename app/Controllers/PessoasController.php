<?php

class PessoasController
{
    private PDO $pdo;
    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function json(array $dados, int $status = 200) :void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    }

    public function listar() :void
    {
        $sql = 'SELECT id, nome, documento, email, telefone, curso, periodo, status, observacoes
                FROM pessoas
                ORDER BY nome';
        $this->json($this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));
    }

    public function buscarPorId() :void
    {
        $id = filter_input(INPUT_GET,'id', FILTER_VALIDATE_INT);

        if(!$id){
            $this->json(['erro' => 'ID Inválido.'], 400);
            return;
        }

        $stmt = $this->pdo->prepare(
            'SELECT id, nome, documento, email, telefone, curso, periodo, status, observacoes
                FROM pessoas
                WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$pessoa){
            $this->json(['erro' => 'Pessoa não encontrada.'], 400);
            return;
        }
        $this->json($pessoa);   
    }

    public function criar() :void
    {
        $nome = trim($_POST['nome'] ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $curso = trim($_POST['curso'] ?? '');
        $periodo = trim($_POST['periodo'] ?? '');
        $status = $_POST['status'] ?? 'ativo';
        $observacoes = trim($_POST['observacoes'] ?? '');

        if($nome ===''|| $email ===''|| $documento ==='') {
            $this->json(['erro'=> 'Nome, documento e e-mail são obrigatórios'], 422);
            return;
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $this->json(['erro'=>'E-mail inválido'], 422);
            return;
        }

        if (!in_array($status, ['ativo','inativo'], true)){
            $this->json(['erro'=> 'Status inválido'], 422);
            return;
        }  

        try
        {
            $stmt = $this->pdo->prepare(
                'INSERT INTO pessoas (nome, documento, email, telefone, curso, periodo, status, observacoes)
                VALUES (:nome, :documento, :email, :telefone, :curso, :periodo, :status, :observacoes)'
            );
            $stmt->execute(compact(
                'nome', 'documento', 'email', 'telefone', 'curso', 'periodo', 'status', 'observacoes'
            ));
            $this->json(['mensagem' => 'Pessoa cadastrada com sucesso', 201]);

        } catch (PDOException $e) {
            $this->json(['erro' => 'Erro ao cadastrar pessoa', 400]);
        }
    }

    public function atualizar() :void
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $curso = trim($_POST['curso'] ?? '');
        $periodo = trim($_POST['periodo'] ?? '');
        $status = $_POST['status'] ?? 'ativo';
        $observacoes = trim($_POST['observacoes'] ?? '');

        if (!$id || $nome=== '' || $documento=== '' || $email=== ''){
            $this->json(['erro' => 'Dados obrigatórios ausentes.'], 422);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $this->json(['erro' => 'E-mail inválido'], 422);
            return;
        }
        if(!in_array($status, ['ativo','inativo'],true)){
            $this->json(['erro' => 'Status inválido'], 422);
            return;
        }

        try {
            $stmt = $this->pdo->prepare(
                'UPDATE pessoas SET nome = :nome,
                                    documento = :documento,
                                    email = :email,
                                    telefone = :telefone,
                                    curso = :curso,
                                    periodo = :periodo,
                                    status = :status,
                                    observacoes = :observacoes
                                WHERE id = :id'
            );
            $stmt->execute(compact(
                'id', 'nome', 'documento', 'email', 'telefone', 'curso', 'periodo', 'status', 'observacoes'
            ));
            $this->json(['mensagem' => 'Pessoa atualizado com sucesso', 201]);
        } catch(PDOException $e) {
            $this->json(['erro' => 'Erro ao atualizar pessoa', 400]);
        }
    }

    public function inativar() :void 
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if(!$id) {
            $this->json(['erro' => 'ID Inválido'], 422);
        }

        $stmt = $this->pdo->prepare(
            "UPDATE pessoas SET status = 'inativo' WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);
        $this->json(['mensagem' => 'Pessoa invativada com sucesso.']);
    }

    }
