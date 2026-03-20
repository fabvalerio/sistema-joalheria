<?php
include '../../db/db.class.php';
include '../../App/php/function.php';

$db = new db();
$db->query("SELECT * FROM forma_pagamento_material ORDER BY tipo_material ASC");
$materiais = $db->resultSet();

header('Content-Type: application/json; charset=utf-8');
echo json_encode($materiais);
