<?php
// Iniciar sessão:
session_start();

// Array de Status:
$status = ["status" => 0, "mensagem" => "0", "dados" => 0]; 

// Verificar se o usuário não está logado:
if (!isset($_SESSION['infosusuario'])) {
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    $status["mensagem"] = "Erro Voçê Não está Logado.";
    echo json_encode($status);
    exit();
} else {
    // Continar caso o usuário esteja logado:
    // Importar o banco.php
    include('db/banco.php');
    // Variável para armazenar o CODBarras do produto a ser removido:
    // apagar.php?id=21545
    $item = $_POST['codBarras'];
    //echo 'Você vai apagar o item ' .$item;

    // Antes de apagar, devemos verificar se o usuário é os resp pelo cadastro:
    $pdo = Banco::conectar();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT idRespCadastro FROM produtos WHERE codbarras = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($item));
    // Resultado do BD:
    $data = $q->fetch(PDO::FETCH_ASSOC);
    // Verificar se o banco devolveu algum resultado:
    if (!is_array($data)) {

            http_response_code(200);
            header('Content-Type: application/json; charset=utf-8');
            $status["mensagem"] = "Item Inexistente.";
            echo json_encode($status);
            exit();

        Banco::desconectar();
    } else {
        // Se idUsuario == idRespCadastro e devo apagar o produto:
        if ($_SESSION['infosusuario']['idUsuario'] == $data['idRespCadastro']) {
            $sql = "DELETE FROM produtos WHERE codbarras = ?";
            $q = $pdo->prepare($sql);
            $q->execute(array($item));
            
            http_response_code(200);
            header('Content-Type: application/json; charset=utf-8');
            $status["mensagem"] = "Produto Removido com Sucesso!";
            $status["status"] = 1;
            echo json_encode($status);
            Banco::desconectar();
            exit();
            
        } else {
            http_response_code(200);
            header('Content-Type: application/json; charset=utf-8');
            $status["mensagem"] = "Este Produto não te Pertense!";
            $status["status"] = 0;
            echo json_encode($status);
            Banco::desconectar();
            exit();
        }
        Banco::desconectar();
    }
}
