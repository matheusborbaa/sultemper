<?php
/**
 * Sultemper — envio de solicitações de orçamento por e-mail
 *
 * Requisitos: hospedagem com PHP e função mail() habilitada
 * (Hostgator, Locaweb, KingHost, cPanel em geral funcionam por padrão).
 *
 * Se a sua hospedagem exigir SMTP autenticado, me avise que adapto
 * para PHPMailer com as credenciais da conta de e-mail.
 */

// ===================== CONFIGURAÇÃO =====================
$destinatario = 'comercial@sultempervidros.com.br';
$assunto      = 'Novo pedido de orçamento — Site Sultemper';
// Remetente: use um e-mail do MESMO domínio do site para não cair em spam
$remetente    = 'no-reply@sultempervidros.com.br';
// ========================================================

header('Content-Type: application/json; charset=utf-8');

// Aceita apenas POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'erro' => 'Método não permitido.']);
    exit;
}

// Honeypot anti-spam: campo invisível que humanos não preenchem
if (!empty($_POST['site'])) {
    // Finge sucesso para não dar pistas ao robô
    echo json_encode(['ok' => true]);
    exit;
}

// Coleta e sanitiza
function campo($nome) {
    $valor = isset($_POST[$nome]) ? trim($_POST[$nome]) : '';
    $valor = strip_tags($valor);
    $valor = str_replace(["\r", "\n", "%0a", "%0d"], ' ', $valor); // anti header-injection
    return mb_substr($valor, 0, 300);
}

$nome     = campo('nome');
$telefone = campo('telefone');
$endereco = campo('endereco');

// Validação
if ($nome === '' || $telefone === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'erro' => 'Preencha nome e telefone.']);
    exit;
}
if (!preg_match('/[0-9]{8,}/', preg_replace('/\D/', '', $telefone))) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'erro' => 'Informe um telefone válido.']);
    exit;
}

// Monta a mensagem
$dataHora = date('d/m/Y H:i');
$corpo  = "Novo pedido de orçamento recebido pelo site.\n\n";
$corpo .= "Nome: {$nome}\n";
$corpo .= "Telefone/WhatsApp: {$telefone}\n";
$corpo .= "Endereço de instalação: " . ($endereco !== '' ? $endereco : 'não informado') . "\n\n";
$corpo .= "Enviado em: {$dataHora}\n";
$corpo .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'desconhecido') . "\n";

$headers  = "From: Site Sultemper <{$remetente}>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

$enviado = mail(
    $destinatario,
    '=?UTF-8?B?' . base64_encode($assunto) . '?=',
    $corpo,
    $headers
);

if ($enviado) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'erro' => 'Não foi possível enviar agora. Tente novamente ou fale conosco pelo WhatsApp.']);
}
