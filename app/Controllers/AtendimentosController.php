<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Middleware/auth.php';

// ============================================================
// AtendeLab - AtendimentosController
// ============================================================

class AtendimentosController
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
    // Retorna o ID do usuário logado na sessão (RN04)
    // ----------------------------------------------------------
    private function usuarioResponsavel(): int
    {
        if (isset($_SESSION['usuario']['id'])) {
            return (int) $_SESSION['usuario']['id'];
        }

        http_response_code(401);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['erro' => 'Usuário não autenticado.']);
        exit;
    }

    // ----------------------------------------------------------
    // GET: listar atendimentos com JOIN
    // ----------------------------------------------------------
    public function listar(): void
    {
        exigirAutenticacao();

        try {
            $pdo  = conectar();
            $stmt = $pdo->query(
                "SELECT a.id,
                        p.nome  AS pessoa,
                        t.nome  AS tipo,
                        u.nome  AS responsavel,
                        a.descricao,
                        a.status,
                        a.data_atendimento,
                        a.horario_atendimento,
                        a.observacao_final,
                        a.criado_em
                   FROM atendimentos a
                   JOIN pessoas            p ON p.id = a.pessoa_id
                   JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                   JOIN usuarios           u ON u.id = a.usuario_id
                  ORDER BY a.data_atendimento DESC, a.horario_atendimento DESC"
            );
            $this->json(['atendimentos' => $stmt->fetchAll()]);
        } catch (PDOException $e) {
            $this->erro('Erro ao listar atendimentos.', 500);
        }
    }

    // ----------------------------------------------------------
    // GET: visualizar atendimento por id
    // ----------------------------------------------------------
    public function visualizar(): void
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
                "SELECT a.id,
                        a.pessoa_id,
                        a.tipo_atendimento_id,
                        a.usuario_id,
                        p.nome  AS pessoa,
                        t.nome  AS tipo,
                        u.nome  AS responsavel,
                        a.descricao,
                        a.status,
                        a.data_atendimento,
                        a.horario_atendimento,
                        a.observacao_final,
                        a.criado_em
                   FROM atendimentos a
                   JOIN pessoas            p ON p.id = a.pessoa_id
                   JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                   JOIN usuarios           u ON u.id = a.usuario_id
                  WHERE a.id = :id
                  LIMIT 1"
            );
            $stmt->execute([':id' => $id]);
            $atendimento = $stmt->fetch();

            if (!$atendimento) {
                $this->erro('Atendimento não encontrado.', 404);
                return;
            }

            $this->json(['atendimento' => $atendimento]);
        } catch (PDOException $e) {
            $this->erro('Erro ao buscar atendimento.', 500);
        }
    }

    // ----------------------------------------------------------
    // POST: criar atendimento (RN02, RN03, RN04)
    // ----------------------------------------------------------
    public function criar(): void
    {
        exigirAutenticacao();

        $pessoaId   = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $tipoId     = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        $descricao  = trim($_POST['descricao'] ?? '');
        $data       = trim($_POST['data_atendimento'] ?? '');
        $horario    = trim($_POST['horario_atendimento'] ?? '');
        $usuarioId  = $this->usuarioResponsavel(); // RN04 - vem da sessão

        if (!$pessoaId) {
            $this->erro('Pessoa é obrigatória. (RN02)');
            return;
        }

        if (!$tipoId) {
            $this->erro('Tipo de atendimento é obrigatório. (RN03)');
            return;
        }

        if ($descricao === '') {
            $this->erro('Descrição é obrigatória.');
            return;
        }

        if ($data === '' || $horario === '') {
            $this->erro('Data e horário são obrigatórios.');
            return;
        }

        try {
            $pdo  = conectar();
            $stmt = $pdo->prepare(
                "INSERT INTO atendimentos
                    (pessoa_id, tipo_atendimento_id, usuario_id, descricao,
                     status, data_atendimento, horario_atendimento)
                 VALUES
                    (:pessoa_id, :tipo_id, :usuario_id, :descricao,
                     'aberto', :data, :horario)"
            );
            $stmt->execute([
                ':pessoa_id'  => $pessoaId,
                ':tipo_id'    => $tipoId,
                ':usuario_id' => $usuarioId,
                ':descricao'  => $descricao,
                ':data'       => $data,
                ':horario'    => $horario,
            ]);

            $novoId = (int) $pdo->lastInsertId();
            $protocolo = 'ATD-' . str_pad((string) $novoId, 4, '0', STR_PAD_LEFT);

            $this->json([
                'mensagem'  => 'Atendimento registrado com sucesso.',
                'id'        => $novoId,
                'protocolo' => $protocolo,
            ], 201);
        } catch (PDOException $e) {
            $this->erro('Erro ao registrar atendimento.', 500);
        }
    }

    // ----------------------------------------------------------
    // POST: alterar status (RN05, RN06)
    // ----------------------------------------------------------
    public function alterarStatus(): void
    {
        exigirAutenticacao();

        $id              = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status          = $_POST['status'] ?? '';
        $observacaoFinal = trim($_POST['observacao_final'] ?? '');

        if (!$id) {
            $this->erro('ID inválido.');
            return;
        }

        $statusValidos = ['aberto', 'em_andamento', 'concluido'];
        if (!in_array($status, $statusValidos, true)) {
            $this->erro('Status inválido.');
            return;
        }

        // RN06 - observação final obrigatória ao concluir
        if ($status === 'concluido' && $observacaoFinal === '') {
            $this->erro('Observação final é obrigatória ao concluir o atendimento. (RN06)');
            return;
        }

        try {
            $pdo  = conectar();
            $stmt = $pdo->prepare(
                "UPDATE atendimentos
                    SET status           = :status,
                        observacao_final = :obs
                  WHERE id = :id"
            );
            $stmt->execute([
                ':status' => $status,
                ':obs'    => $observacaoFinal ?: null,
                ':id'     => $id,
            ]);

            $this->json(['mensagem' => 'Status atualizado com sucesso.']);
        } catch (PDOException $e) {
            $this->erro('Erro ao atualizar status.', 500);
        }
    }
}
