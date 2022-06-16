$(document).ready(function() {

            function limpa_formulário_cep() {
                // Limpa valores do formulário de cep.
                $("#rua").val("");
                $("#bairro").val("");
                $("#cidade").val("");
                $("#uf").val("");
            }

            $("#empresa_cep").blur(function() {

                var cep = $(this).val().replace(/\D/g, '');

                if (cep != "") {

                    var validacep = /^[0-9]{8}$/;

                    if(validacep.test(cep)) {

                        $("#empresa_rua").val("...");
                        $("#empresa_bairro").val("...");
                        $("#empresa_cidade").val("...");
                        $("#empresa_uf").val("...");

                        $("#empresa_numero").focus();

                        //Consulta o webservice
                        $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                            if (!("erro" in dados)) {

                                $("#empresa_rua").val(dados.logradouro);
                                $("#empresa_bairro").val(dados.bairro);
                                $("#empresa_cidade").val(dados.localidade);
                                $("#empresa_uf").val(dados.uf);
                            } //end if.
                            else {

                                limpa_formulário_cep();
                                alert("CEP não encontrado.");
                            }
                        });
                    } //end if.
                    else {
                        
                        limpa_formulário_cep();
                        alert("Formato de CEP inválido.");
                    }
                } //end if.
                else {
                    //cep sem valor, limpa formulário.
                    limpa_formulário_cep();
                }
            });
        });
