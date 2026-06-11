import './bootstrap';

const applyTheme = (theme) => {
    document.documentElement.dataset.theme = theme;
    localStorage.setItem('fees-theme', theme);
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        const icon = button.querySelector('i');
        if (icon) {
            icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        } else {
            // Fallback for text-based toggle
            button.textContent = theme === 'dark' ? 'Light' : 'Dark';
        }
    });
};

const savedTheme = localStorage.getItem('fees-theme');
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

applyTheme(savedTheme || (prefersDark ? 'dark' : 'light'));

document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        applyTheme(document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark');
    });
});
