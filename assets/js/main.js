document.addEventListener('DOMContentLoaded', () => {
    // Copy to Clipboard functionality
    const copyBtns = document.querySelectorAll('.copy-btn');
    
    copyBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            
            if (targetInput) {
                targetInput.select();
                targetInput.setSelectionRange(0, 99999); // For mobile devices
                
                try {
                    navigator.clipboard.writeText(targetInput.value).then(() => {
                        const originalText = btn.innerText;
                        btn.innerText = 'Copied!';
                        btn.style.background = '#00ff88';
                        btn.style.color = '#000';
                        
                        setTimeout(() => {
                            btn.innerText = originalText;
                            btn.style.background = '';
                            btn.style.color = '';
                        }, 2000);
                    });
                } catch (err) {
                    console.error('Failed to copy text: ', err);
                }
            }
        });
    });

    // Add 3D tilt effect to cards if we want to be extra fancy
    const cards = document.querySelectorAll('.glass');
    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            card.style.setProperty('--x', `${x}px`);
            card.style.setProperty('--y', `${y}px`);
        });
    });
});
