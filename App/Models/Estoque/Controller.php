<?php

namespace App\Models\Estoque;

use db;

class Controller
{
    /**
     * Lista lojas disponíveis para o usuário.
     * Admin vê todas; usuário de loja vê apenas a sua.
     */
    public function listarLojas($usuario_loja_id = null, $somente_ativas = true)
    {
        $db = new db();
        $where = $somente_ativas ? " WHERE status = 1" : "";
        if (!empty($usuario_loja_id)) {
            $where .= $where ? " AND id = " . (int)$usuario_loja_id : " WHERE id = " . (int)$usuario_loja_id;
        }
        $db->query("SELECT id, nome, tipo FROM loja {$where} ORDER BY tipo ASC, nome ASC");
        return $db->resultSet();
    }

    /**
     * Retorna estoque da tabela principal 'estoque' (estoque global/CD).
     * Inclui todos os produtos (inclusive quantidade 0) para permitir adicionar estoque aos recém-cadastrados.
     */
    public function estoquePrincipal()
    {
        $db = new db();
        $db->query("
            SELECT 
                0 AS loja_id,
                'Estoque Principal' AS loja_nome,
                'CD' AS loja_tipo,
                e.produtos_id AS produto_id,
                p.descricao_etiqueta AS nome_produto,
                p.codigo_fabricante,
                COALESCE(SUM(e.quantidade), 0) AS quantidade,
                MAX(e.quantidade_minima) AS quantidade_minima
            FROM estoque e
            LEFT JOIN produtos p ON e.produtos_id = p.id
            WHERE p.id IS NOT NULL
            GROUP BY e.produtos_id, p.descricao_etiqueta, p.codigo_fabricante
            ORDER BY p.descricao_etiqueta ASC
        ");
        return $db->resultSet();
    }

    /**
     * Retorna estoque agrupado por loja (estoque_loja).
     * Se $loja_id informado, filtra por essa loja.
     */
    public function estoquePorLoja($loja_id = null)
    {
        $db = new db();
        $where = "";
        if (!empty($loja_id)) {
            $where = " AND el.loja_id = " . (int)$loja_id;
        }

        $db->query("
            SELECT 
                el.loja_id,
                l.nome AS loja_nome,
                l.tipo AS loja_tipo,
                el.produto_id,
                p.descricao_etiqueta AS nome_produto,
                p.codigo_fabricante,
                el.quantidade,
                el.quantidade_minima
            FROM estoque_loja el
            INNER JOIN loja l ON el.loja_id = l.id
            LEFT JOIN produtos p ON el.produto_id = p.id
            WHERE el.quantidade > 0 {$where}
            ORDER BY l.tipo ASC, l.nome ASC, p.descricao_etiqueta ASC
        ");
        return $db->resultSet();
    }

    /**
     * Verifica se a loja é do tipo CD (Centro de Distribuição).
     */
    public function lojaEhCD($loja_id)
    {
        if (empty($loja_id) && $loja_id !== 0 && $loja_id !== '0') {
            return false;
        }
        $db = new db();
        $db->query("SELECT tipo FROM loja WHERE id = :id LIMIT 1");
        $db->bind(':id', (int)$loja_id);
        $row = $db->single();
        return $row && isset($row['tipo']) && $row['tipo'] === 'CD';
    }

    /**
     * Retorna o ID da primeira loja do tipo CD (para movimentações).
     * @param db|null $db Instância de conexão a reutilizar (evita criar nova dentro de transação).
     */
    public function getCDLojaId($db = null)
    {
        if ($db === null) {
            $db = new db();
        }
        $db->query("SELECT id FROM loja WHERE status = 1 AND tipo = 'CD' ORDER BY id ASC LIMIT 1");
        $row = $db->single();
        return $row ? (int)$row['id'] : null;
    }

    /**
     * Adiciona quantidade ao estoque do CD e registra movimentação (Entrada).
     * @param int $produto_id
     * @param float $quantidade
     * @param string $descricao_produto
     * @param float|null $quantidade_minima Opcional - atualiza em todos os registros do produto
     * @return array ['ok' => bool, 'msg' => string]
     */
    public function adicionarEstoqueCD($produto_id, $quantidade, $descricao_produto = '', $quantidade_minima = null)
    {
        $quantidade = (float)$quantidade;
        if ($quantidade <= 0) {
            return ['ok' => false, 'msg' => 'Quantidade inválida.'];
        }

        $db = new db();
        try {
            $db->beginTransaction();

            if ($quantidade_minima !== null) {
                $db->query("UPDATE estoque SET quantidade_minima = :qtd_min WHERE produtos_id = :pid");
                $db->bind(':qtd_min', (float)$quantidade_minima);
                $db->bind(':pid', (int)$produto_id);
                $db->execute();
            }

            $totalAntes = 0;
            $db->query("SELECT COALESCE(SUM(quantidade), 0) as total FROM estoque WHERE produtos_id = :pid");
            $db->bind(':pid', (int)$produto_id);
            $r = $db->single();
            if ($r) {
                $totalAntes = (float)($r['total'] ?? 0);
            }

            $qtdMinVal = $quantidade_minima !== null ? max(0, min(99999, (float)$quantidade_minima)) : null;
            $db->query("SELECT id FROM estoque WHERE produtos_id = :pid LIMIT 1");
            $db->bind(':pid', (int)$produto_id);
            $existe = $db->single();
            if ($existe) {
                $db->query("UPDATE estoque SET quantidade = quantidade + :qtd" . ($qtdMinVal !== null ? ", quantidade_minima = :qtd_min" : "") . " WHERE produtos_id = :pid");
                $db->bind(':qtd', $quantidade);
                if ($qtdMinVal !== null) $db->bind(':qtd_min', $qtdMinVal);
                $db->bind(':pid', (int)$produto_id);
                $db->execute();
            } else {
                $qtdMinBind = $qtdMinVal !== null ? $qtdMinVal : null;
                $db->query("INSERT INTO estoque (produtos_id, quantidade, quantidade_minima, entrada_mercadorias_id) VALUES (:pid, :qtd, :qtd_min, NULL)");
                $db->bind(':pid', (int)$produto_id);
                $db->bind(':qtd', $quantidade);
                $db->bind(':qtd_min', $qtdMinBind);
                $db->execute();
            }

            $totalDepois = $totalAntes + $quantidade;
            $lojaId = $this->getCDLojaId($db);

            $db->query("
                INSERT INTO movimentacao_estoque (
                    produto_id, descricao_produto, tipo_movimentacao, quantidade,
                    data_movimentacao, motivo, estoque_antes, estoque_atualizado, loja_id
                ) VALUES (
                    :pid, :desc, 'Entrada', :qtd,
                    :data, :motivo, :antes, :depois, :loja_id
                )
            ");
            $db->bind(':pid', (int)$produto_id);
            $db->bind(':desc', $descricao_produto);
            $db->bind(':qtd', $quantidade);
            $db->bind(':data', date('Y-m-d'));
            $db->bind(':motivo', 'Ajuste manual - Adicionar');
            $db->bind(':antes', $totalAntes);
            $db->bind(':depois', $totalDepois);
            $db->bind(':loja_id', $lojaId);
            $db->execute();

            $db->endTransaction();
            return ['ok' => true, 'msg' => 'Estoque adicionado com sucesso.'];
        } catch (\Throwable $e) {
            try {
                if ($db->inTransaction()) {
                    $db->cancelTransaction();
                }
            } catch (\Throwable $ignored) {}
            return ['ok' => false, 'msg' => 'Erro: ' . $e->getMessage()];
        }
    }

    /**
     * Ajusta quantidade do estoque do CD para um novo valor e registra movimentação (Ajuste).
     * @param int $produto_id
     * @param float $quantidade_nova
     * @param string $descricao_produto
     * @param float|null $quantidade_minima Opcional - atualiza em todos os registros do produto
     * @return array ['ok' => bool, 'msg' => string]
     */
    public function ajustarEstoqueCD($produto_id, $quantidade_nova, $descricao_produto = '', $quantidade_minima = null)
    {
        $quantidade_nova = (float)$quantidade_nova;
        if ($quantidade_nova < 0) {
            return ['ok' => false, 'msg' => 'Quantidade inválida.'];
        }

        $db = new db();
        try {
            $db->beginTransaction();

            if ($quantidade_minima !== null) {
                $db->query("UPDATE estoque SET quantidade_minima = :qtd_min WHERE produtos_id = :pid");
                $db->bind(':qtd_min', (float)$quantidade_minima);
                $db->bind(':pid', (int)$produto_id);
                $db->execute();
            }

            $db->query("SELECT COALESCE(SUM(quantidade), 0) as total FROM estoque WHERE produtos_id = :pid");
            $db->bind(':pid', (int)$produto_id);
            $r = $db->single();
            $totalAntes = $r ? (float)($r['total'] ?? 0) : 0;

            $diff = $quantidade_nova - $totalAntes;
            if ($diff == 0) {
                if ($quantidade_minima === null) {
                    $db->cancelTransaction();
                    return ['ok' => true, 'msg' => 'Nenhuma alteração necessária.'];
                }
                $db->endTransaction();
                return ['ok' => true, 'msg' => 'Quantidade mínima atualizada com sucesso.'];
            }

            if ($diff > 0) {
                $qtdMinVal = $quantidade_minima !== null ? max(0, min(99999, (float)$quantidade_minima)) : null;
                $db->query("SELECT id FROM estoque WHERE produtos_id = :pid LIMIT 1");
                $db->bind(':pid', (int)$produto_id);
                $existe = $db->single();
                if ($existe) {
                    $db->query("UPDATE estoque SET quantidade = quantidade + :qtd" . ($qtdMinVal !== null ? ", quantidade_minima = :qtd_min" : "") . " WHERE produtos_id = :pid");
                    $db->bind(':qtd', $diff);
                    if ($qtdMinVal !== null) $db->bind(':qtd_min', $qtdMinVal);
                    $db->bind(':pid', (int)$produto_id);
                    $db->execute();
                } else {
                    $qtdMinBind = $qtdMinVal !== null ? $qtdMinVal : null;
                    $db->query("INSERT INTO estoque (produtos_id, quantidade, quantidade_minima, entrada_mercadorias_id) VALUES (:pid, :qtd, :qtd_min, NULL)");
                    $db->bind(':pid', (int)$produto_id);
                    $db->bind(':qtd', $diff);
                    $db->bind(':qtd_min', $qtdMinBind);
                    $db->execute();
                }
            } else {
                $toRemove = abs($diff);
                $db->query("SELECT id, quantidade FROM estoque WHERE produtos_id = :pid AND quantidade > 0 ORDER BY id ASC");
                $db->bind(':pid', (int)$produto_id);
                $rows = $db->resultSet();
                foreach ($rows as $row) {
                    if ($toRemove <= 0) break;
                    $qtd = (float)$row['quantidade'];
                    $id = $row['id'];
                    if ($qtd >= $toRemove) {
                        $db->query("UPDATE estoque SET quantidade = quantidade - :rem WHERE id = :id");
                        $db->bind(':rem', $toRemove);
                        $db->bind(':id', $id);
                        $db->execute();
                        $toRemove = 0;
                    } else {
                        $db->query("UPDATE estoque SET quantidade = 0 WHERE id = :id");
                        $db->bind(':id', $id);
                        $db->execute();
                        $toRemove -= $qtd;
                    }
                }
                if ($toRemove > 0) {
                    $db->cancelTransaction();
                    return ['ok' => false, 'msg' => 'Quantidade insuficiente no estoque.'];
                }
            }

            $lojaId = $this->getCDLojaId($db);
            $db->query("
                INSERT INTO movimentacao_estoque (
                    produto_id, descricao_produto, tipo_movimentacao, quantidade,
                    data_movimentacao, motivo, estoque_antes, estoque_atualizado, loja_id
                ) VALUES (
                    :pid, :desc, 'Ajuste', :qtd,
                    :data, :motivo, :antes, :depois, :loja_id
                )
            ");
            $db->bind(':pid', (int)$produto_id);
            $db->bind(':desc', $descricao_produto);
            $db->bind(':qtd', abs($diff));
            $db->bind(':data', date('Y-m-d'));
            $db->bind(':motivo', 'Ajuste manual - Editar');
            $db->bind(':antes', $totalAntes);
            $db->bind(':depois', $quantidade_nova);
            $db->bind(':loja_id', $lojaId);
            $db->execute();

            $db->endTransaction();
            return ['ok' => true, 'msg' => 'Estoque ajustado com sucesso.'];
        } catch (\Throwable $e) {
            try {
                if ($db->inTransaction()) {
                    $db->cancelTransaction();
                }
            } catch (\Throwable $ignored) {}
            return ['ok' => false, 'msg' => 'Erro: ' . $e->getMessage()];
        }
    }

    /**
     * Adiciona quantidade ao estoque de uma loja (estoque_loja) e registra movimentação.
     * @param int $loja_id
     * @param int $produto_id
     * @param float $quantidade
     * @param string $descricao_produto
     * @param float|null $quantidade_minima Opcional
     * @return array ['ok' => bool, 'msg' => string]
     */
    public function adicionarEstoqueLoja($loja_id, $produto_id, $quantidade, $descricao_produto = '', $quantidade_minima = null)
    {
        $loja_id = (int)$loja_id;
        $produto_id = (int)$produto_id;
        $quantidade = (float)$quantidade;

        if ($loja_id <= 0) {
            return ['ok' => false, 'msg' => 'Loja inválida.'];
        }
        if ($quantidade <= 0) {
            return ['ok' => false, 'msg' => 'Quantidade inválida.'];
        }

        $db = new db();
        try {
            $db->beginTransaction();

            $db->query("SELECT COALESCE(quantidade, 0) as total FROM estoque_loja WHERE produto_id = :pid AND loja_id = :lid");
            $db->bind(':pid', $produto_id);
            $db->bind(':lid', $loja_id);
            $r = $db->single();
            $totalAntes = $r ? (float)($r['total'] ?? 0) : 0;

            $qtdMinVal = null;
            if ($quantidade_minima !== null && $quantidade_minima !== '') {
                $qtdMinVal = max(0, min(99999, (float)str_replace(',', '.', (string)$quantidade_minima)));
            }

            $db->query("
                INSERT INTO estoque_loja (loja_id, produto_id, quantidade, quantidade_minima)
                VALUES (:lid, :pid, :qtd, :qtd_min)
                ON DUPLICATE KEY UPDATE 
                    quantidade = quantidade + :qtd2" .
                ($qtdMinVal !== null ? ", quantidade_minima = :qtd_min2" : "") . "
            ");
            $db->bind(':lid', $loja_id);
            $db->bind(':pid', $produto_id);
            $db->bind(':qtd', $quantidade);
            $db->bind(':qtd_min', $qtdMinVal);
            $db->bind(':qtd2', $quantidade);
            if ($qtdMinVal !== null) {
                $db->bind(':qtd_min2', $qtdMinVal);
            }
            $db->execute();

            $totalDepois = $totalAntes + $quantidade;

            $db->query("
                INSERT INTO movimentacao_estoque (
                    produto_id, descricao_produto, tipo_movimentacao, quantidade,
                    data_movimentacao, motivo, estoque_antes, estoque_atualizado, loja_id
                ) VALUES (
                    :pid, :desc, 'Entrada', :qtd,
                    :data, :motivo, :antes, :depois, :lid
                )
            ");
            $db->bind(':pid', $produto_id);
            $db->bind(':desc', $descricao_produto);
            $db->bind(':qtd', $quantidade);
            $db->bind(':data', date('Y-m-d'));
            $db->bind(':motivo', 'Ajuste manual - Adicionar');
            $db->bind(':antes', $totalAntes);
            $db->bind(':depois', $totalDepois);
            $db->bind(':lid', $loja_id);
            $db->execute();

            $db->endTransaction();
            return ['ok' => true, 'msg' => 'Estoque adicionado com sucesso.'];
        } catch (\Throwable $e) {
            try {
                if ($db->inTransaction()) {
                    $db->cancelTransaction();
                }
            } catch (\Throwable $ignored) {}
            return ['ok' => false, 'msg' => 'Erro: ' . $e->getMessage()];
        }
    }

    /**
     * Retorna estoque unificado: tabela 'estoque' + estoque_loja.
     * - loja_id=null ou '': Todas (Estoque Principal + estoque_loja)
     * - loja_id='0' ou loja CD: Estoque Principal (tabela estoque)
     * - loja_id=id (loja física): estoque_loja dessa loja
     */
    public function estoqueUnificado($loja_id = null)
    {
        $resultado = [];

        // Apenas Estoque Principal (valor explícito 0 ou loja do tipo CD)
        if ($loja_id === '0' || $loja_id === 0 || $this->lojaEhCD($loja_id)) {
            return $this->estoquePrincipal();
        }

        // Todas: Estoque Principal + estoque_loja
        if (empty($loja_id)) {
            $resultado = array_merge($resultado, $this->estoquePrincipal());
        }

        // estoque_loja (todas as lojas ou loja específica)
        $porLoja = $this->estoquePorLoja(empty($loja_id) ? null : $loja_id);
        $resultado = array_merge($resultado, $porLoja);

        return $resultado;
    }
}
