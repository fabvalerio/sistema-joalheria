<?php

use App\Models\FinanceiroContas\Controller;

$tipo = $link['3'];

// Garantir que o tipo é válido (1 ou 2)
if (!in_array($tipo, ['R', 'P'])) {
    $tipo = null; // Se inválido, definir como null
}

// Instanciar o Controller
$controller = new Controller();

// Listar contas com o tipo especificado
$contas = $controller->listar($tipo);

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Contas Financeiras (<?php echo $tipo == 'R' ? 'Contas a Receber' : 'Contas a Pagar' ?>)</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/cadastro/{$link[3]}" ?>" class="btn btn-white text-primary">Adicionar</a>
    </div>

    <div class="card-body">
        <table id="example1" class="table table-striped">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Data Vencimento</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th width="220">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contas as $conta): ?>
                    <tr>
                        <td><?= htmlspecialchars($conta['tipo'] == 'P' ? 'Contas a Pagar' : 'Contas a Receber') ?></td>
                        <td><?= htmlspecialchars($conta['data_vencimento']) ?></td>
                        <td>R$ <?= number_format($conta['valor'], 2, ',', '.') ?></td>
                        <td><span class="badge bg-<?= $conta['status'] == 'Pago' ? 'success' : 'warning' ?>"><?= htmlspecialchars($conta['status']) ?> </span></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ação
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="<?= "{$url}!/{$link[1]}/ver/{$conta['id']}/$link[3]" ?>" class="dropdown-item">Ver</a></li>
                                    <li><a href="<?= "{$url}!/{$link[1]}/editar/{$conta['id']}/$link[3]" ?>" class="dropdown-item">Editar</a></li>
                                    <li><a href="<?= "{$url}!/{$link[1]}/deletar/{$conta['id']}/$link[3]" ?>" class="dropdown-item text-danger">Excluir</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
