<?php

// acessa as paginas do crud
if (isset($_GET['pagina']) && $_GET['pagina'] === 'cores') {
    include 'paginas/cores.php';
} else {
    include 'paginas/usuarios.php';
}