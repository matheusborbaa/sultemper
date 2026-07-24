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

  /* ---------- Formulários de orçamento ---------- */
  document.querySelectorAll('.quote__form').forEach((form) => {
    form.addEventListener('submit', (event) => {
      event.preventDefault();

      const nome = form.querySelector('input[name="nome"]').value.trim();
      const telefone = form.querySelector('input[name="telefone"]').value.trim();
      const endereco = form.querySelector('input[name="endereco"]').value.trim();

      if (!nome || !telefone) {
        form.querySelectorAll('input').forEach((input) => {
          const wrapper = input.closest('.field__input');
          wrapper.style.borderColor = input.value.trim() ? '' : '#e05656';
        });
        return;
      }

      /* Envio via WhatsApp — troque o número abaixo pelo da Sultemper */
      const numeroWhatsApp = '5547999999999';
      const mensagem = encodeURIComponent(
        `Olá! Gostaria de solicitar um orçamento.\n\nNome: ${nome}\nTelefone: ${telefone}\nEndereço de instalação: ${endereco || 'não informado'}`
      );
      window.open(`https://wa.me/${numeroWhatsApp}?text=${mensagem}`, '_blank');
      form.reset();
    });
  });
});
