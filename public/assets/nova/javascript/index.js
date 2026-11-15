import { theme } from './theme.js';
import { interactions, createInteractions, showToast } from './components.js';

export { theme, interactions, createInteractions, showToast };

export const Nova = { theme, interactions, createInteractions, showToast };

if (typeof window !== 'undefined') {
  window.Nova = window.Nova ?? Nova;
}
