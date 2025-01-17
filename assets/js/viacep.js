// Aplicar máscara no campo CEP
$(document).ready(function () {
  $('#cep').mask('00000-000');
});

// Função para limpar o formulário de endereço
function limpa_formulário_cep() {
  $("#endereco").val("");
  $("#bairro").val("");
  $("#cidade").val("");
  $("#estado").val("");
}

// Quando o campo CEP perde o foco
$("#cep").blur(function () {
  var cep = $(this).val().replace(/\D/g, ''); // Remove caracteres não numéricos

  if (cep !== "") {
      var validacep = /^[0-9]{8}$/; // Validação do formato do CEP

      if (validacep.test(cep)) {
          // Preenche os campos com "..." enquanto consulta webservice
          $("#endereco").val("...");
          $("#bairro").val("...");
          $("#cidade").val("...");
          $("#estado").val("...");

          // Consulta ao ViaCEP
          $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {
              if (!("erro" in dados)) {
                  // Preenche os campos com os valores da consulta
                  $("#endereco").val(dados.logradouro);
                  $("#bairro").val(dados.bairro);
                  $("#cidade").val(dados.localidade);
                  $("#estado").val(dados.uf);
                  $("#numero").focus();
              } else {
                  // CEP pesquisado não foi encontrado
                  limpa_formulário_cep();
                  alert("CEP não encontrado. Verifique e tente novamente.");
              }
          }).fail(function () {
              limpa_formulário_cep();
              alert("Erro ao consultar o CEP. Tente novamente mais tarde.");
          });
      } else {
          // CEP com formato inválido
          limpa_formulário_cep();
          alert("Formato de CEP inválido. Insira um CEP válido.");
      }
  } else {
      // CEP sem valor
      limpa_formulário_cep();
  }
});
