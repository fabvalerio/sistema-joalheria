<?php

use App\Models\Modelo\Controller; // Altere para o nome "Modelo" para nome da Tabela do SQL

// ID para editar
$id = $link['3'];

//Exibir
$controller = new Controller();
$return = $controller->ver($id);

// Verificar se o registro foi encontrado
if (!$return) {
    echo notify('danger', "Agente não encontrado.");
    exit;
}

// Verificar se o formulário foi enviado
if(  $link['4'] == 'deletar' ){

    // Deletar com valor de $id
    $deletar =  new Controller();
    $return = $deletar->deletar($id);

    // Verificar se a edição foi realizada
    if ($return) {
        echo notify('success',"Agente deletado com sucesso!");
    } else {
        echo notify('danger', "Erro ao deletar agente.");
    }

}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Deletar Usuario</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar"?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <div class="row g-3">
                <div class="col-lg-4">
                    <label for="" class="form-label d-block">Nome</label>
                    <?php echo $return['nome'] ?? ''; ?>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label d-block">Celular</label>
                    <?php echo $return['telefone'] ?? ''; ?>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label d-block">E-mail</label>
                    <?php echo $return['email'] ?? ''; ?>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label d-block">Comissão (%)</label>
                    <?php echo $return['taxa_comissao'] ?? ''; ?>
                </div>
                <div>
                    <a class="btn btn-danger" href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/deletar"; ?>">Deletar</a>
                </div>
        </div>
    </div>

</div>