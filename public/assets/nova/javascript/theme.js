const THEMES = new Set(['white', 'blue', 'maroon-light', 'red', 'indigo', 'orange', 'green']);
const MODES = new Set(['light', 'dark', 'system']);

function rootElement() {
  return typeof document !== 'undefined' ? document.documentElement : null;
}

function resolvedMode(mode) {
  if (mode !== 'system') return mode;
  return typeof window !== 'undefined' && window.matchMedia?.('(prefers-color-scheme: dark)').matches
    ? 'dark'
    : 'light';
}

export const theme = {
  names: [...THEMES],
  modes: [...MODES],
  set(name) {
    if (!THEMES.has(name)) throw new TypeError(`Unknown Nova theme: ${name}`);
    const root = rootElement();
    if (root) root.dataset.novaTheme = name;
  },
  get() { return rootElement()?.dataset.novaTheme ?? null; },
  setMode(mode) {
    if (!MODES.has(mode)) throw new TypeError(`Unknown Nova mode: ${mode}`);
    const root = rootElement();
    if (!root) return;
    if (mode === 'system') {
      root.dataset.novaMode = resolvedMode('system');
      root.dataset.novaModePreference = 'system';
    } else {
      root.dataset.novaMode = mode;
      delete root.dataset.novaModePreference;
    }
  },
  getMode() { return rootElement()?.dataset.novaMode ?? 'light'; },
  getModePreference() { return rootElement()?.dataset.novaModePreference ?? this.getMode(); },
};
