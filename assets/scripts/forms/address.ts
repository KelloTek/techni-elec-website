const addressContainer = document.querySelectorAll('[data-form-address]') as NodeListOf<HTMLDivElement>;

addressContainer.forEach((address: HTMLDivElement) => {
    const lineInput = address.querySelector('[data-address-line-input]') as HTMLInputElement;
    const cityInput = address.querySelector('[data-address-city-input]') as HTMLInputElement;
    const postalInput = address.querySelector('[data-address-zip-code-input]') as HTMLInputElement;

    const resultBox = document.createElement('div') as HTMLDivElement;
    resultBox.className = 'form-result-address-container scrollbar-hide';
    resultBox.style.width = `${lineInput.offsetWidth}px`;
    // @ts-ignore
    lineInput.parentNode.appendChild(resultBox);

    let timeout: number;

    lineInput.addEventListener('input', () => {
        clearTimeout(timeout);
        const query = lineInput.value.trim() as string;
        if (query.length < 3) {
            resultBox.innerHTML = '';
            return;
        }

        // @ts-ignore
        timeout = setTimeout(() => {
            fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=9`)
                .then(response => response.json())
                .then((data: any) => {
                    resultBox.innerHTML = '';
                    data.features.forEach((feature: any) => {
                        const props = feature.properties as any;
                        const item = document.createElement('div') as HTMLDivElement;
                        item.textContent = props.label;
                        item.className = 'form-result-address-item';

                        item.addEventListener('click', () => {
                            lineInput.value = props.name;
                            cityInput.value = props.city;
                            postalInput.value = props.postcode;
                            resultBox.innerHTML = '';
                        });

                        resultBox.appendChild(item);
                    });
                });
        }, 300);
    });

    document.addEventListener('click', (e: Event) => {
        if (!resultBox.contains(e.target as Node) && e.target !== lineInput) {
            resultBox.innerHTML = '';
        }
    });
});
