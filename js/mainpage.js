document.addEventListener('DOMContentLoaded', () => {

    // --------------------------
    // PRODUCT GRID LOGICA
    // --------------------------
    const productsGrid = document.querySelector('.products-grid');
    if (!productsGrid) return;

    const modalOverlay = document.getElementById('productModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const modalCloseButton = document.getElementById('modalClose');
    const searchInput = document.querySelector('.search-input');

    // Alle producten ophalen vanuit HTML
    const productData = Array.from(productsGrid.querySelectorAll('.product-card')).map(card => ({
        element: card,
        name: card.dataset.name || card.querySelector('h3')?.textContent?.trim() || '',
        description: card.dataset.description || card.querySelector('p')?.textContent?.trim() || '',
        price: card.dataset.price || '',
        image: card.dataset.image || card.querySelector('img')?.getAttribute('src') || '',
        detailUrl: card.dataset.detailUrl || ''
    }));

    // Eventlisteners toevoegen aan elk product
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
                // TODO: winkelmand functionaliteit
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

    // Product filter
    function filterProducts(searchTerm) {
        const searchLower = searchTerm.trim().toLowerCase();
        productData.forEach(product => {
            const matches =
                !searchLower ||
                product.name.toLowerCase().includes(searchLower) ||
                product.description.toLowerCase().includes(searchLower);

            product.element.style.display = matches ? '' : 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            filterProducts(e.target.value);
        });
    }

    // Modal open/sluit functies
    function openModal(product) {
        modalTitle.textContent = product.name;
        const description = product.description || 'Geen beschrijving beschikbaar.';
        const priceInfo = product.price ? ` (Prijs: €${product.price})` : '';
        modalDescription.textContent = `${description}${priceInfo}`;
        modalOverlay.style.display = 'flex';
        setTimeout(() => modalOverlay.classList.remove('hidden'), 10);
    }

    function closeModal() {
        modalOverlay.classList.add('hidden');
        setTimeout(() => {
            if (modalOverlay.classList.contains('hidden')) {
                modalOverlay.style.display = 'none';
            }
        }, 300);
    }

    if (modalOverlay.classList.contains('hidden')) {
        modalOverlay.style.display = 'none';
    }

    if (modalCloseButton) modalCloseButton.addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', (event) => {
        if (event.target === modalOverlay) closeModal();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape" && !modalOverlay.classList.contains('hidden')) closeModal();
    });

    // --------------------------
    // CHATBOX LOGICA
    // --------------------------
    const chatButton = document.querySelector('.chat-button');
    const chatIcon = document.querySelector('.chat-icon');
    const aiChatbox = document.getElementById('aiChatbox');
    const closeChatButton = document.getElementById('closeChat');
    const chatMessages = document.getElementById('chatMessages');
    const chatInput = document.getElementById('chatInput');
    const sendChatButton = document.getElementById('sendChat');

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

    function appendMessage(message, className) {
        const div = document.createElement('div');
        div.className = `message ${className}`;
        div.innerHTML = `<p>${message}</p>`;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return div;
    }

    function sendMessage() {
        const userText = chatInput.value.trim();
        if (userText === '') return;

        appendMessage(userText, 'user-message');
        chatInput.value = '';
        chatMessages.scrollTop = chatMessages.scrollHeight;

        getAiResponse(userText);
    }

    async function getAiResponse(question, attempt = 0) {
        const maxRetries = 2;
        const typingDiv = appendMessage('Even geduld, ik denk na...', 'ai-message typing');

        try {
            const resp = await fetch('ai.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ vraag: question })
            });

            if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
            const data = await resp.json();
            const answer = data.response || 'Sorry, geen antwoord ontvangen.';

            typingDiv.className = 'message ai-message';
            typingDiv.innerHTML = `<p>${answer}</p>`;
            chatMessages.scrollTop = chatMessages.scrollHeight;
        } catch (err) {
            if (attempt < maxRetries) {
                typingDiv.remove();
                await new Promise(res => setTimeout(res, 500));
                return getAiResponse(question, attempt + 1);
            }

            typingDiv.className = 'message ai-message error';
            typingDiv.innerHTML = `<p>Fout bij verbinden met de AI: ${err.message}</p>`;
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    chatButton.addEventListener('click', toggleChatbox);
    closeChatButton.addEventListener('click', toggleChatbox);
    sendChatButton.addEventListener('click', sendMessage);
    chatInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
});
