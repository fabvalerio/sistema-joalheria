<?php
/**
 * Helpers de permissão centralizados.
 * $podeVer($modulo) - visualizar: mostra menu, listar, ver
 * $podeManipular($modulo) - manipular: cadastro, editar, deletar
 */
$ehAdmin = (isset($_COOKIE['nivel_acesso']) && $_COOKIE['nivel_acesso'] === 'Administrador');
$permissoes = [];

if (!$ehAdmin) {
    $permJson = $_COOKIE['permissoes'] ?? '{}';
    $permissoes = json_decode($permJson, true);
    if (is_string($permissoes)) {
        $permissoes = json_decode($permissoes, true) ?: [];
    }
    $permissoes = is_array($permissoes) ? $permissoes : [];
}

$temPermissao = function($val) {
    return ($val === true || $val === 1 || $val === '1');
};

$podeVer = function($modulo) use ($ehAdmin, $permissoes, $temPermissao) {
    if ($ehAdmin) return true;
    if (!isset($permissoes[$modulo])) return false;
    $v = $permissoes[$modulo]['visualizar'] ?? false;
    return $temPermissao($v);
};

$podeManipular = function($modulo) use ($ehAdmin, $permissoes, $temPermissao) {
    if ($ehAdmin) return true;
    if (!isset($permissoes[$modulo])) return false;
    $m = $permissoes[$modulo]['manipular'] ?? false;
    return $temPermissao($m);
};
