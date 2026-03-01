<?php

namespace App\Models\TransferenciaEstoque;

use db;

class Controller
{
    public function listar($inicio = null, $fim = null, $loja_origem = null, $loja_destino = null)
    {
        $where = "";

        if (!empty($inicio) && !empty($fim)) {
            $where .= " AND t.data_transferencia BETWEEN '{$inicio}' AND '{$fim}'";
        }
        if (!empty($loja_origem)) {
            $where .= " AND t.loja_origem_id = '{$loja_origem}'";
        }
        if (!empty($loja_destino)) {
            $where .= " AND t.loja_destino_id = '{$loja_destino}'";
        }

        $db = new db();
        $db->query("
            SELECT 
                t.id,
                t.produto_id,
                p.descricao_etiqueta AS nome_produto,
                p.codigo_fabricante,
                t.quantidade,
                t.data_transferencia,
                t.observacao,
                lo.nome AS loja_origem,
                ld.nome AS loja_destino,
                u.nome_completo AS usuario
            FROM transferencia_estoque t
            LEFT JOIN produtos p ON t.produto_id = p.id
            LEFT JOIN loja lo ON t.loja_origem_id = lo.id
            LEFT JOIN loja ld ON t.loja_destino_id = ld.id
            LEFT JOIN usuarios u ON t.usuario_id = u.id
            WHERE t.id > 0 {$where}
            ORDER BY t.data_transferencia DESC
        ");
        return $db->resultSet();
    }

    public function cadastro($dados)
    {
        $db = new db();

        $db->query("
            INSERT INTO transferencia_estoque (
                produto_id, loja_origem_id, loja_destino_id, quantidade, data_transferencia, usuario_id, observacao
            ) VALUES (
                :produto_id, :loja_origem_id, :loja_destino_id, :quantidade, :data_transferencia, :usuario_id, :observacao
            )
        ");
        $db->bind(':produto_id', $dados['produto_id']);
        $db->bind(':loja_origem_id', $dados['loja_origem_id']);
        $db->bind(':loja_destino_id', $dados['loja_destino_id']);
        $db->bind(':quantidade', $dados['quantidade']);
        $db->bind(':data_transferencia', date('Y-m-d H:i:s'));
        $db->bind(':usuario_id', $dados['usuario_id']);
        $db->bind(':observacao', $dados['observacao'] ?? null);

        if (!$db->execute()) {
            return false;
        }

        $transferencia_id = $db->lastInsertId();

        // Verificar se origem é CD (usa tabela estoque) ou loja (usa estoque_loja)
        $db->query("SELECT tipo FROM loja WHERE id = :loja_id");
        $db->bind(':loja_id', $dados['loja_origem_id']);
        $lojaOrigem = $db->single();
        $tipoOrigem = $lojaOrigem['tipo'] ?? '';

        if ($tipoOrigem === 'CD') {
            // Debitar da tabela estoque (produtos_id) - pega uma linha com saldo suficiente
            $db->query("
                UPDATE estoque e
                INNER JOIN (
                    SELECT id FROM estoque
                    WHERE produtos_id = :produto_id AND quantidade >= :quantidade
                    ORDER BY id LIMIT 1
                ) t ON e.id = t.id
                SET e.quantidade = e.quantidade - :quantidade2
            ");
            $db->bind(':produto_id', $dados['produto_id']);
            $db->bind(':quantidade', $dados['quantidade']);
            $db->bind(':quantidade2', $dados['quantidade']);
        } else {
            // Debitar do estoque_loja
            $db->query("
                UPDATE estoque_loja 
                SET quantidade = quantidade - :quantidade 
                WHERE produto_id = :produto_id AND loja_id = :loja_id
            ");
            $db->bind(':quantidade', $dados['quantidade']);
            $db->bind(':produto_id', $dados['produto_id']);
            $db->bind(':loja_id', $dados['loja_origem_id']);
        }

        if (!$db->execute()) {
            return false;
        }

        // Creditar no estoque do destino (INSERT ou UPDATE)
        $db->query("
            INSERT INTO estoque_loja (loja_id, produto_id, quantidade, quantidade_minima)
            VALUES (:loja_id, :produto_id, :quantidade, 0)
            ON DUPLICATE KEY UPDATE quantidade = quantidade + VALUES(quantidade)
        ");
        $db->bind(':loja_id', $dados['loja_destino_id']);
        $db->bind(':produto_id', $dados['produto_id']);
        $db->bind(':quantidade', $dados['quantidade']);

        if (!$db->execute()) {
            return false;
        }

        // Registrar movimentação de estoque - saída da origem
        $db->query("
            INSERT INTO movimentacao_estoque (
                produto_id, descricao_produto, tipo_movimentacao, quantidade, 
                data_movimentacao, motivo, loja_id
            ) VALUES (
                :produto_id, :descricao_produto, 'Saida', :quantidade, 
                :data_movimentacao, :motivo, :loja_id
            )
        ");
        $db->bind(':produto_id', $dados['produto_id']);
        $db->bind(':descricao_produto', $dados['descricao_produto'] ?? '');
        $db->bind(':quantidade', $dados['quantidade']);
        $db->bind(':data_movimentacao', date('Y-m-d'));
        $db->bind(':motivo', 'Transferência para ' . ($dados['loja_destino_nome'] ?? 'loja'));
        $db->bind(':loja_id', $dados['loja_origem_id']);
        $db->execute();

        // Registrar movimentação de estoque - entrada no destino
        $db->query("
            INSERT INTO movimentacao_estoque (
                produto_id, descricao_produto, tipo_movimentacao, quantidade, 
                data_movimentacao, motivo, loja_id
            ) VALUES (
                :produto_id, :descricao_produto, 'Entrada', :quantidade, 
                :data_movimentacao, :motivo, :loja_id
            )
        ");
        $db->bind(':produto_id', $dados['produto_id']);
        $db->bind(':descricao_produto', $dados['descricao_produto'] ?? '');
        $db->bind(':quantidade', $dados['quantidade']);
        $db->bind(':data_movimentacao', date('Y-m-d'));
        $db->bind(':motivo', 'Transferência de ' . ($dados['loja_origem_nome'] ?? 'CD'));
        $db->bind(':loja_id', $dados['loja_destino_id']);
        $db->execute();

        return $transferencia_id;
    }

    public function listarLojas()
    {
        $db = new db();
        $db->query("SELECT id, nome, tipo FROM loja WHERE status = 1 ORDER BY tipo ASC, nome ASC");
        return $db->resultSet();
    }

    public function listarProdutosComEstoquePorLoja($loja_id)
    {
        $db = new db();
        $db->query("
            SELECT 
                p.id, 
                p.descricao_etiqueta AS nome_produto,
                p.codigo_fabricante,
                el.quantidade AS estoque
            FROM produtos p
            INNER JOIN estoque_loja el ON p.id = el.produto_id
            WHERE el.loja_id = :loja_id AND el.quantidade > 0
            ORDER BY p.descricao_etiqueta ASC
        ");
        $db->bind(':loja_id', $loja_id);
        return $db->resultSet();
    }
}
