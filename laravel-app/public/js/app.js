document.addEventListener('DOMContentLoaded', () => {
    const flash = document.querySelector('.flash-message');
    if (flash) {
        const hide = () => {
            flash.classList.add('flash-hidden');
            setTimeout(() => flash.remove(), 400);
        };
        setTimeout(hide, 3500);
        flash.addEventListener('click', hide);
    }

    const modal = document.querySelector('[data-chart-modal]');
    const modalImg = modal ? modal.querySelector('img') : null;

    document.querySelectorAll('[data-chart-thumb]').forEach(img => {
        img.addEventListener('click', () => {
            if (!modal || !modalImg) return;
            modalImg.src = img.dataset.full || img.src;
            modal.classList.add('active');
        });
    });

    if (modal) {
        modal.addEventListener('click', () => modal.classList.remove('active'));
    }
});
