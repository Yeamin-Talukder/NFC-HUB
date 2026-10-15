document.addEventListener('DOMContentLoaded', () => {
    // Toast helper
    let toastEl;
    window.showToast = (message, icon = 'fa-check') => {
        if (!toastEl) {
            toastEl = document.createElement('div');
            toastEl.className = 'toast';
            toastEl.setAttribute('role', 'status');
            document.body.appendChild(toastEl);
        }
        toastEl.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
        toastEl.classList.add('show');
        clearTimeout(toastEl._t);
        toastEl._t = setTimeout(() => toastEl.classList.remove('show'), 2000);
    };

    // Copy to clipboard
    document.querySelectorAll('[data-copy]').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = document.getElementById(btn.dataset.copy);
            if (!target) return;
            const value = target.value ?? target.textContent;
            const done = () => window.showToast('Link copied');
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(value).then(done);
            } else {
                target.select();
                document.execCommand('copy');
                done();
            }
        });
    });

    // Show / hide password
    document.querySelectorAll('[data-toggle-password]').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.togglePassword);
            const icon = btn.querySelector('i');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !show);
            icon.classList.toggle('fa-eye-slash', show);
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    });

    // Tabs (dashboard on small screens)
    document.querySelectorAll('[data-tab]').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('[data-tab]').forEach(t => t.classList.toggle('active', t === tab));
            document.querySelectorAll('[data-panel]').forEach(p => p.classList.toggle('active', p.dataset.panel === tab.dataset.tab));
        });
    });
});
