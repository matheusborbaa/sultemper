<?php
require __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');

if (!estaLogado()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'erro' => 'Não autenticado.']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'erro' => 'Método não permitido.']);
    exit;
}
validarCsrf();

$acao = $_POST['acao'] ?? 'conteudo';

/* ---------- Troca de senha ---------- */
if ($acao === 'senha') {
    $atual = $_POST['senha_atual'] ?? '';
    $nova  = $_POST['senha_nova'] ?? '';
    $conf  = $_POST['senha_confirma'] ?? '';

    $cred = lerCredenciais();
    if (!password_verify($atual, $cred['hash'])) {
        echo json_encode(['ok' => false, 'erro' => 'A senha atual está incorreta.']);
        exit;
    }
    if (mb_strlen($nova) < 6) {
        echo json_encode(['ok' => false, 'erro' => 'A nova senha deve ter pelo menos 6 caracteres.']);
        exit;
    }
    if ($nova !== $conf) {
        echo json_encode(['ok' => false, 'erro' => 'A confirmação não confere com a nova senha.']);
        exit;
    }
    if (salvarCredenciais($cred['usuario'], $nova)) {
        echo json_encode(['ok' => true, 'msg' => 'Senha alterada com sucesso!']);
    } else {
        http_response_code(500);
        echo json_encode(['ok' => false, 'erro' => 'Não foi possível gravar a nova senha.']);
    }
    exit;
}

/* ---------- Salvar conteúdo ---------- */
$json = $_POST['dados'] ?? '';
$novos = json_decode($json, true);

if (!is_array($novos)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'erro' => 'Dados inválidos.']);
    exit;
}

/* Sanitização: remove tags de todos os textos, valida estrutura básica */
function limpar($valor) {
    if (is_array($valor)) {
        return array_map('limpar', $valor);
    }
    return trim(strip_tags((string) $valor));
}

/* Códigos de rastreamento: preserva o HTML (só o admin autenticado chega aqui) */
$rastreamento = [
    'head' => mb_substr(trim((string) ($novos['rastreamento']['head'] ?? '')), 0, 20000),
    'body' => mb_substr(trim((string) ($novos['rastreamento']['body'] ?? '')), 0, 20000),
];
unset($novos['rastreamento']);

$novos = limpar($novos);
$novos['rastreamento'] = $rastreamento;

/* Estrutura permitida — ignora chaves desconhecidas */
$permitido = ['whatsapp', 'hero', 'sobre', 'solucoes', 'processo', 'depoimentos', 'portfolio', 'rodape', 'rastreamento'];
$dados = array_intersect_key($novos, array_flip($permitido));

/* WhatsApp: só dígitos */
if (isset($dados['whatsapp'])) {
    $dados['whatsapp'] = preg_replace('/\D/', '', $dados['whatsapp']);
}

/* Backup do arquivo anterior */
if (file_exists(ARQUIVO_DADOS)) {
    @copy(ARQUIVO_DADOS, ARQUIVO_DADOS . '.bak');
}

if (salvarDados($dados)) {
    echo json_encode(['ok' => true, 'msg' => 'Conteúdo salvo com sucesso!']);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'erro' => 'Não foi possível gravar o arquivo dados.json. Verifique as permissões da pasta.']);
}
