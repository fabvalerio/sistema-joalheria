<?php

use App\Models\Usuarios\Controller;
// Obter os cargos
// Instanciar o Controller de Usuários
$controller = new Controller();
$cargos = $controller->cargos();
$lojas = $controller->listarLojas();
$diretorios = $controller->listarDiretorios();
$permissoesUsuario = [];
foreach ($diretorios as $dir) {
    $permissoesUsuario[$dir] = ["visualizar" => false, "manipular" => false];
}

// Verificar se o formulário foi enviado
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
        'senha' => password_hash($_POST['senha'], PASSWORD_DEFAULT),
        'nivel_acesso' => $ehColaboradorFabrica ? 'Operador' : $_POST['nivel_acesso'],
        'bairro' => $_POST['bairro'],
        'numero' => $_POST['numero'],
        'status' => $_POST['status'],
        'loja_id' => $ehColaboradorFabrica ? null : (!empty($_POST['loja_id']) ? (int) $_POST['loja_id'] : null),
        'permissoes' => null
    ];
    if ($ehColaboradorFabrica) {
        $permissoesUsuario = [];
        foreach ($diretorios as $dir) {
            $permissoesUsuario[$dir] = $dir === 'Fabrica'
                ? ['visualizar' => true, 'manipular' => true]
                : ['visualizar' => false, 'manipular' => false];
        }
        $dados['permissoes'] = json_encode($permissoesUsuario);
    } elseif (isset($_POST['permissoes'])) {
        // Verifica se há permissões enviadas - iterar sobre TODOS os diretórios
        // (checkboxes desmarcados não são enviados no POST)
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
    // Debug para verificar o conteúdo do array
    // echo '<pre>';
    // echo htmlspecialchars(json_encode($permissoesUsuario, JSON_PRETTY_PRINT));
    // echo '</pre>';
    // exit;

    $controller = new Controller();
    $return = $controller->cadastro($dados);

    if ($return) {
        echo notify('success', "Cadastrado com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao cadastrar.");
    }
}

?>

<div class="card">

    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cadastro de Usuário</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>

            <div class="row g-3">
                <div class="col-lg-4">
                    <label for="colaborador_fabrica" class="form-label">Colaborador da Fábrica</label>
                    <select class="form-select" name="colaborador_fabrica" id="colaborador_fabrica" required>
                        <option value="0" selected>Não</option>
                        <option value="1">Sim</option>
                    </select>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control" name="nome_completo" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">E-mail</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Cargo</label>
                    <!-- select com lista de cargos -->
                    <select class="form-select" name="cargo" required>
                        <option value="">Selecione</option>
                        <?php foreach ($cargos as $cargo) : ?>
                            <option value="<?= $cargo['id'] ?>"><?= $cargo['cargo'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Telefone</label>
                    <input type="text" class="form-control" name="telefone" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">RG</label>
                    <input type="text" class="form-control" name="rg" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Emissão do RG</label>
                    <input type="date" class="form-control" name="emissao_rg" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">CPF</label>
                    <input type="text" class="form-control" id="cpf" name="cpf" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Data de Nascimento</label>
                    <input type="date" class="form-control" name="data_nascimento" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">CEP</label>
                    <input type="text" class="form-control" id="cep" name="cep" required>
                </div>
                <div class="col-lg-6">
                    <label for="" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="endereco" name="endereco" required>
                </div>
                <div class="col-lg-2">
                    <label for="" class="form-label">Numero</label>
                    <input type="text" class="form-control" id="numero" name="numero" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Bairro</label>
                    <input type="text" class="form-control" id="bairro" name="bairro" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Estado</label>
                    <input type="text" class="form-control" id="estado" name="estado" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Login</label>
                    <input type="text" class="form-control" name="login" required>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Senha</label>
                    <input type="password" class="form-control" name="senha" required>
                </div>
                <div class="col-lg-4" id="wrap-nivel-acesso">
                    <label for="nivel_acesso" class="form-label">Nível de Acesso</label>
                    <select class="form-select" name="nivel_acesso" id="nivel_acesso" required>
                        <option value="">Selecione o Nível de Acesso</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Operador">Operador</option>
                        <option value="Consulta">Consulta</option>
                    </select>
                </div>
                <div class="col-lg-4">
                    <label for="" class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                        <option select disabled>Selecione o Status</option>
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
                <div class="col-lg-4" id="wrap-loja">
                    <label for="loja_id" class="form-label">Loja</label>
                    <select class="form-select" name="loja_id" id="loja_id">
                        <option value="">Selecione a Loja</option>
                        <?php foreach ($lojas as $loja): ?>
                            <option value="<?= $loja['id'] ?>"><?= htmlspecialchars($loja['nome']) ?> (<?= $loja['tipo'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <hr>
                <h2>Permissões</h2>
                <p class="text-muted small">Cada permissão controla o módulo correspondente no menu. Visualizar: exibe o menu e permite listar/ver. Manipular: permite cadastrar, editar e excluir.</p>
                <?php
                foreach ($diretorios as $dir) {
                    echo "<div class='col-lg-4 mb-3' data-modulo='" . htmlspecialchars($dir) . "'>";
                    echo "<label class='form-label fw-bold d-block'>" . htmlspecialchars($dir) . " <small class='text-muted'>(menu)</small></label>";

                    echo "<div class='form-check form-check-inline'>";
                    echo "<input class='form-check-input permissao-visualizar' type='checkbox' name='permissoes[$dir][visualizar]' value='1' id='visualizar_$dir'>";
                    echo "<label class='form-check-label' for='visualizar_$dir'>Visualizar</label>";
                    echo "</div>";

                    echo "<div class='form-check form-check-inline'>";
                    echo "<input class='form-check-input permissao-manipular' type='checkbox' name='permissoes[$dir][manipular]' value='1' id='manipular_$dir' data-modulo='$dir'>";
                    echo "<label class='form-check-label' for='manipular_$dir'>Manipular</label>";
                    echo "</div>";

                    echo "</div>";
                }
                ?>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar</button>
                </div>
            </div>

        </form>
    </div>

</div>

<script>
    (() => {
        'use strict'
        const MODULO_FABRICA = 'Fabrica'
        const selFabrica = document.getElementById('colaborador_fabrica')
        const wrapNivel = document.getElementById('wrap-nivel-acesso')
        const wrapLoja = document.getElementById('wrap-loja')
        const nivelAcesso = document.getElementById('nivel_acesso')
        const lojaId = document.getElementById('loja_id')

        function aplicarModoColaboradorFabrica(ativo) {
            if (ativo) {
                wrapNivel.classList.add('d-none')
                nivelAcesso.value = 'Operador'
                nivelAcesso.removeAttribute('required')
                nivelAcesso.setAttribute('disabled', 'disabled')

                wrapLoja.classList.add('d-none')
                lojaId.value = ''
                lojaId.setAttribute('disabled', 'disabled')

                document.querySelectorAll('[data-modulo]').forEach(el => {
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

                wrapLoja.classList.remove('d-none')
                lojaId.removeAttribute('disabled')

                document.querySelectorAll('[data-modulo]').forEach(el => {
                    el.classList.remove('d-none')
                    el.querySelectorAll('.permissao-visualizar, .permissao-manipular').forEach(cb => {
                        cb.disabled = false
                    })
                })
            }
        }

        if (selFabrica) {
            aplicarModoColaboradorFabrica(selFabrica.value === '1')
            selFabrica.addEventListener('change', () => {
                aplicarModoColaboradorFabrica(selFabrica.value === '1')
            })
        }

        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (selFabrica && selFabrica.value === '1') {
                    nivelAcesso.removeAttribute('disabled')
                    lojaId.removeAttribute('disabled')
                }
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                if (selFabrica && selFabrica.value === '1') {
                    nivelAcesso.setAttribute('disabled', 'disabled')
                    lojaId.setAttribute('disabled', 'disabled')
                }
                form.classList.add('was-validated')
            }, false)
        })
        // Ao marcar Manipular, marcar também Visualizar (obrigatório para uso)
        document.querySelectorAll('.permissao-manipular').forEach(cb => {
            cb.addEventListener('change', function() {
                if (this.checked) {
                    const mod = this.dataset.modulo;
                    const vis = document.getElementById('visualizar_' + mod);
                    if (vis) vis.checked = true;
                }
            })
        })
    })()
</script>