/* ============================================================
   Sultemper — interações da landing page
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
  /* ---------- Navegação ativa conforme a seção visível ---------- */
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

  /* ---------- Carrossel de soluções ---------- */
  const track = document.getElementById('solutionsTrack');
  document.querySelectorAll('.solutions__arrow').forEach((btn) => {
    btn.addEventListener('click', () => {
      const dir = Number(btn.dataset.dir) || 1;
      const card = track.querySelector('.sol-card');
      const step = card ? card.offsetWidth + 16 : 385;
      track.scrollBy({ left: dir * step, behavior: 'smooth' });
    });
  });

  /* ---------- Depoimentos ---------- */
  const testimonials = [
    {
      avatar: 'https://www.figma.com/api/mcp/asset/5e543a59-fb59-4c5b-88b1-29cc90d7b50f',
      name: 'Arlingo Ludwig - Gerente Operacional',
      company: 'Shopping Neumarkt/Blumenau',
      quote:
        '" A SulTemper é nosso fornecedor a mais de dez anos, com soluções técnicas criativas e de qualidade, e com diversidade de serviços. Nos atende com cumprimento de prazos e qualidade de serviços. "',
    },
    // Adicione mais depoimentos aqui:
    // { avatar: 'caminho/da/foto.jpg', name: 'Nome - Cargo', company: 'Empresa/Cidade', quote: '" Depoimento... "' },
  ];

  let current = 0;
  const card = document.getElementById('testimonialCard');
  const avatarEl = document.getElementById('tAvatar');
  const nameEl = document.getElementById('tName');
  const companyEl = document.getElementById('tCompany');
  const quoteEl = document.getElementById('tQuote');

  const renderTestimonial = (index) => {
    const item = testimonials[index];
    if (!item) return;
    card.classList.add('is-fading');
    setTimeout(() => {
      avatarEl.src = item.avatar;
      nameEl.textContent = item.name;
      companyEl.textContent = item.company;
      quoteEl.textContent = item.quote;
      card.classList.remove('is-fading');
    }, 250);
  };

  const stepTestimonial = (dir) => {
    if (testimonials.length < 2) return;
    current = (current + dir + testimonials.length) % testimonials.length;
    renderTestimonial(current);
  };

  document.getElementById('tPrev').addEventListener('click', () => stepTestimonial(-1));
  document.getElementById('tNext').addEventListener('click', () => stepTestimonial(1));

  /* ---------- Header fixo com sombra ao rolar ---------- */
  const header = document.querySelector('.header');
  const onScroll = () => {
    header.classList.toggle('header--scrolled', window.scrollY > 10);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---------- Formulários de orçamento (envio por e-mail via enviar.php) ---------- */
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
