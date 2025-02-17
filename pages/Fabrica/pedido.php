<?php

    use App\Models\fabrica\Controller;

    $id = $link[3];
    $controller = new Controller();
    $fabrica = $controller->ver($id);


    $controller = new Controller();
    $fluxo = $controller->acompanhar($fabrica['id']);

    // Verificar se o cliente foi encontrado
    if (!$fabrica) {
        echo notify('danger', "Cliente não encontrado.");
        exit;
    }



    //editar finalização da etapa
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $dados = [
            'status' => $_POST['status'] ?? null,
            'data_fim' => $_POST['data_fim'] ?? null,
            'usuario' => $_POST['usuario'] ?? null,
            'id' => $_POST['id'],
            'fabrica_id' => $_POST['fabrica_id']
        ];

        $controller = new Controller();
        $return = $controller->editarEtapa($dados);



        if ($return) {
            echo notify('success', "Conta cadastrada com sucesso!");
            echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar/' . @$link[3] . '">';
          } else {
            echo notify('danger', "Erro ao cadastrar a conta.");
          }

    }

    if( $link[5] == 'iniciar' ){

    }

?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Pedido Fábrica</h3>
  </div>

  <div class="card-body">
        <div class="row">
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Nome</label>
                <?php echo !empty($fabrica['nome_pf']) ? $fabrica['nome_pf'] : $fabrica['nome_fantasia_pj']?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Pedido</label>
                #<?php echo $fabrica['pedido_id']; ?>
            </div>
            <div class="col-lg-4">
                <label for="" class="form-label d-block fw-bold">Data Entrega</label>
                <span class="badge bg-danger">
                <?php echo dia($fabrica['data_entrega']); ?>
                </span>
            </div>
        </div>  
  </div>
</div>


<div class="card card-body mt-5">

<div class="timeline">

        <?php  
            if (!$fluxo) {
                echo notify('warning', "Nenhum resultado.");
            }else{ 
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
                                  <?php if(!empty($value['data_inicio']) AND !empty($value['data_fim'])){ ?>
                                    <small class="text-muted">Início: <?php echo dia($value['data_inicio']); ?></small>
                                    |
                                    <small class="text-muted">Termíno: <?php echo dia($value['data_fim']); ?></small>
                                    <?php 
                                    if( empty($value['data_fim']) ){
                                        echo '<a name="page"></a>';
                                        $auxFechar = 'false';
                                        echo '<a href="'.$url.'!/'.$link[1].'/'.$link[2].'/'.$link[3].'/'.$value['id'].'/finalizar/#page" class="badge bg-danger">Finalizar</a>';
                                    }

                                    if(@$link[5] == 'finalizar' && @$value['id'] == @$link[4]){
                                        ?>
                                        <form action="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/{$link[4]}/{$value['id']}" ?>" method="POST">
                                            <div class="row g-3 mt-4 d-flex align-items-end">
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
                                                        <?php if( !empty($value['data_fim']) ){ ?>
                                                        <option value="1">Em precesso</option>
                                                        <?php } ?>
                                                        <option value="2">Finalizado</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-4">
                                                    <label for="" class="form-label">
                                                        Próxima Etapa
                                                    </label>
                                                    <?php echo SelectJoin('usuarios', 'nome_completo', 'id',  'cargos', 'id', 'cargo', 't2.fabrica = 1 OR t2.id = 1', 'usuario', 'usuario')?>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input type="hidden" name="id" value="<?php echo $value['id']; ?>">
                                                    <input type="hidden" name="fabrica_id" value="<?php echo $value['fid']; ?>">
                                                    <button type="submit" class="btn btn-primary">Salvar</button>
                                                </div>
                                            </div>
                                        </form>
                                        <?php

                                    }
                                  }else{
                                    ?>
                                    <a href="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$link[3]}/{$value['id']}/iniciar" ?>" class="btn btn-primary">Iniciar</a>
                                    <?php
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
        ?>
        <div class="timeline-item">
            <div class="card">
                <div class="card-body">
                    <a href="#" class="btn btn-success <?php if( $auxFechar == 'false' ){ echo 'disabled'; }?>">Adicionar nova jornada</a>

                </div>
            </div>
        </div>


    </div>

</div>