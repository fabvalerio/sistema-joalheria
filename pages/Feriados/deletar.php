
<?php

use App\Models\Feriados\ControllerFeriados;

$id = $link['3'];
$controller = new ControllerFeriados();
$return = $controller->ver($id);

if (!$return) {
    echo notify('danger', "Feriado não encontrado.");
    exit;
}

if (isset($link['4']) && $link['4'] == 'deletar') {
    $returnDelete = $controller->deletar($id);
    if ($returnDelete) {
        echo notify('success', "Feriado excluído com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao excluir o feriado.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Deletar Feriado</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <p>Tem certeza de que deseja excluir o feriado <strong><?= htmlspecialchars($return['descricao']) ?></strong>?</p>
        <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/deletar" ?>">Deletar</a>
    </div>
</div>
