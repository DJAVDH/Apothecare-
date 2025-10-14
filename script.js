document.addEventListener('DOMContentLoaded', () => {

    // Array met productinformatie
    const products = [
        {
            name: "Paracetamol 500mg",
            image: "https://placehold.co/400x300/a7f3d0/333?text=Paracetamol",
            description: "Effectief bij koorts en pijn bij griep en verkoudheid, hoofdpijn, kiespijn, zenuwpijn, spierpijn en menstruatiepijn. Werkt pijnstillend en koortsverlagend."
        },
        {
            name: "Ibuprofen 400mg",
            image: "https://placehold.co/400x300/a7f3d0/333?text=Ibuprofen",
            description: "Een ontstekingsremmende pijnstiller (NSAID). Het werkt pijnstillend, ontstekingsremmend en koortsverlagend. Te gebruiken bij diverse soorten pijn."
        },
        {
            name: "Xylometazoline Neusspray",
            image: "https://placehold.co/400x300/a7f3d0/333?text=Neusspray",
            description: "Voor de behandeling van een verstopte neus. Het vermindert de zwelling van het neusslijmvlies, waardoor u vrijer kunt ademen."
        },
        {
            name: "Broomhexine Hoestsiroop",
            image: "https://placehold.co/400x300/a7f3d0/333?text=Hoestsiroop",
            description: "Maakt vastzittend slijm in de luchtwegen dunner, waardoor het ophoesten makkelijker wordt. Voor gebruik bij vastzittende hoest."
        },
        {
            name: "Waterproof Pleisters",
            image: "https://placehold.co/400x300/a7f3d0/333?text=Pleisters",
            description: "Een assortiment van waterbestendige pleisters om kleine wondjes te beschermen. Ze zijn flexibel en laten de huid ademen."
        },
        {
            name: "Vitamine C Bruistabletten",
            image: "https://placehold.co/400x300/a7f3d0/333?text=Vitamine+C",
            description: "Ondersteunt het immuunsysteem en helpt bij vermoeidheid. Eén bruistablet per dag is voldoende voor de dagelijkse behoefte."
        },
        {
            name: "Zonnebrandcrème SPF 30",
            image: "https://placehold.co/400x300/a7f3d0/333?text=Zonnebrand",
            description: "Biedt een hoge bescherming tegen schadelijke UVA- en UVB-stralen. Hydraterend en waterresistent. Geschikt voor de gevoelige huid."
        },
        {
            name: "Desinfecterende Handgel",
            image: "https://placehold.co/400x300/a7f3d0/333?text=Handgel",
            description: "Reinigt de handen zonder water en zeep. Doodt 99,9% van de bacteriën. Ideaal voor onderweg, op het werk of op reis."
        }
    ];

    // Selecteer de DOM-elementen
    const productsGrid = document.querySelector('.products-grid');
    const modalOverlay = document.getElementById('productModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const modalCloseButton = document.getElementById('modalClose');

    // Genereer de product kaarten op de pagina
    products.forEach(product => {
        const card = document.createElement('div');
        card.className = 'product-card';
        card.innerHTML = `
            <img src="${product.image}" alt="${product.name}">
            <div class="product-card-content">
                <h3>${product.name}</h3>
                <p>${product.description.substring(0, 100)}...</p>
                <button class="details-button">Bekijk details</button>
            </div>
        `;

        // Voeg een event listener toe aan de knop
        card.querySelector('.details-button').addEventListener('click', () => {
            openModal(product);
        });

        // Voeg de kaart toe aan het grid
        productsGrid.appendChild(card);
    });

    // Functie om de modal te openen
    function openModal(product) {
        modalTitle.textContent = product.name;
        modalDescription.textContent = product.description;
        modalOverlay.classList.remove('hidden');
        // Gebruik de classList van de overlay voor de transitie
        modalOverlay.style.display = 'flex'; 
        setTimeout(() => modalOverlay.classList.remove('hidden'), 10);
    }

    // Functie om de modal te sluiten
    function closeModal() {
        modalOverlay.classList.add('hidden');
    }

    // Event listeners om de modal te sluiten
    modalCloseButton.addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', (event) => {
        // Sluit alleen als er op de overlay zelf wordt geklikt, niet op de content
        if (event.target === modalOverlay) {
            closeModal();
        }
    });

    // Sluit de modal met de Escape-toets
    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape" && !modalOverlay.classList.contains('hidden')) {
            closeModal();
        }
    });

});