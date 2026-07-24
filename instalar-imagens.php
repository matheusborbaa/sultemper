<?php
/**
 * Sultemper — instalador de imagens em alta resolução
 *
 * COMO USAR (uma única vez, logo após subir o site para a hospedagem):
 *   1. Acesse seusite.com.br/instalar-imagens.php no navegador
 *   2. Aguarde o relatório de download
 *   3. APAGUE este arquivo da hospedagem
 *
 * O que ele faz: baixa as imagens originais (alta resolução) dos links
 * temporários da Figma para a pasta assets/img/, e atualiza o dados.json
 * e o index.html para usarem os arquivos locais — as imagens passam a ser
 * permanentes e independentes da Figma.
 *
 * IMPORTANTE: os links da Figma expiram em poucos dias. Rode este script
 * o quanto antes. Se algum link já tiver expirado, me peça para gerar
 * novos links e uma nova versão deste arquivo.
 */

set_time_limit(300);
header('Content-Type: text/html; charset=utf-8');

$pasta = __DIR__ . '/assets/img';
if (!is_dir($pasta)) mkdir($pasta, 0755, true);

/* nome-do-arquivo => lista de URLs candidatas.
   Quando há mais de uma candidata, o script decide pela proporção:
   'paisagem' = mais larga que alta; 'retrato' = mais alta que larga. */
$imagens = [
    'logo.svg'          => ['url' => 'https://www.figma.com/api/mcp/asset/181eb547-1a02-4ead-af56-f85c3aa62a4d'],
    'hero-bg.png'       => [
        'candidatas' => [
            'https://www.figma.com/api/mcp/asset/3911ed90-a27a-4c06-8a7d-5eb12f616e31',
            'https://www.figma.com/api/mcp/asset/c43a302a-3db7-499a-a586-88a4f4cf8b6e',
        ],
        'preferencia' => 'paisagem',
    ],
    'vidro.png'         => ['url' => 'https://www.figma.com/api/mcp/asset/200fb3d8-03df-4c9d-a109-60fbe5205f57'],
    'avatar-arlingo.png'=> ['url' => 'https://www.figma.com/api/mcp/asset/b610c17c-5ce4-485a-9cf3-286596962a2a'],
    'card-esquadrias.png' => ['url' => 'https://www.figma.com/api/mcp/asset/f275706f-5f2c-48df-adee-f7889b55bd6c'],
    'card-abertura.png' => ['url' => 'https://www.figma.com/api/mcp/asset/990fe627-2ef5-44be-a250-913e3e3f11fb'],
    'card-projeto.png'  => ['url' => 'https://www.figma.com/api/mcp/asset/fa2ebc63-2cc6-42c5-a02f-163ab06a41a0'],
    'galeria-1.png'     => ['url' => 'https://www.figma.com/api/mcp/asset/403e8638-f809-4031-ab6d-6212d6cc321c'],
    'galeria-2.png'     => ['url' => 'https://www.figma.com/api/mcp/asset/696bc119-d50f-4936-a608-244007621d57'],
    'galeria-3.png'     => ['url' => 'https://www.figma.com/api/mcp/asset/00482407-8a38-4fb9-ab90-14c04749d63f'],
    'galeria-4.png'     => ['url' => 'https://www.figma.com/api/mcp/asset/d6990752-cb13-4460-a3f8-ad5d563ccf09'],
    'galeria-5.png'     => ['url' => 'https://www.figma.com/api/mcp/asset/d8578fbd-51ca-4ac2-bc9e-66eb7dc7ad49'],
];

function baixar($url) {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (SultemperInstaller)',
        ]);
        $bytes = curl_exec($ch);
        $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($codigo === 200 && $bytes) ? $bytes : null;
    }
    $ctx = stream_context_create(['http' => ['timeout' => 60, 'follow_location' => 1]]);
    $bytes = @file_get_contents($url, false, $ctx);
    return $bytes ?: null;
}

function proporcao($bytes) {
    $tmp = tempnam(sys_get_temp_dir(), 'img');
    file_put_contents($tmp, $bytes);
    $info = @getimagesize($tmp);
    unlink($tmp);
    if (!$info || !$info[1]) return null;
    return $info[0] / $info[1]; // largura / altura
}

echo "<!DOCTYPE html><html lang='pt-BR'><head><meta charset='utf-8'><title>Instalador de imagens — Sultemper</title>
<style>body{font-family:system-ui;max-width:760px;margin:40px auto;padding:0 20px;color:#222}
h1{color:#0d4377}li{margin:6px 0}
.ok{color:#0c6b3d}.erro{color:#9c2b2b}
.alerta{background:#fff6e0;border:1px solid #eedca8;padding:14px 18px;border-radius:10px;margin-top:24px}</style>
</head><body><h1>Instalador de imagens — Sultemper</h1><ul>";

$mapa = [];   // url remota => caminho local
$falhas = 0;

foreach ($imagens as $arquivo => $config) {
    $destino = "$pasta/$arquivo";
    $urlsUsadas = [];

    if (isset($config['candidatas'])) {
        // baixa as candidatas e escolhe pela proporção
        $melhor = null;
        $dadosMelhor = null;
        foreach ($config['candidatas'] as $url) {
            $bytes = baixar($url);
            if (!$bytes) continue;
            $r = proporcao($bytes);
            $ehPaisagem = $r !== null && $r >= 1.3;
            $combina = ($config['preferencia'] === 'paisagem') ? $ehPaisagem : !$ehPaisagem;
            $urlsUsadas[] = $url;
            if ($combina) { $melhor = $url; $dadosMelhor = $bytes; break; }
            if ($melhor === null) { $melhor = $url; $dadosMelhor = $bytes; } // reserva
        }
        $bytes = $dadosMelhor;
        $urlsUsadas = $config['candidatas'];
    } else {
        $bytes = baixar($config['url']);
        $urlsUsadas = [$config['url']];
    }

    if ($bytes) {
        file_put_contents($destino, $bytes);
        $kb = round(strlen($bytes) / 1024);
        echo "<li class='ok'>✔ $arquivo — {$kb} KB</li>";
        foreach ($urlsUsadas as $u) $mapa[$u] = "assets/img/$arquivo";
    } else {
        $falhas++;
        echo "<li class='erro'>✘ $arquivo — não foi possível baixar (link expirado?)</li>";
    }
}

echo '</ul>';

/* Atualiza dados.json e index.html para os caminhos locais */
$atualizados = [];
foreach (['dados.json', 'index.html', 'js/script.js'] as $alvo) {
    $caminho = __DIR__ . '/' . $alvo;
    if (!file_exists($caminho)) continue;
    $conteudo = file_get_contents($caminho);
    $novo = strtr($conteudo, $mapa);
    if ($novo !== $conteudo) {
        copy($caminho, $caminho . '.bak');
        file_put_contents($caminho, $novo, LOCK_EX);
        $atualizados[] = $alvo;
    }
}

if ($atualizados) {
    echo "<p class='ok'><strong>Arquivos atualizados para usar as imagens locais:</strong> " . implode(', ', $atualizados) . " (backups .bak criados)</p>";
}

if ($falhas === 0) {
    echo "<div class='alerta'><strong>Tudo certo!</strong> Agora <strong>apague este arquivo (instalar-imagens.php)</strong> da hospedagem por segurança. As imagens são permanentes e independentes da Figma.</div>";
} else {
    echo "<div class='alerta'><strong>Atenção:</strong> $falhas imagem(ns) falharam. Os links da Figma podem ter expirado — peça novos links e uma nova versão deste instalador.</div>";
}

echo '</body></html>';
