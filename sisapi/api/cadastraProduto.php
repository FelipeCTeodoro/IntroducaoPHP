<?php


// Iniciar utilização de sessão:

error_reporting(E_ALL ^ E_WARNING);
// Importar o arquivo banco.php:
include('db/banco.php');
// Inicar a sessão:
session_start();

// Array de Status:
$status = ["status" => 0, "mensagem" => "0", "dados" => 0];

// Verificar se o usuario está logado:
if (!isset($_SESSION['infosusuario'])) {
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    $status["mensagem"] = "Acesso permitido apenas para usuários autenticados.";
    echo json_encode($status);
} else {

    // Verificar se nome e/ou código de barras não está vazios:
    if ($_POST['codBarras'] != "" && $_POST['nome'] != "" && strlen($_POST['codBarras']) == 5) {
        $codbarras = $_POST['codBarras'];
        $nome = $_POST['nome'];
    } else {
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        $status["mensagem"] = "Códico de Barras ou Nome não Preenchido .";
        echo json_encode($status);
        exit();
    }


    // Verificar se está chegando um valor inteiro/float pelo post
    if (intval($_POST['preco']) != 0) {
        $preco = $_POST['preco'];
    } else {
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        $status["mensagem"] = "Preço Inválido";
        echo json_encode($status);
        exit();
    }

    // Verificar se está chegando um valor inteiro pelo post
    if (floatval($_POST['qtdEstoque']) != 0) {
        $qtdEstoque = $_POST['qtdEstoque'];
    } else {
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        $status["mensagem"] = "Quantidade Estoque Inválida.";
        echo json_encode($status);
        exit();
    }

    // Verificar categoria esta vindo por Post
    if ($_POST['categoria'] != 0) {
        $categoria = $_POST['categoria'];
        // Obter o ID do usuário pela sessão atual:
        $idResp = $_SESSION['infosusuario']['idUsuario'];
    } else {
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        $status["mensagem"] = "Categoria Não selecionada.";
        echo json_encode($status);
        exit();
    }

        $foto="fotos/semfoto.jpg";

    try {
        $pdo = Banco::conectar();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "INSERT INTO produtos (codbarras, nome, preco, estoque, idCategoria, idRespCadastro, foto) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $q = $pdo->prepare($sql);
        $q->execute(array($codbarras, $nome, $preco, $qtdEstoque, $categoria, $idResp, $foto));

        header('Content-Type: application/json; charset=utf-8');
            $status["mensagem"] = "Produto Cadastrado com Sucesso!";
            $status["status"] = 1;
            echo json_encode($status);
            exit();

    } catch (PDOException $e) {
        Banco::desconectar();
        if ($e->getCode() == 23000) {
            header('Content-Type: application/json; charset=utf-8');
            $status["mensagem"] = "Códico de Barras do Produto já Cadastrado";
            echo json_encode($status);
            exit();
        } else {
            http_response_code(200);
            header('Content-Type: application/json; charset=utf-8');
            $status["mensagem"] = "Erro.";
            echo json_encode($status);
            exit();
        }
    }

    Banco::desconectar();

   
}
?>