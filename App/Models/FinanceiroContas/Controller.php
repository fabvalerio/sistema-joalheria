<?php

namespace App\Models\FinanceiroContas;

use db;

class Controller
{
    // Listar todas as contas
    public function listar($tipo = null)
    {
        $db = new db();

        // Construir a query base
        $query = "SELECT * FROM financeiro_contas";

        // Adicionar a cláusula WHERE se o tipo for fornecido
        if ($tipo !== null) {
            $query .= " WHERE tipo = :tipo";
        }

        // Ordenar por data de vencimento
        $query .= " ORDER BY data_vencimento ASC";

        $db->query($query);

        // Bindar o tipo se necessário
        if ($tipo !== null) {
            $db->bind(":tipo", $tipo);
        }

        return $db->resultSet();
    }


    // Ver uma conta específica
    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT * FROM financeiro_contas WHERE id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    // Cadastro de conta
    public function cadastro($dados)
    {
        $db = new db();

        // Query SQL com todos os campos da tabela
        $db->query("INSERT INTO financeiro_contas (
                        fornecedor_id, cliente_id, categoria_id, data_vencimento, valor, data_pagamento, 
                        status, observacao, recorrente, tipo, num_parcelas, 
                        val_par1, dt_par1, val_par2, dt_par2, val_par3, dt_par3, val_par4, dt_par4, 
                        val_par5, dt_par5, val_par6, dt_par6, val_par7, dt_par7, val_par8, dt_par8, 
                        val_par9, dt_par9, val_par10, dt_par10, val_par11, dt_par11, val_par12, dt_par12
                    ) VALUES (
                        :fornecedor_id, :cliente_id, :categoria_id, :data_vencimento, :valor, :data_pagamento, 
                        :status, :observacao, :recorrente, :tipo, :num_parcelas, 
                        :val_par1, :dt_par1, :val_par2, :dt_par2, :val_par3, :dt_par3, :val_par4, :dt_par4, 
                        :val_par5, :dt_par5, :val_par6, :dt_par6, :val_par7, :dt_par7, :val_par8, :dt_par8, 
                        :val_par9, :dt_par9, :val_par10, :dt_par10, :val_par11, :dt_par11, :val_par12, :dt_par12
                    )");

        // Lista de campos esperados na tabela
        $campos = [
            'fornecedor_id',
            'cliente_id',
            'categoria_id',
            'data_vencimento',
            'valor',
            'data_pagamento',
            'status',
            'observacao',
            'recorrente',
            'tipo',
            'num_parcelas',
            'val_par1',
            'dt_par1',
            'val_par2',
            'dt_par2',
            'val_par3',
            'dt_par3',
            'val_par4',
            'dt_par4',
            'val_par5',
            'dt_par5',
            'val_par6',
            'dt_par6',
            'val_par7',
            'dt_par7',
            'val_par8',
            'dt_par8',
            'val_par9',
            'dt_par9',
            'val_par10',
            'dt_par10',
            'val_par11',
            'dt_par11',
            'val_par12',
            'dt_par12'
        ];

        // Garantir que todos os campos tenham valor (ou NULL)
        foreach ($campos as $campo) {
            if (!isset($dados[$campo]) || $dados[$campo] === '') {
                $dados[$campo] = null; // Valores vazios tratados como NULL
            }
        }

        // Vincular todos os parâmetros à query
        foreach ($dados as $key => $value) {
            $db->bind(":$key", $value);
        }

        // Executar a query e retornar o resultado
        return $db->execute();
    }



    // Editar uma conta
    public function editar($id, $dados)
    {
        $db = new db();

        // Lista de campos esperados na tabela
        $campos = [
            'fornecedor_id',
            'cliente_id',
            'categoria_id',
            'data_vencimento',
            'valor',
            'data_pagamento',
            'status',
            'observacao',
            'recorrente',
            'tipo',
            'num_parcelas',
            'val_par1',
            'dt_par1',
            'val_par2',
            'dt_par2',
            'val_par3',
            'dt_par3',
            'val_par4',
            'dt_par4',
            'val_par5',
            'dt_par5',
            'val_par6',
            'dt_par6',
            'val_par7',
            'dt_par7',
            'val_par8',
            'dt_par8',
            'val_par9',
            'dt_par9',
            'val_par10',
            'dt_par10',
            'val_par11',
            'dt_par11',
            'val_par12',
            'dt_par12'
        ];

        // Garantir que todos os campos tenham valor (ou NULL)
        foreach ($campos as $campo) {
            if (!isset($dados[$campo]) || $dados[$campo] === '') {
                $dados[$campo] = null; // Valores vazios tratados como NULL
            }
        }

        // Query de atualização
        $db->query("UPDATE financeiro_contas SET
                    fornecedor_id = :fornecedor_id, cliente_id = :cliente_id, categoria_id = :categoria_id,
                    data_vencimento = :data_vencimento, valor = :valor, data_pagamento = :data_pagamento,
                    status = :status, observacao = :observacao, recorrente = :recorrente, tipo = :tipo,
                    num_parcelas = :num_parcelas,
                    dt_par1 = :dt_par1, val_par1 = :val_par1, dt_par2 = :dt_par2, val_par2 = :val_par2,
                    dt_par3 = :dt_par3, val_par3 = :val_par3, dt_par4 = :dt_par4, val_par4 = :val_par4,
                    dt_par5 = :dt_par5, val_par5 = :val_par5, dt_par6 = :dt_par6, val_par6 = :val_par6,
                    dt_par7 = :dt_par7, val_par7 = :val_par7, dt_par8 = :dt_par8, val_par8 = :val_par8,
                    dt_par9 = :dt_par9, val_par9 = :val_par9, dt_par10 = :dt_par10, val_par10 = :val_par10,
                    dt_par11 = :dt_par11, val_par11 = :val_par11, dt_par12 = :dt_par12, val_par12 = :val_par12
                WHERE id = :id");

        // Vincular parâmetros
        foreach ($dados as $key => $value) {
            $db->bind(":$key", $value);
        }
        $db->bind(":id", $id);

        // Executar a query
        return $db->execute();
    }


    // Deletar uma conta
    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM financeiro_contas WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }

    // Listar clientes (usado para Contas a Receber)
    public function listarClientes()
    {
        $db = new db();
        $db->query("SELECT id, nome_pf, razao_social_pj FROM clientes ORDER BY nome_pf ASC");
        return $db->resultSet();
    }

    // Listar fornecedores (usado para Contas a Pagar)
    public function listarFornecedores()
    {
        $db = new db();
        $db->query("SELECT id, razao_social FROM fornecedores ORDER BY razao_social ASC");
        return $db->resultSet();
    }

    // Listar categorias (usado para Contas a Pagar)
    public function listarCategorias()
    {
        $db = new db();
        $db->query("SELECT id, descricao FROM categoria_despesa ORDER BY descricao ASC");
        return $db->resultSet();
    }
    public function listarComFiltro($tipo = null, $dataInicio = null, $dataFim = null, $status = null)
{
    $db = new db();

    // Construir a query base com JOINs para obter os nomes das relações
    $query = "SELECT 
                fc.*, 
                f.nome_fantasia AS fornecedor_nome, 
                c.nome_pf AS cliente_nome,
                cd.descricao AS categoria_nome
              FROM financeiro_contas fc
              LEFT JOIN fornecedores f ON fc.fornecedor_id = f.id
              LEFT JOIN clientes c ON fc.cliente_id = c.id
              LEFT JOIN categoria_despesa cd ON fc.categoria_id = cd.id
              WHERE 1=1";

    // Filtrar por tipo (Pagar ou Receber)
    if ($tipo !== null) {
        $query .= " AND fc.tipo = :tipo";
    }

    // Filtrar por data de vencimento (entre dataInicio e dataFim)
    if (!empty($dataInicio) && !empty($dataFim)) {
        $query .= " AND fc.data_vencimento BETWEEN :dataInicio AND :dataFim";
    } elseif (!empty($dataInicio)) {
        $query .= " AND fc.data_vencimento >= :dataInicio";
    } elseif (!empty($dataFim)) {
        $query .= " AND fc.data_vencimento <= :dataFim";
    }

    // Filtrar por status (Pago, Pendente, etc.)
    if (!empty($status)) {
        $query .= " AND fc.status = :status";
    }

    // Ordenar por data de vencimento
    $query .= " ORDER BY fc.data_vencimento ASC";

    $db->query($query);

    // Bind dos parâmetros
    if ($tipo !== null) {
        $db->bind(":tipo", $tipo);
    }
    if (!empty($dataInicio)) {
        $db->bind(":dataInicio", $dataInicio);
    }
    if (!empty($dataFim)) {
        $db->bind(":dataFim", $dataFim);
    }
    if (!empty($status)) {
        $db->bind(":status", $status);
    }

    return $db->resultSet();
}

}
