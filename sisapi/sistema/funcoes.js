function confirma(codbarras, nome) {
    swal({
        title: "Atenção!",
        text: "Tem certeza que deseja remover " + nome + " da lista de produtos?",
        icon: "warning",
        buttons: ["NÃO", "REMOVER"],
        dangerMode: true,
    })
    /* 
    Função atualizada 03/03 - Ajustar a apagar.php para 
    receber informações por POST e retornar a situação da remoção
    */
        .then((resposta) => {
            if (resposta) {
                $.post("../api/apagar.php", {
                    codBarras: codbarras
                }).done(function (data) {
                    // Redirecionar para a página do sistema:
                    if (data.status == 1) {
                        swal("Sucesso!", data.mensagem, 'success');
                        // Limpar a tabela:
                        apagarTabela();
                        // Atualizar com novas informações:
                        atualizarProdutos();
                    } else {
                        swal("Erro!", data.mensagem, 'warning');
                    }

                });
            } else {
                swal("Seu produto está a salvo!");
            }
        });
}

function cadastrarProduto() {
    $.post("../api/cadastraProduto.php", {
        codBarras: codBarras.value,
        nome: nome.value,
        preco: preco.value,
        qtdEstoque: qtdEstoque.value,
        categoria: categoria.value
    }).done(function (data) {
        // Redirecionar para a página do sistema:
        if (data.status == 1) {
            swal("Sucesso!", data.mensagem, 'success');
            // Limpar o formulário:
            $('#formCadastroProdutos').trigger("reset");
            // Esconder a modal:
            $('#modalCadastro').modal('hide');
            // Limpar a tabela:
            apagarTabela();
            // Atualizar com novas informações:
            atualizarProdutos()
        } else {
            swal("Erro!", data.mensagem, 'warning');
        }

    });
}

// Função para apagar a listagem de produtos:
function apagarTabela() {
    $("#corpoTabela").html("");
}

function abrirEditar(codbarras, nome, preco, estoque){
    // Abrir a modalEditar
    $('#modalEditar').modal('show');
    // Preencher os campos do modal com as informações do produto selecionado:
    codBarrasEdi.value = codbarras;
    nomeEdi.value = nome;
    precoEdi.value = preco;
    qtdEstoqueEdi.value = estoque;
}

function editarProduto(){
    $.post("../api/modificaProduto.php", {
        idProduto: codBarrasEdi.value,
        nome: nomeEdi.value,
        preco: precoEdi.value,
        estoque: qtdEstoqueEdi.value,
        idCategoria: categoriaEdi.value
    }).done(function (data) {
        // Redirecionar para a página do sistema:
       //console.log(data);
        if (data.status == 1) {
            swal("Sucesso!", data.mensagem, 'success');
            // Esconder a modal:
            $('#modalEditar').modal('hide');
            // Limpar a tabela:
            apagarTabela();
            // Atualizar com novas informações:
            atualizarProdutos();
        } else {
            swal("Erro!", data.mensagem, 'warning');
        }

    });
}

// Função para puxar a lista de produtos:
function atualizarProdutos() {
    $.getJSON("../api/listarProdutos.php").done(function (data) {
        if (data.status == 0) {
            // Caso o usuário não esteja logado:
            swal({
                title: "Atenção!",
                text: "Você precisa logar primeiro!",
                icon: "warning",
                //buttons: true
            })
                .then((resposta) => {
                    location.href = "../index.php";
                });
        } else {
            // Listar os produtos caso o usuário esteja logado:
            data.dados.forEach(function (produto) {
                $("#corpoTabela").append("<tr>");
                $("#corpoTabela").append("<td>" + produto.codbarras + "</td>");
                $("#corpoTabela").append("<td><img class='imagem' src='" + produto.foto + "' /></td>");
                $("#corpoTabela").append("<td>" + produto.nome + "</td>");
                $("#corpoTabela").append("<td>" + produto.preco + "</td>");
                $("#corpoTabela").append("<td>" + produto.estoque + "</td>");
                $("#corpoTabela").append("<td>" + produto.nomeCategoria + "</td>");
                $("#corpoTabela").append("<td><a href=\"#\" onclick=\"confirma(" + produto.codbarras + ",'" + produto.nome + "')\">APAGAR</a> | <a href=\"#\" onclick=\"abrirEditar('"+produto.codbarras+"','"+produto.nome+"','"+produto.preco+"','"+produto.estoque+"')\">EDITAR</a></td>");
                $("#corpoTabela").append("</tr>");
            });
        }
    });
}

// Carregamento de informações quando a página terminar de carregar:
$(document).ready(function () {
    // Puxar o nome do usuário pela API:
    $.getJSON("../api/dadosUsuario.php").done(function (data) {
        $("#nomeCompleto").text(data.dados.nomeCompleto);
    });
    // Listar produtos:
    atualizarProdutos();

    // Listar as categorias no form de cadastro:
    $.getJSON("../api/listarCategorias.php").done(function (data) {
        data.dados.forEach(function (categ) {
            $("#categoria").prepend("<option value=\"" + categ.id + "\">" + categ.nome + "</option>");
            $("#categoriaEdi").prepend("<option value=\"" + categ.id + "\">" + categ.nome + "</option>");
        });
    });
});