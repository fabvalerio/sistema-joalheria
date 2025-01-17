<?php

namespace App\Models\FinanceiroContas;
use db;

class Controller
{
    // Listar todas as contas
    public function listar()
    {
        $db = new db();
        $db->query("SELECT * FROM financeiro_contas ORDER BY data_vencimento ASC");
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
        $db->query("INSERT INTO financeiro_contas (
                        fornecedor_id, cliente_id, categoria_id, data_vencimento, valor, data_pagamento, 
                        status, observacao, recorrente, tipo, num_parcelas, 
                        dt_par1, val_par1, dt_par2, val_par2, dt_par3, val_par3, dt_par4, val_par4, 
                        dt_par5, val_par5, dt_par6, val_par6, dt_par7, val_par7, dt_par8, val_par8, 
                        dt_par9, val_par9, dt_par10, val_par10, dt_par11, val_par11, dt_par12, val_par12
                    ) VALUES (
                        :fornecedor_id, :cliente_id, :categoria_id, :data_vencimento, :valor, :data_pagamento, 
                        :status, :observacao, :recorrente, :tipo, :num_parcelas, 
                        :dt_par1, :val_par1, :dt_par2, :val_par2, :dt_par3, :val_par3, :dt_par4, :val_par4, 
                        :dt_par5, :val_par5, :dt_par6, :val_par6, :dt_par7, :val_par7, :dt_par8, :val_par8, 
                        :dt_par9, :val_par9, :dt_par10, :val_par10, :dt_par11, :val_par11, :dt_par12, :val_par12
                    )");

        foreach ($dados as $key => $value) {
            $db->bind(":$key", $value);
        }

        return $db->execute();
    }

    // Editar uma conta
    public function editar($id, $dados)
    {
        $db = new db();
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

        foreach ($dados as $key => $value) {
            $db->bind(":$key", $value);
        }
        $db->bind(":id", $id);

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
}
?>
