<?php

namespace App\Models\Feriados; // Namespace adequado à tabela 'usuario'
use db; // Importa a classe de conexão com o banco de dados


class ControllerFeriados
{
    public function listar()
    {
        $db = new db();
        $db->query("SELECT * FROM feriados ORDER BY data_feriado ASC");
        return $db->resultSet();
    }

    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT * FROM feriados WHERE id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    public function Feriado($data_feriado)
    {
        $db = new db();
        $db->query("SELECT * FROM feriados WHERE data_feriado = :data_feriado");
        $db->bind(":data_feriado", $data_feriado);
        return $db->single();
    }


    public function cadastro($dados)
    {
        $db = new db();
        $db->query("INSERT INTO feriados (data_feriado, descricao, tipo, facultativo) VALUES (:data_feriado, :descricao, :tipo, :facultativo)");
        $db->bind(":data_feriado", $dados['data_feriado']);
        $db->bind(":descricao", $dados['descricao']);
        $db->bind(":tipo", $dados['tipo']);
        $db->bind(":facultativo", $dados['facultativo']);
        return $db->execute();
    }

    public function editar($id, $dados)
    {
        $db = new db();
        $db->query("UPDATE feriados SET data_feriado = :data_feriado, descricao = :descricao, tipo = :tipo, facultativo = :facultativo WHERE id = :id");
        $db->bind(":data_feriado", $dados['data_feriado']);
        $db->bind(":descricao", $dados['descricao']);
        $db->bind(":tipo", $dados['tipo']);
        $db->bind(":facultativo", $dados['facultativo']);
        $db->bind(":id", $id);
        return $db->execute();
    }

    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM feriados WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }
}
?>
