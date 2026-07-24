<?php
/**
 * Sultemper — configuração e autenticação do painel administrativo
 */

session_start();

define('ARQUIVO_DADOS', dirname(__DIR__) . '/dados.json');
define('ARQUIVO_SENHA', __DIR__ . '/senha.json');
define('PASTA_UPLOADS', dirname(__DIR__) . '/uploads');
define('URL_UPLOADS', 'uploads'); // caminho relativo à raiz do site

/** Cria o arquivo de credenciais no primeiro uso (admin / 123mudar) */
function garantirCredenciais() {
    if (!file_exists(ARQUIVO_SENHA)) {
        $padrao = [
            'usuario' => 'admin',
            'hash'    => password_hash('123mudar', PASSWORD_DEFAULT),
        ];
        file_put_contents(ARQUIVO_SENHA, json_encode($padrao), LOCK_EX);
    }
}

function lerCredenciais() {
    garantirCredenciais();
    return json_decode(file_get_contents(ARQUIVO_SENHA), true);
}

function salvarCredenciais($usuario, $novaSenha) {
    $dados = [
        'usuario' => $usuario,
        'hash'    => password_hash($novaSenha, PASSWORD_DEFAULT),
    ];
    return file_put_contents(ARQUIVO_SENHA, json_encode($dados), LOCK_EX) !== false;
}

function estaLogado() {
    return !empty($_SESSION['sultemper_admin']);
}

function exigirLogin() {
    if (!estaLogado()) {
        header('Location: index.php');
        exit;
    }
}

/** Token CSRF */
function tokenCsrf() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function validarCsrf() {
    $token = $_POST['csrf'] ?? ($_SERVER['HTTP_X_CSRF'] ?? '');
    if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'erro' => 'Sessão expirada. Recarregue a página.']);
        exit;
    }
}

function lerDados() {
    // Se o dados.json ainda não existe (primeiro deploy), usa o modelo de exemplo
    if (!file_exists(ARQUIVO_DADOS)) {
        $exemplo = dirname(__DIR__) . '/dados.exemplo.json';
        if (file_exists($exemplo)) {
            return json_decode(file_get_contents($exemplo), true) ?: [];
        }
        return [];
    }
    return json_decode(file_get_contents(ARQUIVO_DADOS), true) ?: [];
}

function salvarDados($dados) {
    $json = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents(ARQUIVO_DADOS, $json, LOCK_EX) !== false;
}

function e($texto) {
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}
