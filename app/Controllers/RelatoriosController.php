<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Middleware/auth.php';

// ============================================================
// AtendeLab - RelatoriosController
// ============================================================

class RelatoriosController
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
    // GET: relatório de atendimentos por período
    // ----------------------------------------------------------
    public function atendimentos(): void
    {
        exigirAutenticacao();

        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim    = $_GET['data_fim']    ?? date('Y-m-d');

        try {
            $pdo  = conectar();
            $stmt = $pdo->prepare(
                "SELECT a.id,
                        CONCAT('ATD-', LPAD(a.id, 4, '0')) AS protocolo,
                        p.nome  AS pessoa,
                        t.nome  AS tipo,
                        u.nome  AS responsavel,
                        a.descricao,
                        a.status,
                        a.data_atendimento,
                        a.horario_atendimento,
                        a.observacao_final
                   FROM atendimentos a
                   JOIN pessoas            p ON p.id = a.pessoa_id
                   JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                   JOIN usuarios           u ON u.id = a.usuario_id
                  WHERE a.data_atendimento BETWEEN :inicio AND :fim
                  ORDER BY a.data_atendimento ASC, a.horario_atendimento ASC"
            );
            $stmt->execute([
                ':inicio' => $dataInicio,
                ':fim'    => $dataFim,
            ]);
            $registros = $stmt->fetchAll();

            $this->json([
                'periodo'    => ['inicio' => $dataInicio, 'fim' => $dataFim],
                'total'      => count($registros),
                'atendimentos' => $registros,
            ]);
        } catch (PDOException $e) {
            $this->erro('Erro ao gerar relatório.', 500);
        }
    }
}
