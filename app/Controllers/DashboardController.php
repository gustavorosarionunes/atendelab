<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Middleware/auth.php';

// ============================================================
// AtendeLab - DashboardController
// ============================================================

class DashboardController
{
    public function resumo(): void
    {
        exigirAutenticacao();
        header('Content-Type: application/json; charset=UTF-8');

        try {
            $pdo = conectar();

            $totalPessoas = (int) $pdo
                ->query("SELECT COUNT(*) FROM pessoas WHERE status = 'ativo'")
                ->fetchColumn();

            $totalTipos = (int) $pdo
                ->query("SELECT COUNT(*) FROM tipos_atendimentos WHERE status = 'ativo'")
                ->fetchColumn();

            $totalAtendimentos = (int) $pdo
                ->query("SELECT COUNT(*) FROM atendimentos")
                ->fetchColumn();

            $stmtRecentes = $pdo->query(
                "SELECT a.id,
                        p.nome  AS pessoa,
                        t.nome  AS tipo,
                        u.nome  AS responsavel,
                        a.data_atendimento,
                        a.status
                   FROM atendimentos a
                   JOIN pessoas            p ON p.id = a.pessoa_id
                   JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                   JOIN usuarios           u ON u.id = a.usuario_id
                  ORDER BY a.criado_em DESC
                  LIMIT 5"
            );
            $recentes = $stmtRecentes->fetchAll();

            echo json_encode([
                'indicadores' => [
                    'total_pessoas'      => $totalPessoas,
                    'total_tipos'        => $totalTipos,
                    'total_atendimentos' => $totalAtendimentos,
                ],
                'atendimentos_recentes' => $recentes,
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao carregar resumo.']);
        }
    }
}
