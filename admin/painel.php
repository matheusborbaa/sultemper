<?php
require __DIR__ . '/config.php';
exigirLogin();
$dados = lerDados();
$csrf = tokenCsrf();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Painel Sultemper</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    :root {
      --azul: #0d4377; --ciano: #30a9ff; --escuro: #000927;
      --borda: #dfe3e8; --fundo: #f3f6f9;
    }
    body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--fundo); color: #222; }

    .topo {
      position: sticky; top: 0; z-index: 50;
      display: flex; align-items: center; justify-content: space-between;
      padding: 14px 28px; background: var(--escuro); color: #fff;
    }
    .topo h1 { font-size: 18px; font-weight: 700; }
    .topo .acoes { display: flex; gap: 10px; align-items: center; }
    .topo a { color: #9fc9ff; font-size: 14px; text-decoration: none; }
    .topo a:hover { text-decoration: underline; }

    main { max-width: 900px; margin: 28px auto 120px; padding: 0 20px; }

    .cartao {
      background: #fff; border: 1px solid var(--borda); border-radius: 16px;
      padding: 24px; margin-bottom: 22px;
    }
    .cartao h2 {
      font-size: 17px; color: var(--azul); margin-bottom: 4px;
      display: flex; align-items: center; gap: 8px;
    }
    .cartao p.dica { font-size: 13px; color: #778; margin-bottom: 16px; }

    label { display: block; font-size: 13px; font-weight: 600; color: #445; margin: 12px 0 5px; }
    input[type=text], textarea {
      width: 100%; padding: 10px 12px; font-size: 14px;
      border: 1px solid var(--borda); border-radius: 10px; outline: none;
      font-family: inherit; background: #fbfcfe;
    }
    input:focus, textarea:focus { border-color: var(--ciano); background: #fff; }
    textarea { resize: vertical; min-height: 70px; }

    .linha2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

    .item {
      border: 1px solid var(--borda); border-radius: 12px;
      padding: 16px; margin-top: 14px; position: relative; background: #fafbfd;
    }
    .item .remover {
      position: absolute; top: 10px; right: 10px;
      border: none; background: #fdeaea; color: #9c2b2b;
      font-size: 12px; font-weight: 700; padding: 5px 10px;
      border-radius: 8px; cursor: pointer;
    }
    .item .remover:hover { background: #f8d3d3; }

    .btn-add {
      margin-top: 14px; border: 1px dashed var(--ciano); background: #f0f8ff;
      color: var(--azul); font-weight: 600; font-size: 14px;
      padding: 10px 16px; border-radius: 10px; cursor: pointer; width: 100%;
    }
    .btn-add:hover { background: #e2f1ff; }

    .campo-img { display: flex; gap: 8px; align-items: center; }
    .campo-img input[type=text] { flex: 1; }
    .campo-img .mini {
      width: 46px; height: 46px; border-radius: 8px; object-fit: cover;
      border: 1px solid var(--borda); background: #eef1f5; flex-shrink: 0;
    }
    .btn-upload {
      border: none; background: var(--azul); color: #fff; font-size: 13px;
      font-weight: 600; padding: 10px 14px; border-radius: 10px; cursor: pointer;
      white-space: nowrap;
    }
    .btn-upload:hover { filter: brightness(1.15); }

    .rodape-fixo {
      position: fixed; bottom: 0; left: 0; right: 0; z-index: 40;
      background: #fff; border-top: 1px solid var(--borda);
      padding: 14px 20px; display: flex; justify-content: center; gap: 14px; align-items: center;
    }
    .btn-salvar {
      border: none; border-radius: 999px; cursor: pointer;
      background: linear-gradient(100deg, #116ab2, #37a6ff);
      color: #fff; font-size: 16px; font-weight: 700; padding: 13px 44px;
    }
    .btn-salvar:hover { filter: brightness(1.08); }
    .btn-salvar:disabled { opacity: 0.6; cursor: wait; }

    .aviso {
      display: none; padding: 10px 18px; border-radius: 10px; font-size: 14px; font-weight: 600;
    }
    .aviso.ok { display: block; background: #e3f7ec; color: #0c6b3d; border: 1px solid #b5e6cd; }
    .aviso.erro { display: block; background: #fdeaea; color: #9c2b2b; border: 1px solid #f2c5c5; }

    .senha-btn {
      border: none; background: #eef1f5; color: var(--azul); font-weight: 600;
      font-size: 14px; padding: 10px 18px; border-radius: 10px; cursor: pointer; margin-top: 14px;
    }
  </style>
</head>
<body>
  <div class="topo">
    <h1>⚙️ Painel Sultemper</h1>
    <div class="acoes">
      <a href="../" target="_blank">Ver o site ↗</a>
      <a href="logout.php">Sair</a>
    </div>
  </div>

  <main>
    <!-- WhatsApp -->
    <div class="cartao">
      <h2>📱 WhatsApp</h2>
      <p class="dica">Número que recebe os contatos do site. Formato: código do país + DDD + número, só dígitos. Ex.: 5547999990000</p>
      <input type="text" id="c-whatsapp" placeholder="5547999990000" />
    </div>

    <!-- Hero -->
    <div class="cartao">
      <h2>🏠 Topo do site (Hero)</h2>
      <p class="dica">Primeira dobra da página.</p>
      <label>Título (parte normal)</label>
      <input type="text" id="c-hero-titulo" />
      <label>Título (parte destacada em azul)</label>
      <input type="text" id="c-hero-destaque" />
      <label>Subtítulo</label>
      <textarea id="c-hero-subtitulo"></textarea>
    </div>

    <!-- Sobre -->
    <div class="cartao">
      <h2>🏢 Seção "Serviço de qualidade"</h2>
      <label>Texto de apresentação</label>
      <textarea id="c-sobre-texto"></textarea>
      <div class="linha2">
        <div>
          <label>Número 1</label>
          <input type="text" id="c-stat0-numero" />
          <label>Legenda 1</label>
          <input type="text" id="c-stat0-label" />
        </div>
        <div>
          <label>Número 2</label>
          <input type="text" id="c-stat1-numero" />
          <label>Legenda 2</label>
          <input type="text" id="c-stat1-label" />
        </div>
      </div>
    </div>

    <!-- Soluções -->
    <div class="cartao">
      <h2>🧩 Nossas soluções</h2>
      <label>Texto descritivo (direita do título)</label>
      <textarea id="c-solucoes-descricao"></textarea>
      <div id="lista-solucoes"></div>
    </div>

    <!-- Processo -->
    <div class="cartao">
      <h2>🛠️ Nosso processo</h2>
      <label>Subtítulo</label>
      <input type="text" id="c-processo-subtitulo" />
      <p class="dica" style="margin-top:12px">Etapas (use uma quebra de linha para dividir em duas linhas no site):</p>
      <div id="lista-etapas" class="linha2" style="grid-template-columns:1fr 1fr 1fr;"></div>
    </div>

    <!-- Depoimentos -->
    <div class="cartao">
      <h2>💬 Depoimentos</h2>
      <p class="dica">O site exibe um por vez, com setas para navegar.</p>
      <div id="lista-depoimentos"></div>
      <button type="button" class="btn-add" onclick="addDepoimento()">+ Adicionar depoimento</button>
    </div>

    <!-- Portfólio -->
    <div class="cartao">
      <h2>🖼️ Portfólio (galeria de fotos)</h2>
      <p class="dica">Fotos exibidas na galeria, em grade de 3 colunas. Envie um arquivo ou cole a URL da imagem.</p>
      <div id="lista-portfolio"></div>
      <button type="button" class="btn-add" onclick="addPortfolio()">+ Adicionar foto</button>
    </div>

    <!-- Rodapé -->
    <div class="cartao">
      <h2>🔗 Rodapé e redes sociais</h2>
      <label>Texto de copyright</label>
      <input type="text" id="c-rodape-copyright" />
      <label>Link do Facebook</label>
      <input type="text" id="c-rodape-facebook" placeholder="https://facebook.com/sultemper" />
      <label>Link do LinkedIn</label>
      <input type="text" id="c-rodape-linkedin" placeholder="https://linkedin.com/company/sultemper" />
      <label>Link do Instagram</label>
      <input type="text" id="c-rodape-instagram" placeholder="https://instagram.com/sultemper" />
      <p class="dica" style="margin-top:10px">O botão de WhatsApp do rodapé usa automaticamente o número configurado acima.</p>
    </div>

    <!-- Rastreamento -->
    <div class="cartao">
      <h2>📊 Códigos de rastreamento</h2>
      <p class="dica">Cole aqui os códigos completos (com as tags &lt;script&gt;) fornecidos pelo Facebook/Meta Pixel, Google Tag, Google Analytics, GTM etc. Eles são inseridos automaticamente nas páginas do site.</p>
      <label>Antes do fechamento do &lt;/head&gt;</label>
      <textarea id="c-rastreamento-head" style="min-height:120px;font-family:monospace;font-size:13px" placeholder="&lt;!-- Meta Pixel, Google tag (gtag.js), GTM... --&gt;"></textarea>
      <label>Logo após a abertura do &lt;body&gt;</label>
      <textarea id="c-rastreamento-body" style="min-height:100px;font-family:monospace;font-size:13px" placeholder="&lt;!-- Ex.: &lt;noscript&gt; do GTM --&gt;"></textarea>
    </div>

    <!-- Senha -->
    <div class="cartao">
      <h2>🔒 Trocar senha de acesso</h2>
      <div class="linha2">
        <div>
          <label>Senha atual</label>
          <input type="password" id="s-atual" autocomplete="current-password" style="width:100%;padding:10px 12px;font-size:14px;border:1px solid var(--borda);border-radius:10px;" />
        </div>
        <div></div>
        <div>
          <label>Nova senha (mín. 6 caracteres)</label>
          <input type="password" id="s-nova" autocomplete="new-password" style="width:100%;padding:10px 12px;font-size:14px;border:1px solid var(--borda);border-radius:10px;" />
        </div>
        <div>
          <label>Confirmar nova senha</label>
          <input type="password" id="s-confirma" autocomplete="new-password" style="width:100%;padding:10px 12px;font-size:14px;border:1px solid var(--borda);border-radius:10px;" />
        </div>
      </div>
      <button type="button" class="senha-btn" onclick="trocarSenha()">Alterar senha</button>
      <div class="aviso" id="aviso-senha" style="margin-top:12px"></div>
    </div>
  </main>

  <div class="rodape-fixo">
    <div class="aviso" id="aviso-salvar"></div>
    <button type="button" class="btn-salvar" id="btnSalvar" onclick="salvarTudo()">💾 Salvar alterações</button>
  </div>

  <input type="file" id="seletorArquivo" accept="image/*" style="display:none" />

<script>
const CSRF = <?= json_encode($csrf) ?>;
let DADOS = <?= json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?> || {};

/* ================= Utilidades ================= */
const $ = (id) => document.getElementById(id);
const esc = (t) => String(t ?? '').replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

function campoImagem(valor, onchangeNome) {
  return `
    <div class="campo-img">
      <img class="mini" src="${esc(valor) || ''}" onerror="this.style.opacity=.25" alt="" />
      <input type="text" value="${esc(valor)}" placeholder="URL da imagem"
        oninput="this.parentElement.querySelector('.mini').src=this.value" data-campo="${onchangeNome}" />
      <button type="button" class="btn-upload" onclick="enviarImagem(this)">📤 Enviar arquivo</button>
    </div>`;
}

/* ================= Renderização das listas ================= */
function renderSolucoes() {
  const alvo = $('lista-solucoes');
  alvo.innerHTML = (DADOS.solucoes?.cards || []).map((c, i) => `
    <div class="item" data-tipo="solucao" data-i="${i}">
      <label>Título do card ${i + 1}</label>
      <input type="text" value="${esc(c.titulo)}" data-campo="titulo" />
      <label>Texto</label>
      <textarea data-campo="texto">${esc(c.texto)}</textarea>
      <label>Imagem</label>
      ${campoImagem(c.imagem, 'imagem')}
    </div>`).join('');
}

function renderEtapas() {
  const alvo = $('lista-etapas');
  alvo.innerHTML = (DADOS.processo?.etapas || []).map((e2, i) => `
    <div>
      <label>Etapa ${i + 1}</label>
      <textarea data-etapa="${i}" style="min-height:54px">${esc(e2)}</textarea>
    </div>`).join('');
}

function renderDepoimentos() {
  const alvo = $('lista-depoimentos');
  alvo.innerHTML = (DADOS.depoimentos || []).map((d, i) => `
    <div class="item" data-tipo="depoimento" data-i="${i}">
      <button type="button" class="remover" onclick="removerDepoimento(${i})">✕ remover</button>
      <label>Nome e cargo</label>
      <input type="text" value="${esc(d.nome)}" data-campo="nome" placeholder="Maria Silva - Arquiteta" />
      <label>Empresa / cidade</label>
      <input type="text" value="${esc(d.empresa)}" data-campo="empresa" placeholder="Construtora XYZ / Blumenau" />
      <label>Depoimento</label>
      <textarea data-campo="texto">${esc(d.texto)}</textarea>
      <label>Foto do cliente</label>
      ${campoImagem(d.foto, 'foto')}
    </div>`).join('');
}

function renderPortfolio() {
  const alvo = $('lista-portfolio');
  alvo.innerHTML = (DADOS.portfolio || []).map((url, i) => `
    <div class="item" data-tipo="portfolio" data-i="${i}">
      <button type="button" class="remover" onclick="removerPortfolio(${i})">✕ remover</button>
      <label>Foto ${i + 1}</label>
      ${campoImagem(url, 'url')}
    </div>`).join('');
}

/* ================= Ações das listas ================= */
function coletarListas() {
  // sincroniza o que está na tela para DADOS antes de mexer nas listas
  colher();
}
function addDepoimento() {
  coletarListas();
  (DADOS.depoimentos = DADOS.depoimentos || []).push({ foto: '', nome: '', empresa: '', texto: '' });
  renderDepoimentos();
}
function removerDepoimento(i) {
  coletarListas();
  DADOS.depoimentos.splice(i, 1);
  renderDepoimentos();
}
function addPortfolio() {
  coletarListas();
  (DADOS.portfolio = DADOS.portfolio || []).push('');
  renderPortfolio();
}
function removerPortfolio(i) {
  coletarListas();
  DADOS.portfolio.splice(i, 1);
  renderPortfolio();
}

/* ================= Upload ================= */
let inputUrlAtual = null;
function enviarImagem(botao) {
  inputUrlAtual = botao.parentElement.querySelector('input[type=text]');
  $('seletorArquivo').click();
}
$('seletorArquivo').addEventListener('change', async function () {
  if (!this.files.length || !inputUrlAtual) return;
  const fd = new FormData();
  fd.append('imagem', this.files[0]);
  fd.append('csrf', CSRF);
  const btn = inputUrlAtual.parentElement.querySelector('.btn-upload');
  const rotulo = btn.textContent;
  btn.textContent = 'Enviando…';
  btn.disabled = true;
  try {
    const r = await fetch('upload.php', { method: 'POST', body: fd });
    const j = await r.json();
    if (j.ok) {
      inputUrlAtual.value = j.url;
      inputUrlAtual.dispatchEvent(new Event('input'));
    } else {
      alert(j.erro || 'Falha no upload.');
    }
  } catch (e) {
    alert('Falha de conexão no upload.');
  }
  btn.textContent = rotulo;
  btn.disabled = false;
  this.value = '';
});

/* ================= Preencher e colher ================= */
function preencher() {
  $('c-whatsapp').value = DADOS.whatsapp || '';
  $('c-hero-titulo').value = DADOS.hero?.titulo || '';
  $('c-hero-destaque').value = DADOS.hero?.destaque || '';
  $('c-hero-subtitulo').value = DADOS.hero?.subtitulo || '';
  $('c-sobre-texto').value = DADOS.sobre?.texto || '';
  $('c-stat0-numero').value = DADOS.sobre?.stats?.[0]?.numero || '';
  $('c-stat0-label').value = DADOS.sobre?.stats?.[0]?.label || '';
  $('c-stat1-numero').value = DADOS.sobre?.stats?.[1]?.numero || '';
  $('c-stat1-label').value = DADOS.sobre?.stats?.[1]?.label || '';
  $('c-solucoes-descricao').value = DADOS.solucoes?.descricao || '';
  $('c-processo-subtitulo').value = DADOS.processo?.subtitulo || '';
  $('c-rastreamento-head').value = DADOS.rastreamento?.head || '';
  $('c-rastreamento-body').value = DADOS.rastreamento?.body || '';
  $('c-rodape-copyright').value = DADOS.rodape?.copyright || '';
  $('c-rodape-facebook').value = DADOS.rodape?.facebook || '';
  $('c-rodape-linkedin').value = DADOS.rodape?.linkedin || '';
  $('c-rodape-instagram').value = DADOS.rodape?.instagram || '';
  renderSolucoes();
  renderEtapas();
  renderDepoimentos();
  renderPortfolio();
}

function colher() {
  DADOS.whatsapp = $('c-whatsapp').value.trim();
  DADOS.hero = {
    titulo: $('c-hero-titulo').value,
    destaque: $('c-hero-destaque').value,
    subtitulo: $('c-hero-subtitulo').value,
  };
  DADOS.sobre = {
    texto: $('c-sobre-texto').value,
    stats: [
      { numero: $('c-stat0-numero').value, label: $('c-stat0-label').value },
      { numero: $('c-stat1-numero').value, label: $('c-stat1-label').value },
    ],
  };
  DADOS.solucoes = {
    descricao: $('c-solucoes-descricao').value,
    cards: [...document.querySelectorAll('[data-tipo=solucao]')].map((el) => ({
      titulo: el.querySelector('[data-campo=titulo]').value,
      texto: el.querySelector('[data-campo=texto]').value,
      imagem: el.querySelector('.campo-img input[type=text]').value,
    })),
  };
  DADOS.processo = {
    subtitulo: $('c-processo-subtitulo').value,
    etapas: [...document.querySelectorAll('[data-etapa]')].map((el) => el.value),
  };
  DADOS.depoimentos = [...document.querySelectorAll('[data-tipo=depoimento]')].map((el) => ({
    nome: el.querySelector('[data-campo=nome]').value,
    empresa: el.querySelector('[data-campo=empresa]').value,
    texto: el.querySelector('[data-campo=texto]').value,
    foto: el.querySelector('.campo-img input[type=text]').value,
  }));
  DADOS.portfolio = [...document.querySelectorAll('[data-tipo=portfolio]')]
    .map((el) => el.querySelector('.campo-img input[type=text]').value)
    .filter((u) => u.trim() !== '');
  DADOS.rastreamento = {
    head: $('c-rastreamento-head').value,
    body: $('c-rastreamento-body').value,
  };
  DADOS.rodape = {
    copyright: $('c-rodape-copyright').value,
    facebook: $('c-rodape-facebook').value.trim(),
    linkedin: $('c-rodape-linkedin').value.trim(),
    instagram: $('c-rodape-instagram').value.trim(),
  };
}

/* ================= Salvar ================= */
function avisar(id, mensagem, tipo) {
  const el = $(id);
  el.textContent = mensagem;
  el.className = 'aviso ' + tipo;
  if (id === 'aviso-salvar') setTimeout(() => (el.className = 'aviso'), 5000);
}

async function salvarTudo() {
  colher();
  const btn = $('btnSalvar');
  btn.disabled = true;
  try {
    const fd = new FormData();
    fd.append('acao', 'conteudo');
    fd.append('csrf', CSRF);
    fd.append('dados', JSON.stringify(DADOS));
    const r = await fetch('salvar.php', { method: 'POST', body: fd });
    const j = await r.json();
    avisar('aviso-salvar', j.ok ? j.msg : (j.erro || 'Erro ao salvar.'), j.ok ? 'ok' : 'erro');
  } catch (e) {
    avisar('aviso-salvar', 'Falha de conexão ao salvar.', 'erro');
  }
  btn.disabled = false;
}

async function trocarSenha() {
  const fd = new FormData();
  fd.append('acao', 'senha');
  fd.append('csrf', CSRF);
  fd.append('senha_atual', $('s-atual').value);
  fd.append('senha_nova', $('s-nova').value);
  fd.append('senha_confirma', $('s-confirma').value);
  try {
    const r = await fetch('salvar.php', { method: 'POST', body: fd });
    const j = await r.json();
    avisar('aviso-senha', j.ok ? j.msg : (j.erro || 'Erro.'), j.ok ? 'ok' : 'erro');
    if (j.ok) { $('s-atual').value = $('s-nova').value = $('s-confirma').value = ''; }
  } catch (e) {
    avisar('aviso-senha', 'Falha de conexão.', 'erro');
  }
}

preencher();
</script>
</body>
</html>
