// Wealth Creators — client-side JavaScript
// Bevat: mobiele navigatie. Later volgt: categoriefilter (overzicht) en
// bod-validatie (showcase).

document.addEventListener('DOMContentLoaded', function () {
    initNavToggle();
    initCategoryFilter();
    initGallery();
    initBidForm();
});

// Toont/verbergt het navigatiemenu op mobiel.
function initNavToggle() {
    const toggle = document.querySelector('.nav-toggle');
    const nav = document.querySelector('.site-nav');
    if (!toggle || !nav) {
        return;
    }

    toggle.addEventListener('click', function () {
        const isOpen = nav.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
}

// Filtert de producten op het overzicht direct in de browser, zonder herladen.
function initCategoryFilter() {
    const filterBar = document.getElementById('filterBar');
    const grid = document.getElementById('productGrid');
    if (!filterBar || !grid) {
        return;
    }

    const buttons = filterBar.querySelectorAll('button[data-filter]');
    const cards = grid.querySelectorAll('[data-categorie]');
    const noResults = document.getElementById('noResults');

    // Past het gekozen filter toe: toont alleen kaarten van de juiste categorie.
    function applyFilter(filter) {
        let visibleCount = 0;
        cards.forEach(function (card) {
            const match = filter === 'all' || card.getAttribute('data-categorie') === filter;
            card.style.display = match ? '' : 'none';
            if (match) {
                visibleCount++;
            }
        });

        // Toon een melding wanneer er geen resultaten zijn.
        if (noResults) {
            noResults.style.display = visibleCount === 0 ? '' : 'none';
        }
    }

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            buttons.forEach(function (b) {
                b.classList.remove('is-active');
            });
            button.classList.add('is-active');
            applyFilter(button.getAttribute('data-filter'));
        });
    });

    // Pas bij het laden het filter toe dat de server als actief heeft gemarkeerd
    // (bijvoorbeeld via ?categorie=ID vanaf de homepage).
    const initial = filterBar.querySelector('button.is-active');
    if (initial) {
        applyFilter(initial.getAttribute('data-filter'));
    }
}

// Wisselt de hoofdafbeelding van de galerij op de showcasepagina.
function initGallery() {
    const main = document.getElementById('galleryMain');
    const thumbs = document.getElementById('galleryThumbs');
    if (!main || !thumbs) {
        return;
    }

    thumbs.querySelectorAll('img').forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            main.src = thumb.src;
            thumbs.querySelectorAll('img').forEach(function (t) {
                t.classList.remove('is-active');
            });
            thumb.classList.add('is-active');
        });
    });
}

// Valideert het biedformulier vóór verzenden. De server (verwerk_bod.php)
// valideert dezelfde regels nogmaals — client-side validatie alleen is niet genoeg.
function initBidForm() {
    const form = document.getElementById('bidForm');
    if (!form) {
        return;
    }

    const minimaalBod = parseFloat(form.getAttribute('data-minimaal-bod')) || 0;

    // Toont of wist een foutmelding bij een veld.
    function setError(field, message) {
        const input = form.elements[field];
        const box = form.querySelector('[data-error-for="' + field + '"]');
        if (box) {
            box.textContent = message;
        }
        if (input) {
            input.classList.toggle('is-invalid', message !== '');
        }
        return message === '';
    }

    form.addEventListener('submit', function (event) {
        let valid = true;

        // Naam: verplicht, 2 t/m 100 tekens.
        const naam = form.elements['naam'].value.trim();
        if (naam.length < 2 || naam.length > 100) {
            valid = setError('naam', 'Vul een naam in van 2 tot 100 tekens.') && valid;
        } else {
            setError('naam', '');
        }

        // E-mail: verplicht en geldig formaat.
        const email = form.elements['email'].value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            valid = setError('email', 'Vul een geldig e-mailadres in.') && valid;
        } else {
            setError('email', '');
        }

        // Bod: verplicht, numeriek en >= minimaal bod.
        const bod = parseFloat(form.elements['bod_bedrag'].value);
        if (isNaN(bod)) {
            valid = setError('bod_bedrag', 'Vul een bedrag in.') && valid;
        } else if (bod < minimaalBod) {
            valid = setError('bod_bedrag',
                'Uw bod moet minimaal € ' + minimaalBod.toLocaleString('nl-NL') + ' zijn.') && valid;
        } else {
            setError('bod_bedrag', '');
        }

        // Bericht: verplicht, minimaal 10 tekens.
        const bericht = form.elements['bericht'].value.trim();
        if (bericht.length < 10) {
            valid = setError('bericht', 'Uw bericht moet minimaal 10 tekens bevatten.') && valid;
        } else {
            setError('bericht', '');
        }

        // Stop het verzenden als er fouten zijn.
        if (!valid) {
            event.preventDefault();
        }
    });
}
