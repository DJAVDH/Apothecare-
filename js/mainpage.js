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

    // Selecteer de DOM-elementen voor producten
    const productsGrid = document.querySelector('.products-grid');
    const modalOverlay = document.getElementById('productModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const modalCloseButton = document.getElementById('modalClose');
    const searchInput = document.querySelector('.search-input');

    // Functie om producten te filteren en weer te geven
    function filterProducts(searchTerm) {
        const filteredProducts = products.filter(product => {
            const searchLower = searchTerm.toLowerCase();
            return product.name.toLowerCase().includes(searchLower) ||
                   product.description.toLowerCase().includes(searchLower);
        });
        
        // Maak de products grid leeg
        productsGrid.innerHTML = '';
        
        // Voeg de gefilterde producten toe
        filteredProducts.forEach(product => {
            const card = createProductCard(product);
            productsGrid.appendChild(card);
        });
    }

    // Functie om een product kaart te maken
    function createProductCard(product) {
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

        card.querySelector('.details-button').addEventListener('click', () => {
            openModal(product);
        });

        return card;
    }

    // Event listener voor de zoekbalk
    searchInput.addEventListener('input', (e) => {
        filterProducts(e.target.value);
    });

    // Initiële weergave van alle producten
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

        card.querySelector('.details-button').addEventListener('click', () => {
            openModal(product);
        });

        productsGrid.appendChild(card);
    });

    // Functie om de product-modal te openen
    function openModal(product) {
        modalTitle.textContent = product.name;
        modalDescription.textContent = product.description;
        modalOverlay.style.display = 'flex'; 
        setTimeout(() => modalOverlay.classList.remove('hidden'), 10);
    }

    // Functie om de product-modal te sluiten
    function closeModal() {
        modalOverlay.classList.add('hidden');
        setTimeout(() => {
            if (modalOverlay.classList.contains('hidden')) {
                modalOverlay.style.display = 'none';
            }
        }, 300); 
    }
    
    if(modalOverlay.classList.contains('hidden')){
        modalOverlay.style.display = 'none';
    }

    // Event listeners om de product-modal te sluiten
    modalCloseButton.addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', (event) => {
        if (event.target === modalOverlay) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape" && !modalOverlay.classList.contains('hidden')) {
            closeModal();
        }
    });

    // --- AI CHATBOX LOGICA ---

    // Selecteer de DOM-elementen voor de chatbox
    const chatButton = document.querySelector('.chat-button');
    const chatIcon = document.querySelector('.chat-icon');
    const aiChatbox = document.getElementById('aiChatbox');
    const closeChatButton = document.getElementById('closeChat');
    const chatMessages = document.getElementById('chatMessages');
    const chatInput = document.getElementById('chatInput');
    const sendChatButton = document.getElementById('sendChat');

    // Functie om de chatbox te openen/sluiten
    function toggleChatbox() {
        aiChatbox.classList.toggle('hidden');
        chatButton.classList.toggle('open');

        if (chatButton.classList.contains('open')) {
            chatIcon.textContent = '×';
            chatIcon.style.paddingBottom = '0px'; 
        } else {
            chatIcon.textContent = '+';
            chatIcon.style.paddingBottom = '2px';
        }
    }

    // Functie om een bericht te versturen
    function sendMessage() {
        const userText = chatInput.value.trim();
        if (userText === '') return;

        const userMessageDiv = document.createElement('div');
        userMessageDiv.className = 'message user-message';
        userMessageDiv.innerHTML = `<p>${userText}</p>`;
        chatMessages.appendChild(userMessageDiv);

        chatInput.value = '';
        chatMessages.scrollTop = chatMessages.scrollHeight;

        setTimeout(getAiResponse, 1000);
    }

    // Functie om een AI-antwoord te genereren
    function getAiResponse() {
        const aiMessageDiv = document.createElement('div');
        aiMessageDiv.className = 'message ai-message';
        aiMessageDiv.innerHTML = `<p>Bedankt voor uw vraag. Ik ben een demo-assistent. Hoe kan ik u verder helpen met informatie over onze producten?</p>`;
        chatMessages.appendChild(aiMessageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Event listeners voor de chatbox
    chatButton.addEventListener('click', toggleChatbox);
    closeChatButton.addEventListener('click', toggleChatbox);
    sendChatButton.addEventListener('click', sendMessage);
    chatInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

});



document.addEventListener('DOMContentLoaded', () => {
    
  
    // Selecteer de DOM-elementen voor producten
    const productsGrid = document.querySelector('.products-grid');
    const modalOverlay = document.getElementById('productModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const modalCloseButton = document.getElementById('modalClose');
    const searchInput = document.querySelector('.search-input');

    // Functie om producten te filteren en weer te geven
    function filterProducts(searchTerm) {
        const filteredProducts = products.filter(product => {
            const searchLower = searchTerm.toLowerCase();
            return product.name.toLowerCase().includes(searchLower) ||
                   product.description.toLowerCase().includes(searchLower);
        });
        
        // Maak de products grid leeg
        productsGrid.innerHTML = '';
        
        // Voeg de gefilterde producten toe
        filteredProducts.forEach(product => {
            const card = createProductCard(product);
            productsGrid.appendChild(card);
        });
    }

    // Functie om een product kaart te maken
    function createProductCard(product) {
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

        card.querySelector('.details-button').addEventListener('click', () => {
            openModal(product);
        });

        return card;
    }

    // Event listener voor de zoekbalk
    searchInput.addEventListener('input', (e) => {
        filterProducts(e.target.value);
    });

    // Initiële weergave van alle producten
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

        card.querySelector('.details-button').addEventListener('click', () => {
            openModal(product);
        });

        productsGrid.appendChild(card);
    });

    // Functie om de product-modal te openen
    function openModal(product) {
        modalTitle.textContent = product.name;
        modalDescription.textContent = product.description;
        modalOverlay.style.display = 'flex'; 
        setTimeout(() => modalOverlay.classList.remove('hidden'), 10);
    }

    // Functie om de product-modal te sluiten
    function closeModal() {
        modalOverlay.classList.add('hidden');
        setTimeout(() => {
            if (modalOverlay.classList.contains('hidden')) {
                modalOverlay.style.display = 'none';
            }
        }, 300); 
    }
    
    if(modalOverlay.classList.contains('hidden')){
        modalOverlay.style.display = 'none';
    }

    // Event listeners om de product-modal te sluiten
    modalCloseButton.addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', (event) => {
        if (event.target === modalOverlay) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape" && !modalOverlay.classList.contains('hidden')) {
            closeModal();
        }
    });

    // --- AI CHATBOX LOGICA ---

    // Selecteer de DOM-elementen voor de chatbox
    const chatButton = document.querySelector('.chat-button');
    const chatIcon = document.querySelector('.chat-icon');
    const aiChatbox = document.getElementById('aiChatbox');
    const closeChatButton = document.getElementById('closeChat');
    const chatMessages = document.getElementById('chatMessages');
    const chatInput = document.getElementById('chatInput');
    const sendChatButton = document.getElementById('sendChat');

    // Functie om de chatbox te openen/sluiten
    function toggleChatbox() {
        aiChatbox.classList.toggle('hidden');
        chatButton.classList.toggle('open');

        if (chatButton.classList.contains('open')) {
            chatIcon.textContent = '×';
            chatIcon.style.paddingBottom = '0px'; 
        } else {
            chatIcon.textContent = '+';
            chatIcon.style.paddingBottom = '2px';
        }
    }

    // Functie om een bericht te versturen
    function sendMessage() {
        const userText = chatInput.value.trim();
        if (userText === '') return;

        const userMessageDiv = document.createElement('div');
        userMessageDiv.className = 'message user-message';
        userMessageDiv.innerHTML = `<p>${userText}</p>`;
        chatMessages.appendChild(userMessageDiv);

        chatInput.value = '';
        chatMessages.scrollTop = chatMessages.scrollHeight;

        setTimeout(getAiResponse, 1000);
    }

    // Functie om een AI-antwoord te genereren
    async function getAiResponse(question, attempt = 0) {
        const maxRetries = 2;

        // show a temporary typing indicator (reuse per call so retries don't duplicate)
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message ai-message typing';
        typingDiv.innerHTML = `<p>Even geduld, ik denk na...</p>`;
        chatMessages.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        try {
            const resp = await fetch('ai.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ vraag: question })
            });

            if (!resp.ok) throw new Error('Network response was not ok (' + resp.status + ')');
            const data = await resp.json();
            const answer = data.response || 'Sorry, geen antwoord ontvangen.';

            // replace typing indicator with actual answer
            typingDiv.className = 'message ai-message';
            typingDiv.innerHTML = `<p>${answer}</p>`;
            chatMessages.scrollTop = chatMessages.scrollHeight;
        } catch (err) {
            // retry for transient errors
            if (attempt < maxRetries) {
                typingDiv.innerHTML = `<p>Verbinden mislukt. Probeer opnieuw... (${attempt + 1})</p>`;
                // wait a bit and retry (increasing delay)
                await new Promise(res => setTimeout(res, 400 * (attempt + 1)));
                // remove indicator before retry to avoid duplicates
                typingDiv.remove();
                return getAiResponse(question, attempt + 1);
            }

            typingDiv.className = 'message ai-message error';
            typingDiv.innerHTML = `<p>Fout bij verbinden met de AI: ${err.message}. Probeer het later opnieuw.</p>`;
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    // Event listeners voor de chatbox
    chatButton.addEventListener('click', toggleChatbox);
    closeChatButton.addEventListener('click', toggleChatbox);
    sendChatButton.addEventListener('click', sendMessage);
    chatInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

});