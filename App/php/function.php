<?php

function moeda($valor)
{
  return number_format($valor, 2, ',', '.');
}
function _float($valor)
{
  $valor = str_replace('.', '', $valor);
  $valor = str_replace(',', '.', $valor);
  return $valor;
}
function _int($valor)
{
  $valor = explode('.', $valor);
  return $valor[0];
}

/*NOME DE ARQUIVO--------------------------------------------------------------------------------------*/
function nome_arquivo($nome){
  return md5($nome) . date('YmdHisu') . "." . @end(@explode('.', $nome));
}

/*Status Post*/
function StatusPost($var)
{
  if ($var == 'on'):
    $res = '1';
  else:
    $res = '0';
  endif;

  return $res;
}

/*Status Post Retorno*/
function status($var)
{

  switch ($var) {
    case '0':
      $res = '<span class="text-info"><i class="bi bi-arrow-clockwise"></i> Inativo</span>';
      break;
    case '1':
      $res = '<span class="text-success"><i class="bi bi-check2-circle"></i> Ativo</span>';
      break;
    default:
      $res = '<span class="text-danger"><i class="bi bi-x-lg"></i> Inativo</span>';
  }

  return $res;
}


function dia($var)
{
  return date('d-m-Y', strtotime($var));
}

// Função para converter perguntas de alternativa para dissertativa
function converterUltimasPerguntasParaDissertativas($json, $quantidade)
{
  // Decodifica o JSON para um array PHP
  $data = json_decode($json, true);

  if ($quantidade >= 1) {
    // Calcula os índices das últimas perguntas que precisam ser convertidas
    $totalPerguntas = count($data['questions']);

    $indices = range($totalPerguntas - $quantidade, $totalPerguntas - 1);

    // Itera sobre os índices calculados para modificar as perguntas
    foreach ($indices as $indice) {
      if (isset($data['questions'][$indice])) {
        unset($data['questions'][$indice]['alternativas']); // Remove as alternativas
        unset($data['questions'][$indice]['correta']); // Remove a resposta correta
        $data['questions'][$indice]['tipo'] = 'dissertativa'; // Adiciona o tipo dissertativa
      }
    }
  }

  // Codifica novamente o array em JSON
  return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}


function fixCharacters($string)
{
  // Lista de substituições para caracteres especiais e acentuados
  $replacements = [
    'u00e1' => 'á',
    'u00e0' => 'à',
    'u00e2' => 'â',
    'u00e3' => 'ã',
    'u00e4' => 'ä',
    'u00e9' => 'é',
    'u00e8' => 'è',
    'u00ea' => 'ê',
    'u00eb' => 'ë',
    'u00ed' => 'í',
    'u00ec' => 'ì',
    'u00ee' => 'î',
    'u00ef' => 'ï',
    'u00f3' => 'ó',
    'u00f2' => 'ò',
    'u00f4' => 'ô',
    'u00f5' => 'õ',
    'u00f6' => 'ö',
    'u00fa' => 'ú',
    'u00f9' => 'ù',
    'u00fb' => 'û',
    'u00fc' => 'ü',
    'u00e7' => 'ç',
    'u00f1' => 'ñ',
    'u00df' => 'ß',

    'u00c1' => 'Á',
    'u00c0' => 'À',
    'u00c2' => 'Â',
    'u00c3' => 'Ã',
    'u00c4' => 'Ä',
    'u00c9' => 'É',
    'u00c8' => 'È',
    'u00ca' => 'Ê',
    'u00cb' => 'Ë',
    'u00cd' => 'Í',
    'u00cc' => 'Ì',
    'u00ce' => 'Î',
    'u00cf' => 'Ï',
    'u00d3' => 'Ó',
    'u00d2' => 'Ò',
    'u00d4' => 'Ô',
    'u00d5' => 'Õ',
    'u00d6' => 'Ö',
    'u00da' => 'Ú',
    'u00d9' => 'Ù',
    'u00db' => 'Û',
    'u00dc' => 'Ü',
    'u00c7' => 'Ç',
    'u00d1' => 'Ñ',

    // Caracteres especiais
    'u00a1' => '¡',
    'u00bf' => '¿',
    'u00aa' => 'ª',
    'u00ba' => 'º',
    'u00b0' => '°',
    'u00b1' => '±',
    'u00b2' => '²',
    'u00b3' => '³',
    'u00bc' => '¼',
    'u00bd' => '½',
    'u00be' => '¾',

    'u20ac' => '€',
    'u00a3' => '£',
    'u00a5' => '¥',
    'u00a2' => '¢',
    'u00ae' => '®',
    'u2122' => '™',
    'u00a9' => '©',
    'u00b5' => 'µ',

    // Entidades HTML
    '&lt;' => '<',
    '&gt;' => '>',
    '&amp;' => '&',
    '&quot;' => '"',
    '&#39;' => "'",
    '&laquo;' => '«',
    '&raquo;' => '»',
    '&cent;' => '¢',
    '&pound;' => '£',
    '&yen;' => '¥',
    '&euro;' => '€',
    '&copy;' => '©',
    '&reg;' => '®',
    '&trade;' => '™',
  ];

  // Substitui cada ocorrência
  foreach ($replacements as $unicode => $char) {
    $string = str_replace($unicode, $char, $string);
  }

  return $string;
}


function calcTempo($dataHoraInicio, $dataHoraFim)
{
  // Verifica se ambas as datas foram fornecidas
  if (!empty($dataHoraInicio) && !empty($dataHoraFim)) {
    try {
      // Converte para objetos DateTime
      $inicio = new DateTime($dataHoraInicio);
      $fim = new DateTime($dataHoraFim);

      // Calcula a diferença
      $intervalo = $inicio->diff($fim);

      // Retorna o intervalo formatado como HH:MM:SS
      return $intervalo->format('%H:%I:%S');
    } catch (Exception $e) {
      // Em caso de erro, retorna uma string padrão
      return "00:00:00";
    }
  } else {
    // Retorna tempo zero se as datas forem inválidas
    return "00:00:00";
  }
}


function formatarDataHora($dataHora)
{
  // Verifica se a variável não está vazia e é uma string válida
  if (!empty($dataHora) && is_string($dataHora)) {
    try {
      // Converte a string para um objeto DateTime
      $dateTime = new DateTime($dataHora);

      // Formata a data e hora no formato desejado
      return $dateTime->format('d-m-Y') . ' às ' .
        $dateTime->format('H\h i\m\i\n s\s\e\g');
    } catch (Exception $e) {
      // Retorna uma mensagem de erro se a data for inválida
      return "";
    }
  }
  // Retorna vazio caso a variável seja inválida
  return "";
}

/* Função de Seleção */
function Select($tabela, $coluna, $valor, $where, $selectValue = NULL, $id = NULL)
{

  if (!empty($where)) $_where = " WHERE " . $where;
  $visSQL = "SELECT $coluna, $valor  FROM " . $tabela . $_where . " ORDER BY " . $coluna . " ASC";
  $vis = new db();
  $vis->query($visSQL);

  $resultado = '';

  if (!empty($vis->row())) {

    $resultado .= '<select name="' . $id . '" id="' . $id . '" class="form-select" requered>';
    $resultado .= '<option selected disabled value="">Selecione</option>' . "\n";

    foreach ($vis->row() as $row) {

      if ($row[$valor] == $selectValue and !empty($selectValue)) {
        $valorSelec = 'selected="selected"';
      } else {
        $valorSelec = '';
      }

      $resultado .= '<option ' . $valorSelec . ' value="' . ($row[$valor]) . '">' . ($row[$coluna]) . '</option>' . "\n";
      $valorSelec = '';
    }
    $resultado .= '</select>';
  }

  return $resultado;
}


/*Tipo de empregador*/
function empregador($var)
{

  switch ($var) {
    case '1':
      $res = '<span class="text-success">CLT</span>';
      break;
    case '2':
      $res = '<span class="text-info">PJ Médico</span>';
      break;
    case '3':
      $res = '<span class="text-danger">PJ</span>';
      break;
  }

  return $res;
}


/*Tipo de estadio civil*/
function civil($var)
{

  switch ($var) {
    case '1':
      $res = 'Solteiro(a)';
      break;
    case '2':
      $res = 'Casado(a)';
      break;
    case '3':
      $res = 'Divorciado(a)';
      break;
    case '4':
      $res = 'Viúvo(a)';
      break;
  }

  return $res;
}


/*Tipo de sexo*/
function sexo($var)
{
  switch ($var) {
    case '1':
      $res = 'Masculino';
      break;
    case '2':
      $res = 'Feminino';
      break;
    default:
      $res = 'Não especificado';
      break;
  }

  return $res;
}

/*Tipo de pix*/
function pix($var)
{
  switch ($var) {
    case '1':
      $res = 'Telefone';
      break;
    case '2':
      $res = 'CPF/CNPJ';
      break;
    case '3':
      $res = 'Chave';
      break;
    case '4':
        $res = 'E-mail';
        break;
      default:
          $res = 'Não especificado';
          break;
  }

  return $res;
}

/*Tipo de banco*/
function tipoBanco($var)
{
  switch ($var) {
    case '1':
      $res = 'Conta Corrente';
      break;
    case '2':
      $res = 'CPF/CNPJ';
      break;
    case '3':
      $res = 'Conta Poupança';
      break;
    case '4':
        $res = 'Conta Pagamento';
        break;
    case '5':
        $res = 'Conta Salário';
        break;
    default:
      $res = 'Não especificado';
      break;
  }

  return $res;
}

/*Tipo de entidade*/
function tipoEntidade($var)
{
  switch ($var) {
    case '1':
      $res = 'FIDIC';
      break;
    case '2':
      $res = 'BANCOS';
      break;
    case '3':
      $res = 'SECURITIZADORAS';
      break;
    case '4':
      $res = 'PESSOA FISICA';
      break;
    case '5':
      $res = 'OUTROS';
      break;
    default:
      $res = 'Não especificado';
      break;
  }

  return $res;
}