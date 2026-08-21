<?php
/**
 * Redirection automatique vers /public/index.php avec conservation des paramètres GET
 */
$queryString = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header('Location: public/index.php' . $queryString);
exit;
