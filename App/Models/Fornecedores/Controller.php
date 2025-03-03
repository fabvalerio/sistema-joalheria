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
        try {
            $lista = new db();
    
            // Criação dinâmica de placeholders
            $campos = implode(", ", array_keys($dados));
            $placeholders = ":" . implode(", :", array_keys($dados));
    
            $sql = "INSERT INTO fornecedores ($campos) VALUES ($placeholders)";
            $lista->query($sql);
    
            foreach ($dados as $campo => $valor) {
                $lista->bind(":$campo", $valor);
            }
    
            if ($lista->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Cadastro realizado com sucesso!'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Erro ao cadastrar. Tente novamente mais tarde.'
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro no banco de dados: ' . $e->getMessage()
            ];
        }
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
                pix = :pix, 
                numero = :numero, 
                whatsapp = :whatsapp, 
                bairro = :bairro,
                cep = :cep
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
