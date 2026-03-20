<?php

namespace App\Models\Cheques;

use db;

class Controller
{
    public function listar()
    {
        $db = new db();
        $db->query("SELECT * FROM forma_pagamento_cheque ORDER BY nome_cheque ASC");
        return $db->resultSet();
    }

    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT * FROM forma_pagamento_cheque WHERE id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    public function cadastro($dados)
    {
        $db = new db();

        $db->query("INSERT INTO forma_pagamento_cheque (nome_cheque, max_parcelas, juros_parcela_1, juros_parcela_2, juros_parcela_3, juros_parcela_4, juros_parcela_5, juros_parcela_6, juros_parcela_7, juros_parcela_8, juros_parcela_9, juros_parcela_10, juros_parcela_11, juros_parcela_12)
            VALUES (:nome_cheque, :max_parcelas, :juros_parcela_1, :juros_parcela_2, :juros_parcela_3, :juros_parcela_4, :juros_parcela_5, :juros_parcela_6, :juros_parcela_7, :juros_parcela_8, :juros_parcela_9, :juros_parcela_10, :juros_parcela_11, :juros_parcela_12)");

        foreach ($dados as $key => $value) {
            $db->bind(":$key", $value === '' ? null : $value);
        }

        return $db->execute();
    }

    public function editar($id, $dados)
    {
        $db = new db();
        $setFields = [];

        foreach ($dados as $key => $value) {
            $setFields[] = "$key = :$key";
        }

        $setQuery = implode(", ", $setFields);

        $db->query("UPDATE forma_pagamento_cheque SET $setQuery WHERE id = :id");

        foreach ($dados as $key => $value) {
            $db->bind(":$key", $value);
        }
        $db->bind(":id", $id);

        return $db->execute();
    }

    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM forma_pagamento_cheque WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }
}
