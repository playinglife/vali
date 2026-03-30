import './bootstrap';
import { initCallBookingPickers } from './call-booking-picker';

/**
 * Fade 0→half viewport; at/ past half hide. Resize can change threshold — sticky hide
 * until scroll back into the top band.
 */
function updateMenuFromScroll(menu, scrollEl, state) {
    const threshold = window.innerHeight * 0.5;
    const scrollTop = scrollEl.scrollTop;

    if (scrollTop >= threshold) {
        state.wasPastThreshold = true;
        menu.classList.add('menu--hidden');
        menu.style.removeProperty('opacity');
        return;
    }

    if (state.wasPastThreshold && scrollTop >= threshold * 0.5) {
        menu.classList.add('menu--hidden');
        menu.style.removeProperty('opacity');
        return;
    }

    if (scrollTop < threshold * 0.5) {
        state.wasPastThreshold = false;
    }

    menu.classList.remove('menu--hidden');
    const opacity = Math.max(0, 1 - scrollTop / threshold);
    menu.style.opacity = String(opacity);
}

document.addEventListener('DOMContentLoaded', function () {
    initCallBookingPickers();

    const menu = document.getElementById('main-menu');
    const main = document.getElementById('main-content');

    if (!menu || !main) {
        return;
    }

    const state = { wasPastThreshold: false };

    const onScrollOrResize = () => {
        updateMenuFromScroll(menu, main, state);
    };

    let ticking = false;
    const onScroll = () => {
        if (!ticking) {
            requestAnimationFrame(() => {
                onScrollOrResize();
                ticking = false;
            });
            ticking = true;
        }
    };

    main.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScrollOrResize, { passive: true });
    onScrollOrResize();
});
