import { theme, createInteractions } from './javascript/index.js';
createInteractions();
document.querySelector('[data-nova-theme-toggle]')?.addEventListener('click', () => {
  theme.setMode(theme.getMode() === 'dark' ? 'light' : 'dark');
});