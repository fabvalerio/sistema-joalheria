<?php

use App\Models\Modelo\Controller; // Altere para o nome "Modelo" para nome da Tabela do SQL

// Crie uma instância da classe do namespace desejado
$controller = new Controller();
$return = $controller->listar();

?>

<div class="card">

    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Usuários</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/cadastro"?>" class="btn btn-white text-primary">Adicionar</a>
    </div>

    <div class="card-body">

        <table id="example1" class="table table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Status</th>
                    <th width="220">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop para exibir os registros
                foreach ($return as $r) {
                    echo "<tr>";                    
                    echo "<td>" . $r['nome']  . "</td>";
                    echo "<td>" . $r['status']  . "</td>";
                    echo "<td> 
                                <div class=\"dropdown\">
                                <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                                    Ação
                                </button>
                                <ul class=\"dropdown-menu\">
                                    <li><a href=\"{$url}!/{$link[1]}/ver/{$r['id']}\" class=\"dropdown-item\">Ver</a></li>
                                    <li><a href=\"{$url}!/{$link[1]}/editar/{$r['id']}\" class=\"dropdown-item\">Editar</a></li>
                                    <li><a href=\"{$url}!/{$link[1]}/deletar/{$r['id']}\" class=\"dropdown-item text-danger\">Excluir</a></li>
                                </ul>
                                </div>
                        </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

    </div>

</div>