// Animation simple pour le CTA
const cta = document.querySelector('.cta');
if (cta) {
    cta.addEventListener('mouseenter', () => {
        cta.style.transform = 'scale(1.05)';
    });
    cta.addEventListener('mouseleave', () => {
        cta.style.transform = 'scale(1)';
    });
}
// Animation d'apparition des features
window.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.feature').forEach((el, i) => {
    el.style.opacity = 0;
    setTimeout(() => {
      el.style.transition = 'opacity 0.8s';
      el.style.opacity = 1;
    }, 400 + i * 200);
  });
});
// Ajout dynamique d'une section conseils
const main = document.querySelector('main');
if(main) {
  const conseils = document.createElement('section');
  conseils.className = 'conseils';
  conseils.innerHTML = `<h2><i class="fas fa-heartbeat"></i> Conseils santé</h2><ul><li>Arrivez 10 min avant votre rendez-vous.</li><li>Apportez vos anciens examens si possible.</li><li>Pour toute question, contactez notre équipe !</li></ul>`;
  main.appendChild(conseils);
}