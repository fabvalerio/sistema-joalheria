<?php

namespace App\Models\Relatorios;

use db;

class Controller
{
    // Listar todas as contas
    public function listar($tipo = null, $inicio = null, $fim = null)
    {
        $where = "";

        // Garantir que o tipo seja valido ('R' para Receber, 'P' para Pagar ou null para todos)
        if(!empty($tipo) && !in_array($tipo, ['R', 'P'])) {
            $where .= " AND tipo = '{$tipo}'";
        }

        if(!empty($inicio) && !empty($fim)) {
            $where .= " AND data_vencimento BETWEEN '{$inicio}' AND '{$fim}'";
        }

        $db = new db();

        // Construir a query base
        $query = "SELECT * FROM financeiro_contas WHERE id > 0 " . $where . 'ORDER BY data_vencimento ASC';



        $db->query($query);


        return $db->resultSet();
    }
    // Listar todas as contas
    public function soma($inicio = null, $fim = null)
    {
        $where = "";

        if(!empty($inicio) && !empty($fim)) {
            $where .= " AND data_vencimento BETWEEN '{$inicio }' AND '{$fim}'";
        }

        
        $db = new db();

        // Construir a query base
        $query = "SELECT *,
                    (SELECT SUM(valor) FROM financeiro_contas WHERE tipo = 'R' {$where}) AS total_receber,
                    (SELECT SUM(valor) FROM financeiro_contas WHERE tipo = 'P' {$where}) AS total_pagar
                    FROM financeiro_contas
                    ORDER BY data_vencimento ASC";




        $db->query($query);


        return $db->resultSet();
    }


}
