<?php

include '../../db/db.class.php';
include '../../App/php/function.php';
require_once '../../App/Models/Insumos/Controller.php';

use App\Models\Insumos\Controller;

header('Content-Type: application/json');

if (!isset($_GET['grupo_id']) || empty($_GET['grupo_id'])) {
    echo json_encode([]);
    exit;
}

$grupo_id = intval($_GET['grupo_id']);

$controller = new Controller();
$subgrupos = $controller->listarSubgruposPorGrupo($grupo_id);

echo json_encode($subgrupos);
