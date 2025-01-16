<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Painel Joalheria</h1>
</div>

<!-- Content Row (Primeira Linha de Indicadores) -->
<div class="row">

  <!-- Pronto Entrega -->
  <div class="col-xl-2 col-md-6 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
              Pronto Entrega
            </div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-gem fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Pronto Retirada -->
  <div class="col-xl-2 col-md-6 mb-4">
    <div class="card border-left-info shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
              Pronto Retirada
            </div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">1</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-tag fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Separado -->
  <div class="col-xl-2 col-md-6 mb-4">
    <div class="card border-left-success shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
              Separado
            </div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-truck-loading fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Em Produção -->
  <div class="col-xl-2 col-md-6 mb-4">
    <div class="card border-left-warning shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
              Em Produção
            </div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">1</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-layer-group fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Finalizado -->
  <div class="col-xl-2 col-md-6 mb-4">
    <div class="card border-left-secondary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
              Finalizado
            </div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Aguardando Separação -->
  <div class="col-xl-2 col-md-6 mb-4">
    <div class="card border-left-danger shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
              Aguard. Separação
            </div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">13</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
<!-- Fim da Primeira Linha de Indicadores -->

<!-- Content Row (Segunda Linha) -->
<div class="row">

  <!-- Gráfico de Evolução das Vendas (Area Chart) -->
  <div class="col-xl-8 col-lg-7">
    <div class="card shadow mb-4">
      <!-- Cabeçalho do Card -->
      <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">Evolução de Vendas</h6>
      </div>
      <!-- Corpo do Card -->
      <div class="card-body">
        <div class="chart-area">
          <!-- Substitua pelo seu Chart.js ou outro gráfico -->
          <canvas id="myAreaChart"></canvas>
        </div>
        <div class="mt-4 text-center small">
          <!-- Descrição / legenda adicional, se quiser -->
          <!-- Ex: Evolução de vendas nos últimos 7 dias -->
        </div>
      </div>
    </div>
  </div>

  <!-- Gráfico de Status de Pedidos (Pie Chart) -->
  <div class="col-xl-4 col-lg-5">
    <div class="card shadow mb-4">
      <!-- Cabeçalho do Card -->
      <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">Status de Pedidos</h6>
      </div>
      <!-- Corpo do Card -->
      <div class="card-body">
        <div class="chart-pie pt-4 pb-2">
          <!-- Substitua pelo seu Chart.js ou outro gráfico -->
          <canvas id="myPieChart"></canvas>
        </div>
        <div class="mt-4 text-center small">
          <span class="mr-2">
            <i class="fas fa-circle text-primary"></i> Em Separação
          </span>
          <span class="mr-2">
            <i class="fas fa-circle text-success"></i> Em Produção
          </span>
          <span class="mr-2">
            <i class="fas fa-circle text-info"></i> Finalizado
          </span>
          <span class="mr-2">
            <i class="fas fa-circle text-danger"></i> Atrasado
          </span>
        </div>
      </div>
    </div>
  </div>

</div>
<!-- Fim da Segunda Linha -->



<!-- Quarta Linha: Desempenho por Vendedor, Desempenho por Produto, Grupos Mais Vendidos, Financeiro -->
<div class="row">

  <!-- Desempenho por Vendedor -->
  <div class="col-xl-3 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">Desempenho por Vendedor</h6>
      </div>
      <div class="card-body">
        <!-- Exemplo de conteúdo -->
        <p class="mb-2 fw-bold text-gray-800">Adilson (3 vendas) - R$ 20.653,25</p>
        <div class="progress mb-3" style="height: 6px;">
          <div class="progress-bar bg-success" style="width:100%;"></div>
        </div>

        <p class="mb-2 fw-bold text-gray-800">Maria (2 vendas) - R$ 4.500,00</p>
        <div class="progress mb-2" style="height: 6px;">
          <div class="progress-bar bg-info" style="width:70%;"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Desempenho por Produto -->
  <div class="col-xl-3 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">Desempenho por Produto</h6>
      </div>
      <div class="card-body">
        <p class="mb-2">Pulseira Grume Ouro (2 vendas)</p>
        <div class="progress mb-2" style="height: 6px;">
          <div class="progress-bar bg-success" style="width:80%;"></div>
        </div>
        <p class="small text-end">R$ 17.795,00</p>

        <p class="mb-2">Jg Ouro Amarelo (1 venda)</p>
        <div class="progress mb-2" style="height: 6px;">
          <div class="progress-bar bg-info" style="width:40%;"></div>
        </div>
        <p class="small text-end">R$ 3.200,00</p>
      </div>
    </div>
  </div>

  <!-- Grupos Mais Vendidos -->
  <div class="col-xl-3 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">Grupos Mais Vendidos</h6>
      </div>
      <div class="card-body">
        <ul class="list-group list-group-flush small">
          <li class="list-group-item d-flex justify-content-between">
            <span>Pulseiras</span>
            <span>R$ 10.500,00</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span>Brincos</span>
            <span>R$ 7.800,00</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span>Correntes</span>
            <span>R$ 4.300,00</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span>Anéis</span>
            <span>R$ 3.000,00</span>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Financeiro -->
  <div class="col-xl-3 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">Financeiro</h6>
      </div>
      <div class="card-body">
        <p class="small text-muted">Posição em DD/MM/AAAA</p>
        <p class="mb-1"><strong>Recebimentos:</strong> R$ 0,00</p>
        <p class="mb-1"><strong>Recebido:</strong> R$ 0,00</p>
        <p class="mb-1"><strong>Pagamentos:</strong> R$ 0,00</p>
        <!-- Adicione mais linhas conforme sua necessidade -->
      </div>
    </div>
  </div>

</div>
<!-- Fim da Quarta Linha -->
