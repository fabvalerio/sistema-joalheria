<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Fulano de Tal</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/{$link[2]}" ?>" class="needs-validation" novalidate>

            <div class="row g-3">

                <div class="col-lg-4">
                    <label for="" class="form-label">Data pedido</label>
                    <input type="date" name="pedido" id="pedido" class="form-control">
                </div>

                <div class="col-lg-4">
                    <label for="" class="form-label">Data Entrega</label>
                    <input type="date" name="entrega" id="entrega" class="form-control">
                </div>

                <div class="col-lg-4">
                    <label for="" class="form-label">Status de Pagamento</label>
                    <select name="pagamento" id="pagamento" class="form-select">
                        <option value="">Pago</option>
                        <option value="">Aberto</option>
                        <option value="">Parcial</option>
                    </select>
                </div>

<div class="col-lg-12">
    <label for="" class="form-label">Produto</label>
    <select name="produto" id="" class="form-select">
        <option value="">Corrente</option>
        <option value="">Anel</option>
        <option value="">Bricno</option>
    </select>
</div>

                <div class="col-lg-12">
                    <label for="" class="form-label">Material</label>
                    <div>
                        <input type="checkbox" class="ms-2 btn-check" id="btn-check-outlined" autocomplete="off">
                        <label class="btn btn-outline-success" for="btn-check-outlined">Ouro</label>

                        <input type="checkbox" class="ms-2 btn-check" id="btn-check-2-outlined" autocomplete="off">
                        <label class="btn btn-outline-success" for="btn-check-2-outlined">Prata</label>

                        <input type="checkbox" class="ms-2 btn-check" id="btn-check-3-outlined" autocomplete="off">
                        <label class="btn btn-outline-success" for="btn-check-3-outlined">Ouro Branco</label>

                        <input type="checkbox" class="ms-2 btn-check" id="btn-check-outlined" autocomplete="off">
                        <label class="btn btn-outline-success" for="btn-check-outlined">Ouro Rose</label>
                    </div>

                </div>

                <div class="col-lg-12">
                    <label for="" class="form-label">Cotação do Material</label>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="badge text-bg-success rounded-pill">Ouro</span>
                            <div class="d-flex">
                                <input type="text" class="form-control me-2" placeholder="Cotação">
                                <input type="text" class="form-control me-2" placeholder="GR">
                                <input type="text" class="form-control me-2" placeholder="Margem (%)">
                                <input type="text" class="form-control me-2" placeholder="Atividades">
                                <span class="input-group-text" id="basic-addon1">R$</span>
                                <input type="text" class="form-control" placeholder="Total" aria-label="Total" aria-describedby="basic-addon1">
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="badge text-bg-success rounded-pill">Ouro Branco</span>
                            <div class="d-flex">
                                <input type="text" class="form-control me-2" placeholder="Cotação">
                                <input type="text" class="form-control me-2" placeholder="GR">
                                <input type="text" class="form-control me-2" placeholder="Margem (%)">
                                <input type="text" class="form-control me-2" placeholder="Atividades">
                                <span class="input-group-text" id="basic-addon1">R$</span>
                                <input type="text" class="form-control" placeholder="Total" aria-label="Total" aria-describedby="basic-addon1">
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-12">
                    <label for="" class="form-label">Pedra</label>
                    <div>
                        <input type="checkbox" class="ms-2 btn-check" id="btn-check-outlined-1" autocomplete="off">
                        <label class="btn btn-outline-primary" for="btn-check-outlined-1">Diamante</label>

                        <input type="checkbox" class="ms-2 btn-check" id="btn-check-outlined-2" autocomplete="off">
                        <label class="btn btn-outline-primary" for="btn-check-outlined-2">Rubi</label>

                        <input type="checkbox" class="ms-2 btn-check" id="btn-check-outlined-3" autocomplete="off">
                        <label class="btn btn-outline-primary" for="btn-check-outlined-3">Esmeralda</label>

                        <input type="checkbox" class="ms-2 btn-check" id="btn-check-outlined-4" autocomplete="off">
                        <label class="btn btn-outline-primary" for="btn-check-outlined-4">Safira</label>
                    </div>

                </div>

                <div class="col-lg-12">
                    <label for="" class="form-label">Cotação do Pedra</label>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="badge text-bg-primary rounded-pill">Rubi</span>
                            <div class="d-flex">
                                <input type="text" class="form-control me-2" placeholder="Cotação">
                                <input type="text" class="form-control me-2" placeholder="QL">
                                <input type="text" class="form-control me-2" placeholder="Margem (%)">
                                <input type="text" class="form-control me-2" placeholder="Atividades">
                                <span class="input-group-text" id="basic-addon1">R$</span>
                                <input type="text" class="form-control" placeholder="Total" aria-label="Total" aria-describedby="basic-addon1">
                                
                            </div>
                        </li>
                    </ul>
                </div>

        </form>
    </div>
</div>