<?php

use App\Models\Usuarios\Controller; // Altere para o namespace correto

// ID do registro a ser editado
$id = $link['3'];

// Buscar os dados do registro para preencher o formulário
$controller = new Controller();
$return = $controller->ver($id);


// Verificar se o registro foi encontrado
if (!$return) {
    echo notify('danger', "Usuário não encontrado.");
    exit;
}

$cargos = $controller->cargos();
$lojas = $controller->listarLojas();

// Obter a lista de diretórios disponíveis
$diretorios = $controller->listarDiretorios();

// Decodificar as permissões do banco (se existirem)
$permissoesUsuario = !empty($return['permissoes']) ? json_decode($return['permissoes'], true) : [];
if (!is_array($permissoesUsuario)) {
    $permissoesUsuario = [];
}

/** Perfil cadastrado como colaborador da fábrica (sem coluna dedicada no banco). */
$colaboradorFabricaInicial = ($return['nivel_acesso'] ?? '') === 'Operador';
if ($colaboradorFabricaInicial) {
    $fabricaAtiva = !empty($permissoesUsuario['Fabrica']['visualizar']) || !empty($permissoesUsuario['Fabrica']['manipular']);
    $outroModuloAtivo = false;
    foreach ($diretorios as $dir) {
        if ($dir === 'Fabrica') {
            continue;
        }
        $p = $permissoesUsuario[$dir] ?? [];
        if (!empty($p['visualizar']) || !empty($p['manipular'])) {
            $outroModuloAtivo = true;
            break;
        }
    }
    $colaboradorFabricaInicial = $fabricaAtiva && !$outroModuloAtivo;
} else {
    $colaboradorFabricaInicial = false;
}

// Atualizar o registro se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ehColaboradorFabrica = isset($_POST['colaborador_fabrica']) && $_POST['colaborador_fabrica'] === '1';

    $dados = [
        'nome_completo' => $_POST['nome_completo'],
        'email' => $_POST['email'],
        'cargo' => $_POST['cargo'],
        'telefone' => $_POST['telefone'],
        'rg' => $_POST['rg'],
        'emissao_rg' => $_POST['emissao_rg'],
        'cpf' => $_POST['cpf'],
        'data_nascimento' => $_POST['data_nascimento'],
        'cep' => $_POST['cep'],
        'endereco' => $_POST['endereco'],
        'cidade' => $_POST['cidade'],
        'estado' => $_POST['estado'],
        'login' => $_POST['login'],
        'nivel_acesso' => $ehColaboradorFabrica ? 'Operador' : $_POST['nivel_acesso'],
        'bairro' => $_POST['bairro'],
        'numero' => $_POST['numero'],
        'status' => $_POST['status'],
        'loja_id' => $ehColaboradorFabrica ? null : (!empty($_POST['loja_id']) ? (int) $_POST['loja_id'] : null)
    ];

    // Atualizar a senha apenas se for informada no formulário
    if (!empty($_POST['senha'])) {
        $dados['senha'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    }

    if ($ehColaboradorFabrica) {
        $permissoesUsuario = [];
        foreach ($diretorios as $dir) {
            $permissoesUsuario[$dir] = $dir === 'Fabrica'
                ? ['visualizar' => true, 'manipular' => true]
                : ['visualizar' => false, 'manipular' => false];
        }
        $dados['permissoes'] = json_encode($permissoesUsuario);
    } elseif (isset($_POST['permissoes'])) {
        // Atualizar permissões - iterar sobre TODOS os diretórios para salvar explicitamente
        // (checkboxes desmarcados não são enviados no POST, então precisamos tratar cada módulo)
        $permissoesUsuario = [];
        foreach ($diretorios as $dir) {
            $perms = $_POST['permissoes'][$dir] ?? [];
            $permissoesUsuario[$dir] = [
                "visualizar" => !empty($perms['visualizar']),
                "manipular" => !empty($perms['manipular'])
            ];
        }
        $dados['permissoes'] = json_encode($permissoesUsuario);
    }

    $returnUpdate = $controller->editar($id, $dados);

    if ($returnUpdate) {
        echo notify('success', "Usuário atualizado com sucesso! O usuário precisa fazer logout e login novamente para que as alterações nas permissões tenham efeito.");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao atualizar o usuário.");
    }
}

?>

<div class="card">

    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Editar Usuário</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}/{$id}" ?>" class="needs-validation" novalidate>

            <div class="row g-3">
                <div class="col-lg-4">
                    <label for="colaborador_fabrica" class="form-label">Colaborador da Fábrica</label>
                    <select class="form-select" name="colaborador_fabrica" id="colaborador_fabrica" required>
                        <option value="0" <?= !$colaboradorFabricaInicial ? 'selected' : '' ?>>Não</option>
                        <option value="1" <?= $colaboradorFabricaInicial ? 'selected' : '' ?>>Sim</option>
                    </select>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control" name="nome_completo" value="<?= $return['nome_completo'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">E-mail</label>
                    <input type="email" class="form-control" name="email" value="<?= $return['email'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Cargo</label>
                    <select class="form-select" name="cargo" required>
                        <option value="">Selecione</option>
                        <?php foreach ($cargos as $cargo) : ?>
                            <option
                                value="<?= htmlspecialchars($cargo['id']) ?>"
                                <?= ($return['cargo'] == $cargo['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cargo['cargo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="col-lg-4">
                    <label for="" class="form-label">Telefone</label>
                    <input type="text" class="form-control" name="telefone" value="<?= $return['telefone'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">RG</label>
                    <input type="text" class="form-control" name="rg" value="<?= $return['rg'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Data de Emissão do RG</label>
                    <input type="date" class="form-control" name="emissao_rg" value="<?= $return['emissao_rg'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">CPF</label>
                    <input type="text" class="form-control" name="cpf" value="<?= $return['cpf'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Data de Nascimento</label>
                    <input type="date" class="form-control" name="data_nascimento" value="<?= $return['data_nascimento'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">CEP</label>
                    <input type="text" class="form-control" id="cep" name="cep" value="<?= $return['cep'] ?>" required>
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="endereco" name="endereco" value="<?= $return['endereco'] ?>" required>
                </div>
                <div class="col-lg-2">
                    <label for="" class="form-label">N°</label>
                    <input type="text" class="form-control" name="numero" value="<?= $return['numero'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade" value="<?= $return['cidade'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Bairro</label>
                    <input type="text" class="form-control" id="bairro" name="bairro" value="<?= $return['bairro'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Estado</label>
                    <input type="text" class="form-control" id="estado" name="estado" value="<?= $return['estado'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Login</label>
                    <input type="text" class="form-control" name="login" value="<?= $return['login'] ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Senha (Deixe em branco para não alterar)</label>
                    <input type="password" class="form-control" name="senha">
                </div>
                <div class="col-lg-4" id="wrap-nivel-acesso">
                    <label for="nivel_acesso" class="form-label">Nível de Acesso</label>
                    <select class="form-select" name="nivel_acesso" id="nivel_acesso" required
                        data-initial-value="<?= htmlspecialchars($return['nivel_acesso'] ?? '') ?>">
                        <option value="">Selecione o Nível de Acesso</option>
                        <option value="Administrador" <?= $return['nivel_acesso'] == 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                        <option value="Operador" <?= $return['nivel_acesso'] == 'Operador' ? 'selected' : '' ?>>Operador</option>
                        <option value="Consulta" <?= $return['nivel_acesso'] == 'Consulta' ? 'selected' : '' ?>>Consulta</option>
                    </select>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                        <option value="1" <?= $return['status'] == 1 ? 'selected' : '' ?>>Ativo</option>
                        <option value="0" <?= $return['status'] == 0 ? 'selected' : '' ?>>Inativo</option>
                    </select>
                </div>
                <div class="col-lg-4" id="wrap-loja">
                    <label for="loja_id" class="form-label">Loja</label>
                    <select class="form-select" name="loja_id" id="loja_id"
                        data-initial-value="<?= htmlspecialchars((string) ($return['loja_id'] ?? '')) ?>">
                        <option value="">Selecione a Loja</option>
                        <?php foreach ($lojas as $loja): ?>
                            <option value="<?= $loja['id'] ?>" <?= ($return['loja_id'] ?? '') == $loja['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($loja['nome']) ?> (<?= $loja['tipo'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <hr>
                <h2>Permissões</h2>
                <div class="mb-3" id="wrap-permissoes-acoes">
                    <button type="button" class="btn btn-outline-primary btn-sm me-2" id="selectAllPermissions">Selecionar Todos</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="deselectAllPermissions">Desmarcar Todos</button>
                </div>
                <p class="text-muted small">Cada permissão controla o módulo correspondente no menu. Visualizar: exibe o menu e permite listar/ver. Manipular: permite cadastrar, editar e excluir.</p>
                <div class="row" id="permissionsContainer">
                    <?php foreach ($diretorios as $dir): ?>
                        <div class="col-lg-4 mb-3" data-modulo="<?= htmlspecialchars($dir) ?>">
                            <label class="form-label fw-bold d-block"><?= htmlspecialchars($dir) ?> <small class="text-muted">(menu)</small></label>

                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input permissao-checkbox permissao-visualizar"
                                    type="checkbox"
                                    name="permissoes[<?= htmlspecialchars($dir) ?>][visualizar]"
                                    value="1"
                                    id="visualizar_<?= htmlspecialchars($dir) ?>"
                                    <?= isset($permissoesUsuario[$dir]['visualizar']) && $permissoesUsuario[$dir]['visualizar'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="visualizar_<?= htmlspecialchars($dir) ?>">Visualizar</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input permissao-checkbox permissao-manipular"
                                    type="checkbox"
                                    name="permissoes[<?= htmlspecialchars($dir) ?>][manipular]"
                                    value="1"
                                    id="manipular_<?= htmlspecialchars($dir) ?>"
                                    data-modulo="<?= htmlspecialchars($dir) ?>"
                                    <?= isset($permissoesUsuario[$dir]['manipular']) && $permissoesUsuario[$dir]['manipular'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="manipular_<?= htmlspecialchars($dir) ?>">Manipular</label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar Alterações</button>
                </div>
            </div>

        </form>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        'use strict'
        const MODULO_FABRICA = 'Fabrica'
        const selFabrica = document.getElementById('colaborador_fabrica')
        const wrapNivel = document.getElementById('wrap-nivel-acesso')
        const wrapLoja = document.getElementById('wrap-loja')
        const wrapPermAcoes = document.getElementById('wrap-permissoes-acoes')
        const nivelAcesso = document.getElementById('nivel_acesso')
        const lojaId = document.getElementById('loja_id')

        const permStateInitial = new Map()
        document.querySelectorAll('#permissionsContainer [data-modulo] .permissao-checkbox').forEach(cb => {
            permStateInitial.set(cb.id, cb.checked)
        })
        let permSnapshotAntesFabrica = null
        let nivelLojaSnapshotAntesFabrica = null

        function aplicarModoColaboradorFabrica(ativo) {
            if (ativo) {
                wrapNivel.classList.add('d-none')
                nivelAcesso.value = 'Operador'
                nivelAcesso.removeAttribute('required')
                nivelAcesso.setAttribute('disabled', 'disabled')

                wrapLoja.classList.add('d-none')
                lojaId.value = ''
                lojaId.setAttribute('disabled', 'disabled')

                wrapPermAcoes?.classList.add('d-none')

                document.querySelectorAll('#permissionsContainer [data-modulo]').forEach(el => {
                    const mod = el.getAttribute('data-modulo')
                    const vis = el.querySelector('.permissao-visualizar')
                    const man = el.querySelector('.permissao-manipular')
                    if (mod === MODULO_FABRICA) {
                        el.classList.remove('d-none')
                        if (vis) { vis.checked = true; vis.disabled = true }
                        if (man) { man.checked = true; man.disabled = true }
                    } else {
                        el.classList.add('d-none')
                        if (vis) { vis.checked = false; vis.disabled = true }
                        if (man) { man.checked = false; man.disabled = true }
                    }
                })
            } else {
                wrapNivel.classList.remove('d-none')
                nivelAcesso.setAttribute('required', 'required')
                nivelAcesso.removeAttribute('disabled')
                if (nivelLojaSnapshotAntesFabrica) {
                    nivelAcesso.value = nivelLojaSnapshotAntesFabrica.nivel
                } else {
                    const nv = nivelAcesso.dataset.initialValue || ''
                    if (nv) nivelAcesso.value = nv
                }

                wrapLoja.classList.remove('d-none')
                lojaId.removeAttribute('disabled')
                if (nivelLojaSnapshotAntesFabrica) {
                    lojaId.value = nivelLojaSnapshotAntesFabrica.loja
                } else {
                    lojaId.value = lojaId.dataset.initialValue || ''
                }

                wrapPermAcoes?.classList.remove('d-none')

                const srcPerm = permSnapshotAntesFabrica || permStateInitial
                document.querySelectorAll('#permissionsContainer [data-modulo]').forEach(el => {
                    el.classList.remove('d-none')
                    el.querySelectorAll('.permissao-visualizar, .permissao-manipular').forEach(cb => {
                        cb.disabled = false
                        cb.checked = srcPerm.has(cb.id) ? srcPerm.get(cb.id) : false
                    })
                })
            }
        }

        if (selFabrica) {
            aplicarModoColaboradorFabrica(selFabrica.value === '1')
            selFabrica.addEventListener('change', () => {
                const ativo = selFabrica.value === '1'
                if (ativo) {
                    permSnapshotAntesFabrica = new Map()
                    document.querySelectorAll('#permissionsContainer [data-modulo] .permissao-checkbox').forEach(cb => {
                        permSnapshotAntesFabrica.set(cb.id, cb.checked)
                    })
                    nivelLojaSnapshotAntesFabrica = {
                        nivel: nivelAcesso.value,
                        loja: lojaId.value
                    }
                }
                aplicarModoColaboradorFabrica(ativo)
            })
        }

        const selectAllBtn = document.getElementById('selectAllPermissions')
        const deselectAllBtn = document.getElementById('deselectAllPermissions')
        selectAllBtn?.addEventListener('click', function () {
            document.querySelectorAll('#permissionsContainer .permissao-checkbox:not(:disabled)').forEach(cb => { cb.checked = true })
        })
        deselectAllBtn?.addEventListener('click', function () {
            document.querySelectorAll('#permissionsContainer .permissao-checkbox:not(:disabled)').forEach(cb => { cb.checked = false })
        })

        document.querySelectorAll('.permissao-manipular').forEach(cb => {
            cb.addEventListener('change', function () {
                if (this.checked) {
                    const vis = document.getElementById('visualizar_' + this.dataset.modulo)
                    if (vis) vis.checked = true
                }
            })
        })

        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (selFabrica && selFabrica.value === '1') {
                    nivelAcesso.removeAttribute('disabled')
                    lojaId.removeAttribute('disabled')
                    document.querySelectorAll('#permissionsContainer [data-modulo] .permissao-checkbox').forEach(cb => {
                        cb.disabled = false
                    })
                }
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                if (selFabrica && selFabrica.value === '1') {
                    nivelAcesso.setAttribute('disabled', 'disabled')
                    lojaId.setAttribute('disabled', 'disabled')
                    aplicarModoColaboradorFabrica(true)
                }
                form.classList.add('was-validated')
            }, false)
        })
    })
</script>