<?php

namespace App\Models\Consignacao;

use db; // Classe de conexão com o banco

class Controller
{
    // Listar todas as consignações
    public function listar()
    {
        $db = new db();
        $db->query("
            SELECT 
                c.id, 
                c.data_consignacao, 
                c.valor, 
                c.status,
                c.desconto_percentual, 
                cl.nome_pf, 
                cl.nome_fantasia_pj,
                cl.telefone,
                cl.whatsapp,
                c.bonificacao
            FROM 
                consignacao c
            LEFT JOIN 
                clientes cl ON c.cliente_id = cl.id
            ORDER BY 
                c.data_consignacao DESC
        ");
        return $db->resultSet();
    }

    // Visualizar uma consignação e seus itens
    public function ver($id)
    {
        $db = new db();

        // Consulta principal da consignação
        $db->query("
            SELECT 
                c.id, 
                c.data_consignacao, 
                c.valor, 
                c.status, 
                c.observacao,
                c.desconto_percentual, 
                cl.nome_pf, 
                cl.nome_fantasia_pj,
                cl.telefone,
                cl.whatsapp,
                ci.id AS item_id,
                c.bonificacao
            FROM 
                consignacao c
            LEFT JOIN 
                clientes cl ON c.cliente_id = cl.id
            LEFT JOIN 
                consignacao_itens ci ON c.id = ci.consignacao_id
            WHERE 
                c.id = :id
            ORDER BY 
                ci.id ASC
        ");
        $db->bind(':id', $id);
        $consignacao = $db->single();

        // Consulta para os itens da consignação
        $db->query("
            SELECT 
                ci.id,
                ci.produto_id, 
                ci.quantidade, 
                ci.valor, 
                ci.qtd_devolvido, 
                p.descricao_etiqueta AS nome_produto,
                p.codigo_fabricante,
                p.id as produto_id
            FROM 
                consignacao_itens ci
            LEFT JOIN 
                produtos p ON ci.produto_id = p.id
            WHERE 
                ci.consignacao_id = :consignacao_id
            ORDER BY 
                p.id ASC
        ");
        $db->bind(':consignacao_id', $id);
        $itens = $db->resultSet();

        return [
            'consignacao' => $consignacao,
            'itens' => $itens
        ];
    }

    // Cadastrar uma nova consignação
    public function cadastro($dados)
    {
        $db = new db();

        $loja_id = $dados['loja_id'] ?? null;

        $db->query("
            INSERT INTO consignacao (
                cliente_id, data_consignacao, valor, status, observacao, desconto_percentual, bonificacao, loja_id
            ) VALUES (
                :cliente_id, :data_consignacao, :valor, :status, :observacao, :desconto_percentual, :bonificacao, :loja_id
            )
        ");
        $db->bind(':cliente_id', $dados['cliente_id']);
        $db->bind(':data_consignacao', $dados['data_consignacao']);
        $db->bind(':valor', $dados['valor']);
        $db->bind(':status', $dados['status']);
        $db->bind(':observacao', $dados['observacao']);
        $db->bind(':desconto_percentual', $dados['desconto_percentual']);
        $db->bind(':bonificacao', $dados['bonificacao']);
        $db->bind(':loja_id', $loja_id);

        if ($db->execute()) {
            $consignacaoId = $db->lastInsertId();

            foreach ($dados['itens'] as $item) {
                if (!isset($item['produto_id'], $item['quantidade'], $item['valor'])) {
                    continue;
                }

                $db->query("
                    INSERT INTO consignacao_itens (
                        consignacao_id, produto_id, quantidade, valor, qtd_devolvido
                    ) VALUES (
                        :consignacao_id, :produto_id, :quantidade, :valor, :qtd_devolvido
                    )
                ");
                $db->bind(':consignacao_id', $consignacaoId);
                $db->bind(':produto_id', $item['produto_id']);
                $db->bind(':quantidade', $item['quantidade']);
                $db->bind(':valor', $item['valor']);
                $db->bind(':qtd_devolvido', $item['qtd_devolvido'] ?? 0);

                if (!$db->execute()) {
                    return false;
                }

                $db->query("
                    UPDATE estoque
                    SET quantidade = quantidade - :quantidade
                    WHERE produtos_id = :produto_id
                ");
                $db->bind(':quantidade', $item['quantidade']);
                $db->bind(':produto_id', $item['produto_id']);
                $db->execute();

                if ($loja_id) {
                    $db->query("
                        UPDATE estoque_loja
                        SET quantidade = quantidade - :quantidade
                        WHERE produto_id = :produto_id AND loja_id = :loja_id
                    ");
                    $db->bind(':quantidade', $item['quantidade']);
                    $db->bind(':produto_id', $item['produto_id']);
                    $db->bind(':loja_id', $loja_id);
                    $db->execute();
                }
            }

            return true;
        }

        return false;
    }

    // Listar clientes para o select
    public function listarClientes()
    {
        $db = new db();
        $db->query("
            SELECT 
                id, nome_pf, nome_fantasia_pj, cpf, cnpj_pj
            FROM 
                clientes
            ORDER BY 
                nome_pf ASC, nome_fantasia_pj ASC
        ");
        return $db->resultSet();
    }

    // Listar produtos para o select
    public function listarProdutos()
    {
        $db = new db();
        $db->query("
            SELECT 
                p.id, 
                p.descricao_etiqueta AS nome_produto, 
                p.em_reais AS preco, 
                e.quantidade AS estoque
            FROM 
                produtos p
            LEFT JOIN 
                estoque e ON p.id = e.produtos_id
            ORDER BY 
                p.descricao_etiqueta ASC
        ");
        return $db->resultSet();
    }
    // Editar uma consignação existente
    public function editar($id, $dados)
    {
        $db = new db();

        // Atualizar o status da consignação
        $db->query("
            UPDATE consignacao
            SET 
                status = :status,
                valor = :valor,
                desconto_percentual = :desconto_percentual,
                bonificacao = :bonificacao
            WHERE id = :id
        ");
        $db->bind(':status', $dados['status']);
        $db->bind(':valor', $dados['valor']);
        $db->bind(':desconto_percentual', $dados['desconto_percentual']);
        $db->bind(':bonificacao', $dados['bonificacao']);
        $db->bind(':id', $id);

        if (!$db->execute()) {
            echo "<pre>Erro ao atualizar status da consignação.</pre>";
            return false; // Retorna falso se a atualização do status falhar
        }

        // Buscar loja_id da consignação
        $db->query("SELECT loja_id FROM consignacao WHERE id = :id");
        $db->bind(':id', $id);
        $consignacaoData = $db->single();
        $loja_id = $consignacaoData['loja_id'] ?? null;

        // Buscar quantidades devolvidas anteriores para calcular diferença
        $db->query("SELECT id, produto_id, qtd_devolvido FROM consignacao_itens WHERE consignacao_id = :cid");
        $db->bind(':cid', $id);
        $itensAntigos = $db->resultSet();
        $devolvidoAnterior = [];
        foreach ($itensAntigos as $ia) {
            $devolvidoAnterior[$ia['id']] = (float)$ia['qtd_devolvido'];
        }

        foreach ($dados['itens'] as $item) {
            $novaDevolvida = (float)$item['qtd_devolvido'];
            $anteriorDevolvida = $devolvidoAnterior[$item['id']] ?? 0;
            $diferenca = $novaDevolvida - $anteriorDevolvida;

            $db->query("
                UPDATE consignacao_itens
                SET qtd_devolvido = :qtd_devolvido
                WHERE id = :id
            ");
            $db->bind(':qtd_devolvido', $novaDevolvida);
            $db->bind(':id', $item['id']);

            if (!$db->execute()) {
                return false;
            }

            if ($diferenca > 0) {
                $db->query("
                    UPDATE estoque 
                    SET quantidade = quantidade + :quantidade
                    WHERE produtos_id = :produto_id
                ");
                $db->bind(':quantidade', $diferenca);
                $db->bind(':produto_id', $item['produto_id']);
                $db->execute();

                if ($loja_id) {
                    $db->query("
                        UPDATE estoque_loja 
                        SET quantidade = quantidade + :quantidade
                        WHERE produto_id = :produto_id AND loja_id = :loja_id
                    ");
                    $db->bind(':quantidade', $diferenca);
                    $db->bind(':produto_id', $item['produto_id']);
                    $db->bind(':loja_id', $loja_id);
                    $db->execute();
                }
            }
        }

        return true;
    }

    public function deletar($id)
    {
        $db = new db();

        // Buscar loja_id da consignação
        $db->query("SELECT loja_id FROM consignacao WHERE id = :id");
        $db->bind(":id", $id);
        $consig = $db->single();
        $loja_id = $consig['loja_id'] ?? null;

        $db->query("
            SELECT ci.produto_id, ci.quantidade, ci.qtd_devolvido 
            FROM consignacao_itens ci 
            WHERE ci.consignacao_id = :id
        ");
        $db->bind(":id", $id);
        $itens = $db->resultSet();

        foreach ($itens as $item) {
            $quantidadeDevolvida = $item['quantidade'] - $item['qtd_devolvido'];
            if ($quantidadeDevolvida > 0) {
                $db->query("
                    UPDATE estoque 
                    SET quantidade = quantidade + :quantidade 
                    WHERE produtos_id = :produto_id
                ");
                $db->bind(":quantidade", $quantidadeDevolvida);
                $db->bind(":produto_id", $item['produto_id']);
                $db->execute();

                if ($loja_id) {
                    $db->query("
                        UPDATE estoque_loja 
                        SET quantidade = quantidade + :quantidade 
                        WHERE produto_id = :produto_id AND loja_id = :loja_id
                    ");
                    $db->bind(":quantidade", $quantidadeDevolvida);
                    $db->bind(":produto_id", $item['produto_id']);
                    $db->bind(":loja_id", $loja_id);
                    $db->execute();
                }
            }
        }

        $db->query("DELETE FROM consignacao_itens WHERE consignacao_id = :id");
        $db->bind(":id", $id);
        $itensDeletados = $db->execute();

        $db->query("DELETE FROM consignacao WHERE id = :id");
        $db->bind(":id", $id);
        $consignacaoDeletada = $db->execute();

        return $itensDeletados && $consignacaoDeletada;
    }

    /**
     * Retorna os produtos em mãos de cada vendedora externa (consignações abertas).
     * Agrupa por cliente/vendedora, mostrando os itens ainda não devolvidos.
     */
    public function produtosPorVendedora($cliente_id = null)
    {
        $db = new db();
        $where = $cliente_id ? " AND c.cliente_id = '{$cliente_id}'" : "";

        $db->query("
            SELECT 
                c.id AS consignacao_id,
                c.data_consignacao,
                c.status,
                cl.id AS cliente_id,
                cl.nome_pf,
                cl.nome_fantasia_pj,
                cl.telefone,
                cl.whatsapp,
                ci.produto_id,
                p.descricao_etiqueta AS nome_produto,
                p.codigo_fabricante,
                ci.quantidade,
                ci.qtd_devolvido,
                (ci.quantidade - ci.qtd_devolvido) AS em_maos,
                ci.valor
            FROM consignacao c
            INNER JOIN clientes cl ON c.cliente_id = cl.id
            INNER JOIN consignacao_itens ci ON c.id = ci.consignacao_id
            LEFT JOIN produtos p ON ci.produto_id = p.id
            WHERE c.status = 'Aberta'
              AND (ci.quantidade - ci.qtd_devolvido) > 0
              {$where}
            ORDER BY cl.nome_pf ASC, c.data_consignacao DESC, p.descricao_etiqueta ASC
        ");

        return $db->resultSet();
    }
}
