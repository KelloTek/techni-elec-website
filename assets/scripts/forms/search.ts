import AirDatepicker from 'air-datepicker';
import 'air-datepicker/air-datepicker.css';
import localeFr from 'air-datepicker/locale/fr';

const searchForms = document.querySelectorAll('[data-search-form]') as NodeListOf<HTMLFormElement>;

searchForms.forEach((form: HTMLFormElement) => {
    const submitButton = {
        content: 'Appliquer',
        onClick: () => {
            form.submit();
        }
    };

    new AirDatepicker('[data-search-date-input]', {
        locale: localeFr,
        dateFormat: 'dd-MM-yyyy',
        buttons: [submitButton, 'clear'],
    });

    form.addEventListener('reset', (e: Event) => {
        window.location.href = window.location.origin + window.location.pathname;
    });

    const searchInput = form.querySelector('[data-search-input]') as HTMLInputElement;

    searchInput.addEventListener('change', () => {
        form.submit();
    });
});
