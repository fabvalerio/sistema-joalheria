<?php

namespace App\Models\Fabrica;

use db;

class Controller
{

    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT f.*, c.nome_pf, c.nome_fantasia_pj
                            FROM fabrica as f 
                            LEFT JOIN pedidos as p ON f.pedido_id = p.id
                            LEFT JOIN clientes as c ON p.cliente_id = c.id
                            WHERE f.pedido_id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    public function acompanhar($id)
    {
        $db = new db();
        $db->query("SELECT f.*, u.nome_completo as nome, c.cargo, f.fabrica_id as fid
                            FROM fabrica_etapas as f 
                            LEFT JOIN usuarios as u 
                                ON f.usuarios_id = u.id
                            LEFT JOIN cargos as c 
                                ON u.cargo = c.id
                            WHERE f.fabrica_id = :id
                            ORDER BY f.id  ASC");
        $db->bind(":id", $id);
        return $db->resultSet();
    }

    public function listaAberto()
    {
        $db = new db();
        $db->query("SELECT 
                            p.id, 
                            p.data_pedido,
                            p.forma_pagamento,
                            p.acrescimo,
                            p.desconto,
                            p.total,
                            p.valor_pago,
                            p.status_pedido,
                            p.data_entrega,
                            c.nome_pf,
                            c.nome_fantasia_pj,
                            f.status as status_fabrica,
                            u.nome_completo as nome,
                            u.cargo,
                            fe.data_inicio,
                            fe.status,
                            fe.id as fid
                        FROM pedidos as p
                        LEFT JOIN  clientes as c 
                            ON p.cliente_id = c.id
                        LEFT JOIN  fabrica as f 
                            ON p.id = f.pedido_id
                        LEFT JOIN fabrica_etapas as fe
                            ON f.id = fe.fabrica_id
                        LEFT JOIN usuarios as u
                            ON fe.usuarios_id = u.id
                        WHERE f.status = 'Em Producao' 
                        AND fe.status <> '2'
                        ORDER BY 
                            p.data_pedido ASC 
                    ");
        return $db->resultSet();
    }

    public function editarEtapa($dados){
        $db = new db();
        $db->query("UPDATE fabrica_etapas SET status = :status, data_fim = :data_fim WHERE id = :id");
        $db->bind(":status", $dados['status']);
        $db->bind(":data_fim", $dados['data_fim']);
        $db->bind(":id", $dados['id']);
        

        $dbIns = new db();
        $dbIns->query("INSERT INTO fabrica_etapas (fabrica_id, usuarios_id, status) VALUES (:fabrica_id, :usuarios_id, :status)");
        $dbIns->bind(":fabrica_id", $dados['fabrica_id']);
        $dbIns->bind(":usuarios_id", $dados['usuario']);
        $dbIns->bind(":status", 1);

        return [$db->single(), $dbIns->single()];
    }

}
?>
