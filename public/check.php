<?php
session_start();
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
echo "<hr>";
echo "Role esperada: 'supervisor_monitor'<br>";
echo "Role na sessão: '" . ($_SESSION['user']['role'] ?? 'N/A') . "'<br>";
echo "Match? " . (($_SESSION['user']['role'] ?? '') === 'supervisor_monitor' ? 'SIM' : 'NÃO');
