<?php

require __DIR__ . '/../connection.php';
require __DIR__ . '/../templates.php';

$acao = $_GET['acao'] ?? '';

switch ($acao) {
    case 'cadastrar':
        $connection = new Connection();

        $colors = $connection->query("SELECT * FROM colors");

        echo $twig->render('paginas/usuarios/cadastrar.html.twig', ['colors' => $colors]);
      break;

    case 'cadastro':
        if (isset($_POST['acao']) && $_POST['acao'] === 'cadastro') {
            $connection = new Connection();

            $store = $connection->storeUser($_POST);

            header("Location: /?acao=editar&id={$store}");
        }
        break;

    case 'editar':
        $connection = new Connection();

        $user = $connection->getUserById($_GET['id']);

        $colors = $connection->query("SELECT * FROM colors");

        echo $twig->render('paginas/usuarios/editar.html.twig', ['user' => $user, 'colors' => $colors]);
        break;

    case 'salvar':
        if ((isset($_POST['acao']) && $_POST['acao'] === 'salvar') && isset($_POST['id'])) {
            $connection = new Connection();
            
            $update = $connection->updateUser($_POST);

            header("Location: /?acao=editar&id={$_POST['id']}");
        }
        break;

    case 'remover':
        if ((isset($_POST['acao']) && $_POST['acao'] === 'remover') && isset($_POST['id'])) {
            $connection = new Connection();
            
            $update = $connection->destroyUser($_POST);

            header('Location: /');
        }
        break;

    default:
        $connection = new Connection();

        $users = $connection->query("SELECT * FROM users");

        echo $twig->render('paginas/usuarios/index.html.twig', ['users' => $users]);
        break;
}

