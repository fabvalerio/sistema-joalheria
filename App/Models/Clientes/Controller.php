<?php

namespace App\Models\Clientes;

use db;

class Controller
{
    public function listar()
    {
        $db = new db();
        $db->query("SELECT * FROM clientes ORDER BY id ASC");
        return $db->resultSet();
    }

    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT * FROM clientes WHERE id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    public function cadastro($dados)
    {
        $db = new db();
        $db->query("INSERT INTO clientes (tipo_cliente, nome_pf, razao_social_pj, nome_fantasia_pj, perfil, telefone, whatsapp, email, rg, cpf, ie_pj, cnpj_pj, cep, endereco, bairro, cidade, estado, data_nascimento, tags, origem_contato, estado_civil, corporativo, grupo) 
                    VALUES (:tipo_cliente, :nome_pf, :razao_social_pj, :nome_fantasia_pj, :perfil, :telefone, :whatsapp, :email, :rg, :cpf, :ie_pj, :cnpj_pj, :cep, :endereco, :bairro, :cidade, :estado, :data_nascimento, :tags, :origem_contato, :estado_civil, :corporativo, :grupo)");
        foreach ($dados as $key => $value) {
            $db->bind(":$key", $value);
        }
        return $db->execute();
    }

    public function editar($id, $dados)
    {
        $db = new db();
        $fields = [];
        foreach ($dados as $key => $value) {
            $fields[] = "$key = :$key";
        }
        $sql = "UPDATE clientes SET " . implode(", ", $fields) . " WHERE id = :id";
        $db->query($sql);
        foreach ($dados as $key => $value) {
            $db->bind(":$key", $value);
        }
        $db->bind(":id", $id);
        return $db->execute();
    }

    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM clientes WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }

    public function listarGrupos()
    {
        $db = new db();
        $db->query("SELECT * FROM grupo_clientes ORDER BY id ASC");
        return $db->resultSet();
    }
}
?>
