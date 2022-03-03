<?php
// Pendente de validação de erros !!! 
// Iniciar utilização de sessão:
session_start();

// Array de Status:
$status = ["status" => 0, "mensagem" => "0", "dados" => 0];

// Verificar se o usuário não está logado:
if (!isset($_SESSION['infosusuario'])) {
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    $status["mensagem"] = "Acesso permitido apenas para usuários autenticados.";
    echo json_encode($status);
}
// Puxar o arquivo de conexão com o banco de dados:
include('db/banco.php');

$pdo = Banco::conectar();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obter as informações do produto e verificar se ele pertence ao usuário logado:
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT idRespCadastro FROM produtos WHERE codbarras = ?";
$q = $pdo->prepare($sql);
$q->execute(array($_POST['idProduto']));
// Resultado do BD:
$data = $q->fetch(PDO::FETCH_ASSOC);
if ($data['idRespCadastro'] != $_SESSION['infosusuario']['idUsuario']) {
    echo 'Este produto não te pertence';
    Banco::desconectar();
    exit();
} else {
    // Definir fuso horário:
    date_default_timezone_set('America/Sao_Paulo');
    
    // Verificar se nome e/ou código de barras não está vazios:
        if ($_POST['idProduto'] != "" && $_POST['nome'] != "" && strlen($_POST['idProduto']) == 5) {
            $idProduto = $_POST['idProduto'];
            $nome = $_POST['nome'];
        } else {
            http_response_code(200);
            header('Content-Type: application/json; charset=utf-8');
            $status["mensagem"] = "Nome não Preenchido .";
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
    if (floatval($_POST['estoque']) != 0) {
        $estoque = $_POST['estoque'];
    } else {
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        $status["mensagem"] = "Quantidade Estoque Inválida.";
        echo json_encode($status);
        exit();
    }

    // Verificar categoria esta vindo por Post
    if ($_POST['idCategoria'] != 0) {
        $idCategoria = $_POST['idCategoria'];
        // Obter o ID do usuário pela sessão atual:
        $idResp = $_SESSION['infosusuario']['idUsuario'];
    } else {
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        $status["mensagem"] = "Categoria Não selecionada.";
        echo json_encode($status);
        exit();
    }
  
  

    try{
        $pdo = Banco::conectar();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "UPDATE produtos SET codbarras = ?, nome = ?, preco = ?, estoque = ?, idCategoria = ? WHERE codbarras = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($idProduto, $nome, $preco, $estoque, $idCategoria, $idProduto));

        header('Content-Type: application/json; charset=utf-8');
            $status["mensagem"] = "Item Editado com Sucesso!";
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