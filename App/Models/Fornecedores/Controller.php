<?php

namespace App\Models\Fornecedores;

use db;

class Controller
{
    public function listar()
    {
        $db = new db();
        $db->query("SELECT * FROM fornecedores ORDER BY razao_social ASC");
        return $db->resultSet();
    }

    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT * FROM fornecedores WHERE id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    public function cadastro($dados)
    {
        $db = new db();
        $db->query("
            INSERT INTO fornecedores (
                razao_social, nome_fantasia, cnpj, insc_estadual, insc_municipal, 
                condicao_pagto, vigencia_acordo, telefone, email, endereco, 
                cidade, estado, contato, site, banco, numero_banco, 
                agencia, conta, pix
            ) VALUES (
                :razao_social, :nome_fantasia, :cnpj, :insc_estadual, :insc_municipal, 
                :condicao_pagto, :vigencia_acordo, :telefone, :email, :endereco, 
                :cidade, :estado, :contato, :site, :banco, :numero_banco, 
                :agencia, :conta, :pix
            )
        ");
        foreach ($dados as $key => $value) {
            $db->bind(":$key", $value);
        }
        return $db->execute();
    }

    public function editar($id, $dados)
    {
        $db = new db();
        $db->query("
            UPDATE fornecedores SET 
                razao_social = :razao_social, 
                nome_fantasia = :nome_fantasia, 
                cnpj = :cnpj, 
                insc_estadual = :insc_estadual, 
                insc_municipal = :insc_municipal, 
                condicao_pagto = :condicao_pagto, 
                vigencia_acordo = :vigencia_acordo, 
                telefone = :telefone, 
                email = :email, 
                endereco = :endereco, 
                cidade = :cidade, 
                estado = :estado, 
                contato = :contato, 
                site = :site, 
                banco = :banco, 
                numero_banco = :numero_banco, 
                agencia = :agencia, 
                conta = :conta, 
                pix = :pix 
            WHERE id = :id
        ");
        foreach ($dados as $key => $value) {
            $db->bind(":$key", $value);
        }
        $db->bind(":id", $id);
        return $db->execute();
    }

    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM fornecedores WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }
}
