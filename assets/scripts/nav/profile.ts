const nav = document.querySelector('[data-nav-profile]') as HTMLElement;
const minimizeButton = document.querySelector('[data-nav-profile-minimize]') as HTMLElement;
const overlay = document.querySelector('[data-overlay]') as HTMLElement;

const mediaQuery = window.matchMedia('(max-width: 1024px)') as MediaQueryList;

minimizeButton.addEventListener('click', (): void => {
    toggleMenu();
});

document.addEventListener('click', (e: PointerEvent): void => {
    if (mediaQuery.matches && !nav.contains(e.target as Node) && !minimizeButton.contains(e.target as Node) && !nav.classList.contains('animate-custom-nav-profile-collapse')) {
        toggleMenu();
    }
})

function toggleMenu(): void {
    if (mediaQuery.matches) {
        nav.classList.toggle('animate-custom-nav-profile-collapse');
        nav.classList.toggle('animate-custom-nav-profile-expand');

        overlay.classList.toggle('hidden');
        overlay.classList.toggle('animate-custom-overlay-collapse');
        overlay.classList.toggle('animate-custom-overlay-expand');
    } else {
        nav.classList.toggle('lg:animate-custom-nav-profile-collapse');
        nav.classList.toggle('lg:animate-custom-nav-profile-expand');
    }

    minimizeButton.classList.toggle('bx-sidebar');
    minimizeButton.classList.toggle('bx-sidebar-right');
}
