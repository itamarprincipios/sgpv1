<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['test_count'])) {
    $_SESSION['test_count'] = 0;
}
$_SESSION['test_count']++;

echo "<h1>Diagnóstico de Sessão PHP</h1>";
echo "<b>Session Save Path:</b> " . session_save_path() . "<br>";
echo "<b>Session Status:</b> " . session_status() . " (2 = ACTIVE)<br>";
echo "<b>Session ID:</b> " . session_id() . "<br>";
echo "<b>Cookie Params:</b> <pre>" . print_r(session_get_cookie_params(), true) . "</pre>";
echo "<hr>";
echo "<h3>Teste de Persistência</h3>";
echo "Valor esperado ao recarregar: <b>" . ($_SESSION['test_count'] + 1) . "</b><br>";
echo "Valor ATUAL: <b style='font-size:20px; color:red;'>" . $_SESSION['test_count'] . "</b><br>";
echo "(Se este número não aumentar ao recarregar a página, as sessões não estão funcionando).";
echo "<hr>";
echo "<h3>Conteúdo da Sessão</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
