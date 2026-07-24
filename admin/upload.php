<?php
require __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');

if (!estaLogado()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'erro' => 'Não autenticado.']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['imagem'])) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'erro' => 'Nenhuma imagem enviada.']);
    exit;
}
validarCsrf();

$arquivo = $_FILES['imagem'];

if ($arquivo['error'] !== UPLOAD_ERR_OK) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'erro' => 'Falha no envio (código ' . $arquivo['error'] . ').']);
    exit;
}
if ($arquivo['size'] > 8 * 1024 * 1024) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'erro' => 'Imagem muito grande (máximo 8 MB).']);
    exit;
}

/* Valida o tipo real do arquivo */
$info = @getimagesize($arquivo['tmp_name']);
$tipos = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
];
if (!$info || !isset($tipos[$info['mime']])) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'erro' => 'Arquivo inválido. Envie JPG, PNG, WebP ou GIF.']);
    exit;
}

if (!is_dir(PASTA_UPLOADS)) {
    @mkdir(PASTA_UPLOADS, 0755, true);
}

$ext  = $tipos[$info['mime']];
$nome = date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
$destino = PASTA_UPLOADS . '/' . $nome;

if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'erro' => 'Não foi possível salvar a imagem. Verifique as permissões da pasta uploads/.']);
    exit;
}

echo json_encode(['ok' => true, 'url' => URL_UPLOADS . '/' . $nome]);
