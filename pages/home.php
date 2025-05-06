<?php

use App\Models\Home\Controller;

$controller = new Controller();
$financeiro = $controller->financeiro();
$produtosMaisVendidos = $controller->produtosMaisVendidos();
$desempenhoProdutos = $controller->desempenhoProdutos();
$desempenhoVendedores = $controller->desempenhoVendedores();
$vendasPorMes = $controller->vendasPorMes();
$statusFabrica = $controller->statusFabrica();
$kpis = $controller->kpisFabrica();

?>

<!-- Chart.js -->
<script src="vendor/chart.js/Chart.min.js"></script>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Painel Joalheria</h1>
</div>

<!-- Content Row (Primeira Linha de Indicadores) -->
<div class="row">
  <?php
  $statusIcons = [
    'Pronto Entrega' => ['primary', 'gem'],
    'Pronto Retirada' => ['info', 'tag'], 
    'Separado' => ['success', 'truck-loading'],
    'Em Producao' => ['warning', 'layer-group'],
    'Finalizado' => ['secondary', 'check-circle'],
    'Aguardando Separacao' => ['danger', 'hourglass-half']
  ];

  foreach($statusIcons as $status => $config) {
    $total = 0;
    foreach($kpis as $kpi) {
      if($kpi['status'] == $status) {
        $total = $kpi['total'];
        break;
      }
    }
    ?>
    <div class="col-xl-2 col-md-6 mb-4">
      <div class="card border-left-<?php echo $config[0] ?> shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-<?php echo $config[0] ?> text-uppercase mb-1">
                <?php echo $status ?>
              </div>
              <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total ?></div>
            </div>
            <div class="col-auto">
              <i class="fas fa-<?php echo $config[1] ?> fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php
  }
  ?>
</div>
<!-- Fim da Primeira Linha de Indicadores -->

<!-- Content Row (Segunda Linha) -->
<div class="row">

  <!-- Gráfico de Evolução das Vendas (Area Chart) -->
  <div class="col-xl-8 col-lg-7 mb-3">
    <div class="card shadow mb-4 h-100">
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
  <div class="col-xl-4 col-lg-5 mb-3">
    <div class="card shadow mb-4 h-100">
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
            <i class="fas fa-circle" style="color: #4e73df"></i> Aguardando Separacao
          </span>
          <span class="mr-2">
            <i class="fas fa-circle" style="color: #1cc88a"></i> Em Producao
          </span>
          <span class="mr-2">
            <i class="fas fa-circle" style="color: #36b9cc"></i> Finalizado
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
    <div class="card shadow mb-4 h-100">
      <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">Desempenho por Vendedor</h6>
      </div>
      <div class="card-body">
        <?php foreach ($desempenhoVendedores as $vendedor): ?>
          <p class="mb-2 fw-bold text-gray-800">
            <?= $vendedor['nome_completo'] ?> (<?= $vendedor['total_vendas'] ?> vendas) - R$ <?= number_format($vendedor['valor_total'], 2, ',', '.') ?>
          </p>
          <?php
          $maxValue = max(array_column($desempenhoVendedores, 'valor_total'));
          if ($maxValue > 0) {
            $minValue = $maxValue * 0.01; // Define o mínimo como 1% do máximo
            $percentual = ($vendedor['valor_total'] - $minValue) / ($maxValue - $minValue) * 100;
            $percentual = max(1, min(100, $percentual)); // Garante que fique entre 1% e 100%
          } else {
            $percentual = 0;
          }
          ?>
          <div class="progress mb-3" style="height: 6px;">
            <div class="progress-bar bg-success" style="width:<?= $percentual ?>%;"></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Desempenho por Produto -->
  <div class="col-xl-3 col-lg-6">
    <div class="card shadow mb-4 h-100">
      <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">Desempenho por Produto</h6>
      </div>
      <div class="card-body">
        <?php foreach ($desempenhoProdutos as $produto): ?>
          <p class="mb-2" style="font-size: 11px;"><?= $produto['descricao_produto'] ?> (<?= $produto['total_vendas'] ?> vendas)</p>
          <?php
          $maxValue = max(array_column($desempenhoProdutos, 'valor_total'));
          $minValue = $maxValue * 0.01; // Define o mínimo como 10% do máximo
          $percentual = ($produto['valor_total'] - $minValue) / ($maxValue - $minValue) * 100;
          $percentual = max(0, min(100, $percentual)); // Garante que fique entre 0 e 100%
          ?>
          <div class="progress mb-2" style="height: 6px;">
            <div class="progress-bar bg-success" style="width:<?= $percentual ?>%;"></div>
          </div>
          <p class="small text-end">R$ <?= number_format($produto['valor_total'], 2, ',', '.') ?></p>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Grupos Mais Vendidos -->
  <div class="col-xl-3 col-lg-6">
    <div class="card shadow mb-4 h-100">
      <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">Grupos Mais Vendidos</h6>
      </div>
      <div class="card-body">
        <ul class="list-group list-group-flush small">
          <?php foreach ($produtosMaisVendidos as $produto): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span style="font-size: 11px;"><?= $produto['descricao_produto'] ?? 'Não Informa' ?></span>
              <span class="text-muted text-center"><?= $produto['total'] ?> <br>venda(s)</span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>

  <!-- Financeiro -->
  <div class="col-xl-3 col-lg-6">
    <div class="card shadow mb-4 h-100">
      <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">Financeiro</h6>
      </div>
      <div class="card-body">
        <spam class="small text-muted badge bg-info my-3">Posição em <?php echo date('d/m/Y'); ?></spam>
        <hr>
        <p class="mb-1"><strong>Á Receber:</strong> R$ <?php echo number_format($financeiro[0]['recebimentos'] ?? '0', 2, ',', '.'); ?></p>
        <hr>
        <p class="mb-1"><strong>Recebido:</strong> R$ <?php echo number_format($financeiro[0]['recebido'] ?? '0', 2, ',', '.'); ?></p>
        <hr>
        <p class="mb-1"><strong>Pagamentos:</strong> R$ <?php echo number_format($financeiro[0]['pagamentos'] ?? '0', 2, ',', '.'); ?></p>
        <!-- Adicione mais linhas conforme sua necessidade -->
      </div>
    </div>
  </div>

</div>
<!-- Fim da Quarta Linha -->

<script>
  // Set new default font family and font color to mimic Bootstrap's default styling
  Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
  Chart.defaults.global.defaultFontColor = '#858796';

  function number_format(number, decimals, dec_point, thousands_sep) {
    // *     example: number_format(1234.56, 2, ',', ' ');
    // *     return: '1 234,56'
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
      prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
      sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
      dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
      s = '',
      toFixedFix = function(n, prec) {
        var k = Math.pow(10, prec);
        return '' + Math.round(n * k) / k;
      };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
      s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
      s[1] = s[1] || '';
      s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
  }

  // Area Chart Example
  var ctx = document.getElementById("myAreaChart");
  var myLineChart = new Chart(ctx, {
    type: 'line', 
    data: {
      labels: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
      datasets: [{
        label: "Vendas",
        lineTension: 0.3,
        backgroundColor: "rgba(78, 115, 223, 0.05)",
        borderColor: "rgba(78, 115, 223, 1)",
        pointRadius: 3,
        pointBackgroundColor: "rgba(78, 115, 223, 1)", 
        pointBorderColor: "rgba(78, 115, 223, 1)",
        pointHoverRadius: 3,
        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
        pointHitRadius: 10,
        pointBorderWidth: 2,
        data: <?php echo json_encode(array_values($vendasPorMes)); ?>,
      }],
    },
    options: {
      maintainAspectRatio: false,
      layout: {
        padding: {
          left: 10,
          right: 25,
          top: 25,
          bottom: 0
        }
      },
      scales: {
        xAxes: [{
          time: {
            unit: 'date'
          },
          gridLines: {
            display: false,
            drawBorder: false
          },
          ticks: {
            maxTicksLimit: 7
          }
        }],
        yAxes: [{
          ticks: {
            maxTicksLimit: 5,
            padding: 10,
            // Include a dollar sign in the ticks
            callback: function(value, index, values) {
              return 'R$' + number_format(value);
            }
          },
          gridLines: {
            color: "rgb(234, 236, 244)",
            zeroLineColor: "rgb(234, 236, 244)",
            drawBorder: false,
            borderDash: [2],
            zeroLineBorderDash: [2]
          }
        }],
      },
      legend: {
        display: false
      },
      tooltips: {
        backgroundColor: "rgb(255,255,255)",
        bodyFontColor: "#858796",
        titleMarginBottom: 10,
        titleFontColor: '#6e707e',
        titleFontSize: 14,
        borderColor: '#dddfeb',
        borderWidth: 1,
        xPadding: 15,
        yPadding: 15,
        displayColors: false,
        intersect: false,
        mode: 'index',
        caretPadding: 10,
        callbacks: {
          label: function(tooltipItem, chart) {
            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
            return datasetLabel + ': R$' + number_format(tooltipItem.yLabel);
          }
        }
      }
    }
  });
</script>


<script>
  // Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Pie Chart Example
var ctx = document.getElementById("myPieChart");
var myPieChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ["Aguardando Separacao", "Em Producao", "Finalizado"],
    datasets: [{
      data: <?php echo json_encode($statusFabrica); ?>,
      backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
      hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
      hoverBorderColor: "rgba(234, 236, 244, 1)",
    }],
  },
  options: {
    maintainAspectRatio: false,
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
    },
    legend: {
      display: false
    },
    cutoutPercentage: 80,
  },
});

</script>