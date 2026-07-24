/* ============================================================
   Sultemper — interações da landing page
   Conteúdo dinâmico carregado de dados.json (editável no /admin)
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
  /* ============================================================
     UTILIDADES
     ============================================================ */
  const esc = (t) =>
    String(t ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  /** Converte texto simples com **negrito** e quebras de linha em HTML seguro */
  const rico = (texto) =>
    esc(texto)
      .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
      .replace(/\n/g, '<br />');

  /* ============================================================
     ESTADO DINÂMICO (padrões substituídos pelo dados.json)
     ============================================================ */
  let testimonials = [
    {
      foto: 'https://www.figma.com/api/mcp/asset/5e543a59-fb59-4c5b-88b1-29cc90d7b50f',
      nome: 'Arlingo Ludwig - Gerente Operacional',
      empresa: 'Shopping Neumarkt/Blumenau',
      texto:
        '" A SulTemper é nosso fornecedor a mais de dez anos, com soluções técnicas criativas e de qualidade, e com diversidade de serviços. Nos atende com cumprimento de prazos e qualidade de serviços. "',
    },
  ];
  let whatsappNumero = '';

  /* ============================================================
     HIDRATAÇÃO A PARTIR DO dados.json
     ============================================================ */
  const aplicarDados = (d) => {
    if (!d || typeof d !== 'object') return;

    whatsappNumero = d.whatsapp || '';

    // Hero
    if (d.hero) {
      if (d.hero.titulo) document.getElementById('heroTitulo').textContent = d.hero.titulo;
      if (d.hero.destaque) document.getElementById('heroDestaque').textContent = ' ' + d.hero.destaque;
      if (d.hero.subtitulo) document.getElementById('heroSubtitulo').textContent = d.hero.subtitulo;
    }

    // Sobre
    if (d.sobre) {
      if (d.sobre.texto) document.getElementById('sobreTexto').innerHTML = rico(d.sobre.texto);
      const statCards = document.querySelectorAll('.stat-card');
      (d.sobre.stats || []).forEach((stat, i) => {
        if (!statCards[i]) return;
        statCards[i].querySelector('.stat-card__number').textContent = stat.numero;
        statCards[i].querySelector('.stat-card__label').textContent = stat.label;
      });
    }

    // Soluções
    if (d.solucoes) {
      if (d.solucoes.descricao) {
        document.getElementById('solucoesDescricao').innerHTML = rico(d.solucoes.descricao);
      }
      if (Array.isArray(d.solucoes.cards) && d.solucoes.cards.length) {
        document.getElementById('solutionsTrack').innerHTML = d.solucoes.cards
          .map(
            (c) => `
          <article class="sol-card">
            <div class="sol-card__img"><img src="${esc(c.imagem)}" alt="${esc(c.titulo)}" /></div>
            <div class="sol-card__body">
              <h3 class="sol-card__title">${esc(c.titulo)}</h3>
              <p class="sol-card__text">${esc(c.texto)}</p>
            </div>
          </article>`
          )
          .join('');
      }
    }

    // Processo
    if (d.processo) {
      if (d.processo.subtitulo) {
        document.getElementById('processoSubtitulo').textContent = d.processo.subtitulo;
      }
      const labels = document.querySelectorAll('.process-card__label');
      (d.processo.etapas || []).forEach((etapa, i) => {
        if (labels[i]) labels[i].innerHTML = rico(etapa);
      });
    }

    // Depoimentos
    if (Array.isArray(d.depoimentos) && d.depoimentos.length) {
      testimonials = d.depoimentos;
      current = 0;
      renderTestimonial(0, false);
    }

    // Portfólio
    if (Array.isArray(d.portfolio) && d.portfolio.length) {
      document.querySelector('.gallery').innerHTML = d.portfolio
        .map((url) => `<div class="gallery__item"><img src="${esc(url)}" alt="Projeto Sultemper" /></div>`)
        .join('');
    }

    // Rodapé
    if (d.rodape) {
      if (d.rodape.copyright) document.getElementById('footerCopy').textContent = d.rodape.copyright;
      const setLink = (id, url) => {
        const el = document.getElementById(id);
        if (el && url) el.href = url;
      };
      setLink('socFacebook', d.rodape.facebook);
      setLink('socLinkedin', d.rodape.linkedin);
      setLink('socInstagram', d.rodape.instagram);
    }
    if (whatsappNumero) {
      document.getElementById('socWhatsapp').href = `https://wa.me/${whatsappNumero}`;
    }
  };

  fetch('dados.json', { cache: 'no-store' })
    .then((r) => (r.ok ? r.json() : null))
    .then(aplicarDados)
    .catch(() => {
      /* sem servidor (arquivo aberto localmente): mantém o conteúdo padrão */
    });

  /* ============================================================
     NAVEGAÇÃO ATIVA
     ============================================================ */
  const navLinks = document.querySelectorAll('.header__nav .nav-link');
  const sections = ['inicio', 'sobre', 'solucoes', 'portfolio']
    .map((id) => document.getElementById(id))
    .filter(Boolean);

  const setActive = (id) => {
    navLinks.forEach((link) => {
      link.classList.toggle('nav-link--active', link.getAttribute('href') === `#${id}`);
    });
  };

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) setActive(entry.target.id);
      });
    },
    { rootMargin: '-40% 0px -55% 0px' }
  );
  sections.forEach((section) => observer.observe(section));

  /* ============================================================
     CARROSSEL DE SOLUÇÕES
     ============================================================ */
  const track = document.getElementById('solutionsTrack');
  document.querySelectorAll('.solutions__arrow').forEach((btn) => {
    btn.addEventListener('click', () => {
      const dir = Number(btn.dataset.dir) || 1;
      const card = track.querySelector('.sol-card');
      const step = card ? card.offsetWidth + 51 : 420;
      track.scrollBy({ left: dir * step, behavior: 'smooth' });
    });
  });

  /* ============================================================
     DEPOIMENTOS
     ============================================================ */
  let current = 0;
  const card = document.getElementById('testimonialCard');
  const avatarEl = document.getElementById('tAvatar');
  const nameEl = document.getElementById('tName');
  const companyEl = document.getElementById('tCompany');
  const quoteEl = document.getElementById('tQuote');

  const renderTestimonial = (index, animar = true) => {
    const item = testimonials[index];
    if (!item) return;
    const aplicar = () => {
      avatarEl.src = item.foto || '';
      nameEl.textContent = item.nome || '';
      companyEl.textContent = item.empresa || '';
      quoteEl.textContent = item.texto || '';
      card.classList.remove('is-fading');
    };
    if (animar) {
      card.classList.add('is-fading');
      setTimeout(aplicar, 250);
    } else {
      aplicar();
    }
  };

  const stepTestimonial = (dir) => {
    if (testimonials.length < 2) return;
    current = (current + dir + testimonials.length) % testimonials.length;
    renderTestimonial(current);
  };

  document.getElementById('tPrev').addEventListener('click', () => stepTestimonial(-1));
  document.getElementById('tNext').addEventListener('click', () => stepTestimonial(1));

  /* ============================================================
     HEADER FIXO COM SOMBRA
     ============================================================ */
  const header = document.querySelector('.header');
  const onScroll = () => {
    header.classList.toggle('header--scrolled', window.scrollY > 10);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ============================================================
     FORMULÁRIOS DE ORÇAMENTO (envio por e-mail via enviar.php)
     ============================================================ */
  document.querySelectorAll('.quote__form').forEach((form) => {
    const status = form.parentElement.querySelector('.form-status') || form.querySelector('.form-status');
    const button = form.querySelector('button[type="submit"]');

    const showStatus = (message, type) => {
      if (!status) return;
      status.textContent = message;
      status.hidden = false;
      status.classList.remove('form-status--ok', 'form-status--erro');
      status.classList.add(type === 'ok' ? 'form-status--ok' : 'form-status--erro');
    };

    form.addEventListener('submit', async (event) => {
      event.preventDefault();

      const nome = form.querySelector('input[name="nome"]').value.trim();
      const telefone = form.querySelector('input[name="telefone"]').value.trim();

      let valido = true;
      [['nome', nome], ['telefone', telefone]].forEach(([name, value]) => {
        const wrapper = form.querySelector(`input[name="${name}"]`).closest('.field__input');
        wrapper.style.borderColor = value ? '' : '#e05656';
        if (!value) valido = false;
      });
      if (!valido) {
        showStatus('Preencha seu nome e telefone para solicitar o orçamento.', 'erro');
        return;
      }

      const originalLabel = button.innerHTML;
      button.disabled = true;
      button.style.opacity = '0.7';
      button.innerHTML = button.innerHTML.replace('Solicitar orçamento', 'Enviando...');

      try {
        const resposta = await fetch(form.action, {
          method: 'POST',
          body: new FormData(form),
        });
        const dados = await resposta.json().catch(() => ({}));

        if (resposta.ok && dados.ok) {
          showStatus('Pedido enviado com sucesso! Retornaremos em até 24 horas úteis.', 'ok');
          form.reset();
        } else {
          showStatus(dados.erro || 'Não foi possível enviar agora. Tente novamente em instantes.', 'erro');
        }
      } catch (erro) {
        showStatus('Não foi possível conectar ao servidor. Verifique sua internet e tente novamente.', 'erro');
      } finally {
        button.disabled = false;
        button.style.opacity = '';
        button.innerHTML = originalLabel;
      }
    });
  });
});
