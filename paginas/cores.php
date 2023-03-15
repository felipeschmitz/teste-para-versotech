<?php

require __DIR__ . '/../connection.php';
require __DIR__ . '/../templates.php';

$acao = $_GET['acao'] ?? '';

switch ($acao) {
    case 'cadastrar':
        echo $twig->render('paginas/cores/cadastrar.html.twig');
        break;

    case 'cadastro':
        if (isset($_POST['acao']) && $_POST['acao'] === 'cadastro') {
            $connection = new Connection();

            $store = $connection->storeColor($_POST);

            header("Location: /?pagina=cores&acao=editar&id={$store}");
        }
        break;

    case 'editar':
        $connection = new Connection();

        $color = $connection->getColorById($_GET['id']);

        echo $twig->render('paginas/cores/editar.html.twig', ['color' => $color]);
        break;

    case 'salvar':
        if ((isset($_POST['acao']) && $_POST['acao'] === 'salvar') && isset($_POST['id'])) {
          $connection = new Connection();
            
            $update = $connection->updateColor($_POST);

            header("Location: /?pagina=cores&acao=editar&id={$_POST['id']}");
        }
        break;

    case 'remover':
        if ((isset($_POST['acao']) && $_POST['acao'] === 'remover') && isset($_POST['id'])) {
            $connection = new Connection();
            
            $update = $connection->destroyColor($_POST);

            header('Location: /?pagina=cores');
        }
        break;

    
    default:
        $connection = new Connection();

        $colors = $connection->query("SELECT * FROM colors");

        echo $twig->render('paginas/cores/index.html.twig', ['colors' => $colors]);
        break;
}