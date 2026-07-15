const themeIcon = {
  dark: '<i class="fa-regular fa-moon-stars fa-fw fs-5"></i>',
  light: '<i class="fa-regular fa-sun fa-fw fs-5"></i>'
};

// Determinar tema inicial
let storedTheme = localStorage.getItem('theme');
if (storedTheme !== 'light' && storedTheme !== 'dark') {
  const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  storedTheme = systemPrefersDark ? 'dark' : 'light';
}

document.documentElement.setAttribute('data-bs-theme', storedTheme);

// Exponer funciones globales
window.changeTheme = function(theme) {
  if (theme !== 'light' && theme !== 'dark') {
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    theme = systemPrefersDark ? 'dark' : 'light';
  }
  document.documentElement.setAttribute('data-bs-theme', theme);
  localStorage.setItem('theme', theme);
  const btn = document.querySelector('#btn-theme');
  if (btn) {
    btn.innerHTML = themeIcon[theme];
  }
};

window.toggleTheme = function() {
  const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
  const newTheme = (currentTheme === 'dark') ? 'light' : 'dark';
  window.changeTheme(newTheme);
};

// Inicializar ícono del botón al cargar el DOM
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.querySelector('#btn-theme');
  if (btn) {
    btn.innerHTML = themeIcon[storedTheme];
  }
});
