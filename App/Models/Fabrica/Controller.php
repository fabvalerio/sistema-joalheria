<?php

namespace App\Models\Fabrica;
//include '.php/function.php';

use db;

class Controller
{

    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT f.*, c.nome_pf, c.nome_fantasia_pj, p.observacoes
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
        $db->query("SELECT f.*, u.nome_completo as nome, 
                                    c.cargo, f.fabrica_id as fid
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

    public function finalizarEtapa($id){
        $db = new db();
        $db->query("UPDATE fabrica_etapas SET status = :status WHERE id = :id");
        $db->bind(":status", 2);
        $db->bind(":id", $id);


        return $db->single();
    }

    public function finalizarEtapaFabrica($id){
        $db = new db();
        $db->query("UPDATE fabrica SET status = :status WHERE id = :id");
        $db->bind(":status", 'Em Producao');
        $db->bind(":id", $id);

        return $db->single();
    }

    public function encerrar($id){
        $db = new db();
        $db->query("UPDATE fabrica_etapas SET status = :status WHERE id = :id");
        $db->bind(":status", 3);
        $db->bind(":id", $id);


        return $db->single();
    }

    public function encerrarFabrica($id){
        $db = new db();
        $db->query("UPDATE fabrica SET status = :status WHERE id = :id");
        $db->bind(":status", 'Finalizado');
        $db->bind(":id", $id);

        return $db->single();
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

    public function registrarAtividades($dados){


        $reg = new db();
        $reg->query("INSERT INTO fabrica_etapas (fabrica_id, usuarios_id, status, data_inicio, data_fim ) 
                            VALUES (:fabrica_id, :usuarios_id, :status, :data_inicio, :data_fim)");
        $reg->bind(":fabrica_id", $dados['fabrica_id']);
        $reg->bind(":status", $dados['status']);
        $reg->bind(":data_inicio", $dados['data_inicio']);
        $reg->bind(":data_fim", $dados['data_fim']);
        $reg->bind(":usuarios_id", $dados['usuario']);

        return $reg->execute(); 
        
    }

    //Registrar
    public function registrar($dados)
    {

        //Listar produtos para fabrica
        $list = new db();
        $list->query("SELECT pi.id, pi.pedido_id, pi.produto_id, pi.quantidade, pi.descricao_produto, pi.fabrica, p.data_entrega
                        FROM pedidos_itens as pi
                        LEFT JOIN fabrica as f
                            ON pi.pedido_id = f.id
                        LEFT JOIN pedidos as p
                            ON pi.pedido_id = p.id
                        WHERE  pi.pedido_id = :pedido_id
                        AND pi.fabrica = '1'
                        AND f.status IS NULL ");
        $list->bind(":pedido_id", $dados);
        $lista = $list->resultSet();

        //registrando atividades
        if (empty($list->rowCount())) {
            return 'Sem registro';
        }else{
            foreach ($lista as $item) {

                $data = subtrairDiasUteis($item['data_entrega'], 2);
                $hoje = date('Y-m-d');

                $sql = "INSERT INTO fabrica (pedido_id, pedidos_itens_id, data_entrega, data_solicitacao, total)
                        VALUES ('{$dados}', '{$item['id']}', '{$data}', '{$hoje}', '{$item['quantidade']}')";
                $db = new db();
                $db->query($sql);
                $db->single();

                $data = '';

                $sqls[] =  "<div class=\"alert alert-success mb-2\" role=\"alert\">Item {$item['id']} registrado com sucesso</div>";
            }
        }

        return $sqls;


        // $db = new db();
        // $db->query("INSERT INTO fabrica (pedido_id) VALUES (:pedido_id)");
        // $db->bind(":pedido_id", $dados);
        // return $db->single();
    }
    public function registrarFabrica($id){
        $db = new db();
        $db->query("UPDATE fabrica SET status = :status WHERE id = :id");
        $db->bind(":status", 'Em Producao');
        $db->bind(":id", $id);

        return $db->single();
    }

}
?>
