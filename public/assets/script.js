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