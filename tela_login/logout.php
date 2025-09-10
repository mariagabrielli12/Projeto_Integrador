<?php
// 1. Inicia a sessão para poder aceder-lhe
session_start();
 
// 2. Remove todas as variáveis da sessão
$_SESSION = array();
 
// 3. Destrói a sessão completamente
session_destroy();
 
// 4. Redireciona o utilizador para a página de login
header("location: index.php");
exit;
?>