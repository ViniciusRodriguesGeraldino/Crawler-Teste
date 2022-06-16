  // Adicionamos o evento onclick ao botão com o ID "pesquisar"
  $("#cnpj").blur(function() {
    
    // Aqui recuperamos o cnpj preenchido do campo e usamos uma expressão regular para limpar da string tudo aquilo que for diferente de números
    var cnpj = $('#cnpj').val().replace(/[^0-9]/g, '');
    
    // Fazemos uma verificação simples do cnpj confirmando se ele tem 14 caracteres
    if(cnpj.length == 14) {

      //Preenche os campos com "..." enquanto consulta webservice.
      $("#razao").val("...");
      $("#fantasia").val("...");
      $("#situacao").val("...");
      $('#abertura').val("...");
    
      $.ajax({
        url:'https://www.receitaws.com.br/v1/cnpj/' + cnpj,
        method:'GET',
        dataType: 'jsonp',
        complete: function(xhr){

          response = xhr.responseJSON;
          
          if(response.status == 'OK') {

            $('#razao').val(response.nome);
            $('#fantasia').val(response.fantasia);
            $('#situacao').val(response.situacao);
            $('#abertura').val(response.abertura);
            $('#cep').val(response.cep.replace(/\./, ''));
            $('#rua').val(response.logradouro);
            $('#numero').val(response.numero);
            $('#complemento').val(response.complemento);
            $('#bairro').val(response.bairro);
            $('#cidade').val(response.municipio);
            $('#uf').val(response.uf);

          } else {
            alert(response.message);
          }
        }
      });
    
    // Tratativa para caso o CNPJ não tenha 14 caracteres
    } else {
      alert('CNPJ inválido');
    }
  });