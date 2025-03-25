<?php

use App\Models\fabrica\Controller;

$id = $link[3];
$pid = $link[4];

$controller = new Controller();
$fabrica = $controller->ver($pid);

$fluxo = $controller->acompanhar($fabrica['id']);

// Verificar se o cliente foi encontrado
if (!$fabrica) {
    echo notify('danger', "Cliente não encontrado.");
    exit;
}



//editar finalização da etapa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($link[5] == 'inicio-atividade') {

        $dados = [
            'status' => $_POST['status'] ?? null,
            'data_inicio' => date('Y-m-d'),
            'data_fim' => $_POST['data_fim'] ?? null,
            'usuario' => $_COOKIE['id'] ?? null,
            'fabrica_id' => $_POST['fabrica_id'] ?? null
        ];

        $return = $controller->registrarAtividades($dados);
        $return = $controller->registrarFabrica($fabrica['id']);

            echo '<meta http-equiv="refresh" content="0; url=' . $url . '!/' . $link[1] . '/pedido/' . ($link[3] ?? '') . '/' . ($link[4] ?? '') . '">';
    }
}

//Encerrar
if( ($link[5] ?? '') == 'encerrar' ) {
    $controller->encerrar($link[4]);
    $controller->encerrarFabrica($fabrica['id']);

    echo '<meta http-equiv="refresh" content="3; url=' . $url . '!/Fabrica/andamento/">';
}


?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Pedido Fábrica</h3>
    </div>

    <div class="card-body">
        <div class="row g-5">
            <div class="col-lg-3">
                <label for="" class="form-label d-block fw-bold">Nome</label>
                <?php echo !empty($fabrica['nome_pf']) ? $fabrica['nome_pf'] : $fabrica['nome_fantasia_pj'] ?>
            </div>
            <div class="col-lg-2">
                <label for="" class="form-label d-block fw-bold">Fabrica</label>
                #<?php echo $fabrica['id']; ?>
            </div>
            <div class="col-lg-2">
                <label for="" class="form-label d-block fw-bold">Pedido</label>
                #<?php echo $fabrica['pedido_id']; ?>
            </div>
            <div class="col-lg-2">
                <label for="" class="form-label d-block fw-bold">Item</label>
                #<?php echo $link['4']; ?>
            </div>
            <div class="col-lg-2">
                <label for="" class="form-label d-block fw-bold">Data Entrega</label>
                <span class="badge bg-danger">
                    <?php echo dia($fabrica['data_entrega']); ?>
                </span>
            </div>
            <div class="col-12">
                <div class="alert alert-danger">
                    <label for="" class="form-label d-block fw-bold">Observações Gerais</label>
                    <?php echo ($fabrica['observacoes']); ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card card-body mt-5">

    <div class="timeline">

        <?php
        if (!$fluxo) {
        } else {
            $aux = 1;
            foreach ($fluxo as $key => $value) {
        ?>
                <div class="timeline-item">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-lightbulb-fill text-primary"></i> Etapa <?php echo $aux++; ?></h5>
                            <div><?php echo statusFabrica($value['status']); ?></d>
                                <div class="mt-3">
                                    <p>
                                        <strong><?php echo $value['nome']; ?></strong>
                                        <br>
                                        <i><?php echo $value['cargo']; ?></i>
                                    </p>
                                </div>
                                <div>
                                    <?php if (!empty($value['data_inicio']) and !empty($value['data_fim'])) { ?>
                                        <a name="page-<?php echo $value['id'];?>"></a>
                                        <small class="text-muted">Início: <?php echo dia($value['data_inicio']); ?></small>
                                        |
                                        <small class="text-muted">Termíno: <?php echo dia($value['data_fim']); ?></small>
                                        <?php
                                        if( $value['usuarios_id'] == $_COOKIE['id'] AND ($link[6] ?? '') != 'finalizar') {

                                            if (($value['status'] == 1)) {
                                                $auxFechar = 'false';
                                                echo '<a href="' . $url . '!/' . $link[1] . '/' . $link[2] . '/' . $link[3] . '/' . $link[4] . '/' . $value['id'] . '/finalizar/#page-'.$value['id'].'" class="btn btn-danger btn-sm">Finalizar</a>';
                                            }else{
                                                $auxFechar = '';
                                                echo '<spam class="badge badge-success">Finalizado</spam>';
                                            }

                                        }elseif( $link[6] == 'finalizar'){
                                            $controller->finalizarEtapa($value['id']);
                                            $controller->finalizarEtapaFabrica($fabrica['id']);
                                            //echo "<meta http-equiv=\"refresh\" content=\"2; url=\"{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/{$link[4]}\">";
                                            echo '<meta http-equiv="refresh" content="0; url=' . $url . '!/' . $link[1] . '/pedido/' . ($link[3] ?? '') . '/' . ($link[4] ?? '') . '">';    
                                            
                                        }else{
                                            echo '<spam class="badge badge-warning">Aguardando usuário finalizar</spam>';
                                        }
                                    } 
                                    ?>
                                </div>
                                <p class="card-text"><?php echo $value['descricao'] ?? ''; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
        }
        

        if (($link[5] ?? '') == 'jornada') {
            ?>
            <form class="my-3" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/{$link[4]}/inicio-atividade" ?>" method="POST">
                <div class="row g-3 mt-4 d-flex align-items-end">
                    <div class="col-12">
                        <h2>Iniciar Jornada</h2>
                    </div>
                    <div class="col-lg-3">
                        <label for="" class="form-label">
                            Data término
                        </label>
                        <input type="date" name="data_fim" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-lg-3">
                        <label for="" class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="" selected disabled>Selecione</option>
                            <option value="1">Em precesso</option>
                            <option value="2">Finalizado</option>
                            <!-- <option value="3">Pronto para entrega</option> -->
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <input type="hidden" name="usuario" value="<?php echo $$_COOKIE['id']; ?>">
                        <input type="hidden" name="id" value="<?php echo $value['id']; ?>">
                        <input type="hidden" name="pid" value="<?php echo $link[4]; ?>">
                        <input type="hidden" name="fabrica_id" value="<?php echo $fabrica['id']; ?>">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </div>
            </form>
        <?php
        }

        //echo $auxFechar;

        if ( ( empty($link[5]) AND ($auxFechar ?? '') != 'false' )AND $fabrica['status'] != 'Finalizado' ) {
        ?>
            <div class="timeline-item">
                <div class="card">
                    <div class="card-body d-flex justify-content-between">
                        <a href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/{$link[4]}/jornada" ?>" class="btn btn-success <?php if ($auxFechar == 'false') { echo 'disabled'; } ?>">Adicionar nova jornada</a>
                        <a href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/{$link[4]}/encerrar" ?>" class="btn btn-primary <?php if ($auxFechar == 'false') { echo 'disabled'; } ?>">Produto Pronto para entrega</a>
                    </div>
                </div>
            </div>
        <?php } ?>


    </div>

</div>