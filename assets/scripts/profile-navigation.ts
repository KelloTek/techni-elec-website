const navbar = document.querySelector('[data-profile-navigation]') as HTMLElement;
const button = document.querySelector('[data-profile-navigation-showing-button]') as HTMLElement;

if (navbar) {
    button.addEventListener('click', () => {
        navbar.classList.toggle('animate-custom-navigation-profile-close');
        navbar.classList.toggle('animate-custom-navigation-profile-open');

        button.classList.toggle('bx-chevron-left');
        button.classList.toggle('bx-chevron-right');
    });
}
