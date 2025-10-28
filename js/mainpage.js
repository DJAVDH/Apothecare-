document.addEventListener('DOMContentLoaded', () => {
    const productsGrid = document.querySelector('.products-grid');
    if (!productsGrid) {
        return;
    }

    const modalOverlay = document.getElementById('productModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const modalCloseButton = document.getElementById('modalClose');
    const searchInput = document.querySelector('.search-input');

    const productData = Array.from(productsGrid.querySelectorAll('.product-card')).map(card => ({
        element: card,
        name: card.dataset.name || card.querySelector('h3')?.textContent?.trim() || '',
        description: card.dataset.description || card.querySelector('p')?.textContent?.trim() || '',
        price: card.dataset.price || '',
        image: card.dataset.image || card.querySelector('img')?.getAttribute('src') || '',
        detailUrl: card.dataset.detailUrl || ''
    }));

    productData.forEach(product => {
        const detailsButton = product.element.querySelector('.details-button');
        if (detailsButton) {
            detailsButton.addEventListener('click', (event) => {
                event.stopPropagation();
                openModal(product);
            });
        }

        const addToCartButton = product.element.querySelector('.add-to-cart-button');
        if (addToCartButton) {
            addToCartButton.addEventListener('click', (event) => {
                event.stopPropagation();
                // TODO: voeg winkelmand functionaliteit toe
            });
        }

        product.element.addEventListener('click', (event) => {
            if (event.target.closest('.details-button') || event.target.closest('.add-to-cart-button')) {
                return;
            }

            if (product.detailUrl) {
                window.location.href = product.detailUrl;
            }
        });
    });

    // Functie om producten te filteren en weer te geven
    function filterProducts(searchTerm) {
        const searchLower = searchTerm.trim().toLowerCase();

        productData.forEach(product => {
            const matches = !searchLower ||
                product.name.toLowerCase().includes(searchLower) ||
                product.description.toLowerCase().includes(searchLower);

            product.element.style.display = matches ? '' : 'none';
        });
    }

    // Event listener voor de zoekbalk
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            filterProducts(e.target.value);
        });
    }

    // Functie om de product-modal te openen
    function openModal(product) {
        modalTitle.textContent = product.name;
        const description = product.description || 'Geen beschrijving beschikbaar.';
        const priceInfo = product.price ? ` (Prijs: € ${product.price})` : '';
        modalDescription.textContent = `${description}${priceInfo}`;
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
    if (modalCloseButton) {
        modalCloseButton.addEventListener('click', closeModal);
    }
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

        // Send message to backend AI endpoint
        getAiResponse(userText);
    }

    // Functie om een AI-antwoord te genereren
    async function getAiResponse(question) {
        // show a temporary typing indicator
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

            if (!resp.ok) throw new Error('Network response was not ok');
            const data = await resp.json();
            const answer = data.response || 'Sorry, geen antwoord ontvangen.';

            // replace typing indicator with actual answer
            typingDiv.className = 'message ai-message';
            typingDiv.innerHTML = `<p>${answer}</p>`;
            chatMessages.scrollTop = chatMessages.scrollHeight;
        } catch (err) {
            typingDiv.className = 'message ai-message error';
            typingDiv.innerHTML = `<p>Fout bij verbinden met de AI: ${err.message}</p>`;
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