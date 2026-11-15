const SELECTORS = {
  modalOpen: '[data-nova-modal-open]', modalClose: '[data-nova-modal-close]',
  dropdown: '[data-nova-dropdown]', dropdownToggle: '[data-nova-dropdown-toggle]',
  tab: '[data-nova-tab]', accordion: '[data-nova-accordion]',
  drawerOpen: '[data-nova-drawer-open]', drawerClose: '[data-nova-drawer-close]',
  toast: '[data-nova-toast]',
  header: '[data-nova-header]',
  headerToggle: '[data-nova-header-toggle]',
  shell: '[data-nova-shell]',
  shellToggle: '[data-nova-shell-toggle]',
  shellBackdrop: '[data-nova-shell-backdrop]',
};

const query = (root, selector) => root?.querySelector?.(selector) ?? null;
const closest = (node, selector) => node?.closest?.(selector) ?? null;

function setExpanded(button, expanded) {
  button?.setAttribute('aria-expanded', String(expanded));
}

function closeDropdown(dropdown) {
  if (!dropdown) return;
  dropdown.classList.remove('is-open');
  setExpanded(query(dropdown, SELECTORS.dropdownToggle), false);
}

function closeDrawer(drawer) {
  if (!drawer) return;
  drawer.classList.remove('is-open');
  drawer.setAttribute('aria-hidden', 'true');
  const opener = drawer._novaOpener ?? document.querySelector(`[data-nova-drawer-open="${CSS.escape(drawer.id)}"]`);
  setExpanded(opener, false);
  drawer.removeAttribute('aria-modal');
  opener?.focus?.();
  drawer._novaOpener = null;
}

function setHeaderOpen(header, open) {
  if (!header) return;
  header.classList.toggle('is-open', open);
  const toggle = query(header, SELECTORS.headerToggle);
  setExpanded(toggle, open);
  const panelId = toggle?.getAttribute('aria-controls');
  const panel = panelId ? query(header, `#${CSS.escape(panelId)}`) : query(header, '.nova-header-panel');
  if (open) focusFirst(panel ?? header);
  else toggle?.focus?.();
}

function closeHeader(header) {
  setHeaderOpen(header, false);
}

function setShellNavOpen(shell, open) {
  if (!shell) return;
  shell.classList.toggle('is-nav-open', open);
  const toggle = query(shell, SELECTORS.shellToggle);
  setExpanded(toggle, open);
  const sidebar = query(shell, '.nova-sidebar');
  if (sidebar) {
    sidebar.setAttribute('aria-hidden', String(!open));
    if (open) sidebar.setAttribute('aria-modal', 'true');
    else sidebar.removeAttribute('aria-modal');
  }
  const backdrop = query(shell, SELECTORS.shellBackdrop);
  if (backdrop) backdrop.hidden = !open;
  if (open) focusFirst(sidebar ?? shell);
  else toggle?.focus?.();
}

function closeShell(shell) {
  setShellNavOpen(shell, false);
}

function focusFirst(container) {
  query(container, 'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])')?.focus();
}

export function createInteractions(root = typeof document !== 'undefined' ? document : null) {
  if (!root?.addEventListener) return { destroy() {} };

  const onClick = (event) => {
    const target = event.target;

    const modalOpener = closest(target, SELECTORS.modalOpen);
    if (modalOpener) {
      const dialog = query(root, `#${CSS.escape(modalOpener.dataset.novaModalOpen)}`);
      if (dialog?.showModal) dialog.showModal();
      else if (dialog) dialog.setAttribute('open', '');
      return;
    }
    const modalCloser = closest(target, SELECTORS.modalClose);
    if (modalCloser) {
      const dialog = closest(modalCloser, 'dialog');
      if (dialog?.close) dialog.close();
      else dialog?.removeAttribute('open');
      return;
    }

    const dropdownToggle = closest(target, SELECTORS.dropdownToggle);
    if (dropdownToggle) {
      const dropdown = closest(dropdownToggle, SELECTORS.dropdown);
      const opening = !dropdown?.classList.contains('is-open');
      root.querySelectorAll(SELECTORS.dropdown).forEach(item => closeDropdown(item));
      if (opening && dropdown) {
        dropdown.classList.add('is-open');
        setExpanded(dropdownToggle, true);
      }
      return;
    }

    const tab = closest(target, SELECTORS.tab);
    if (tab) {
      const list = closest(tab, '[role="tablist"]');
      const targetId = tab.getAttribute('aria-controls');
      if (!list || !targetId) return;
      const scope = list.parentElement;
      list.querySelectorAll(SELECTORS.tab).forEach(item => {
        const selected = item === tab;
        item.setAttribute('aria-selected', String(selected));
        item.tabIndex = selected ? 0 : -1;
      });
      scope.querySelectorAll('[role="tabpanel"]').forEach(panel => {
        panel.hidden = panel.id !== targetId;
      });
      return;
    }

    const accordion = closest(target, SELECTORS.accordion);
    if (accordion) {
      const panelId = accordion.getAttribute('aria-controls');
      const panel = panelId ? query(root, `#${CSS.escape(panelId)}`) : null;
      const expanded = accordion.getAttribute('aria-expanded') === 'true';
      setExpanded(accordion, !expanded);
      if (panel) panel.hidden = expanded;
      return;
    }

    const drawerOpener = closest(target, SELECTORS.drawerOpen);
    if (drawerOpener) {
      const drawer = query(root, `#${CSS.escape(drawerOpener.dataset.novaDrawerOpen)}`);
      if (drawer) {
        drawer._novaOpener = drawerOpener;
        drawer.classList.add('is-open');
        drawer.setAttribute('aria-hidden', 'false');
        drawer.setAttribute('aria-modal', 'true');
        setExpanded(drawerOpener, true);
        focusFirst(drawer);
      }
      return;
    }
    const drawerCloser = closest(target, SELECTORS.drawerClose);
    if (drawerCloser) {
      closeDrawer(closest(drawerCloser, '[data-nova-drawer]'));
      return;
    }

    const headerToggle = closest(target, SELECTORS.headerToggle);
    if (headerToggle) {
      const header = closest(headerToggle, SELECTORS.header);
      setHeaderOpen(header, !header?.classList.contains('is-open'));
      return;
    }

    const shellToggle = closest(target, SELECTORS.shellToggle);
    if (shellToggle) {
      const shell = closest(shellToggle, SELECTORS.shell);
      setShellNavOpen(shell, !shell?.classList.contains('is-nav-open'));
      return;
    }
    const shellBackdrop = closest(target, SELECTORS.shellBackdrop);
    if (shellBackdrop) {
      closeShell(closest(shellBackdrop, SELECTORS.shell));
      return;
    }

    const toastTrigger = closest(target, SELECTORS.toast);
    if (toastTrigger) showToast(toastTrigger.dataset.novaToast, { root, variant: toastTrigger.dataset.novaToastVariant ?? 'info' });
  };

  const onKeydown = (event) => {
    const activeDrawer = root.querySelector('[data-nova-drawer].is-open');
    if (event.key === 'Tab' && activeDrawer) {
      const focusable = [...activeDrawer.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])')]
        .filter(item => !item.hidden && item.getAttribute('aria-hidden') !== 'true');
      if (focusable.length) {
      const first = focusable[0];
      const last = focusable[focusable.length - 1];
      if (event.shiftKey && (document.activeElement === first || !activeDrawer.contains(document.activeElement))) {
        event.preventDefault(); last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault(); first.focus();
      }
      }
    }
    if (event.key.startsWith('Arrow')) {
      const tab = closest(event.target, SELECTORS.tab);
      const list = tab && closest(tab, '[role="tablist"]');
      if (list) {
        const tabs = [...list.querySelectorAll(SELECTORS.tab)].filter(item => !item.disabled);
        const index = tabs.indexOf(tab);
        const next = event.key === 'ArrowRight' || event.key === 'ArrowDown' ? 1 : -1;
        if (index >= 0 && (event.key === 'ArrowRight' || event.key === 'ArrowLeft' || event.key === 'ArrowDown' || event.key === 'ArrowUp')) {
          event.preventDefault();
          tabs[(index + next + tabs.length) % tabs.length]?.click();
          tabs[(index + next + tabs.length) % tabs.length]?.focus();
        }
      }
    }
    if (event.key !== 'Escape') return;
    root.querySelectorAll(SELECTORS.dropdown).forEach(closeDropdown);
    root.querySelectorAll('[data-nova-drawer].is-open').forEach(closeDrawer);
    root.querySelectorAll(`${SELECTORS.header}.is-open`).forEach(closeHeader);
    root.querySelectorAll(`${SELECTORS.shell}.is-nav-open`).forEach(closeShell);
    root.querySelectorAll('dialog[open]').forEach(dialog => dialog.close?.());
  };

  const onPointer = (event) => {
    if (closest(event.target, SELECTORS.dropdown)) return;
    root.querySelectorAll(SELECTORS.dropdown).forEach(closeDropdown);
  };

  root.addEventListener('click', onClick);
  root.addEventListener('keydown', onKeydown);
  root.addEventListener('click', onPointer, true);

  return {
    destroy() {
      root.removeEventListener('click', onClick);
      root.removeEventListener('keydown', onKeydown);
      root.removeEventListener('click', onPointer, true);
    }
  };
}

export function showToast(message, { root = typeof document !== 'undefined' ? document : null, duration = 4000, variant = 'info' } = {}) {
  if (!root || !message) return null;
  let region = query(root, '[data-nova-toast-region]');
  if (!region) {
    region = document.createElement('div');
    region.className = 'nova-toast-region';
    region.dataset.novaToastRegion = '';
    region.setAttribute('aria-live', 'polite');
    region.setAttribute('aria-atomic', 'false');
    (root.body ?? root).append(region);
  }
  const toast = document.createElement('div');
  toast.className = `nova-toast nova-toast-${['success', 'warning', 'danger', 'info'].includes(variant) ? variant : 'info'}`;
  toast.setAttribute('role', variant === 'danger' ? 'alert' : 'status');
  toast.textContent = String(message);
  region.append(toast);
  const timer = setTimeout(() => toast.remove(), Math.max(0, duration));
  return { element: toast, dismiss() { clearTimeout(timer); toast.remove(); } };
}

export const interactions = { init: createInteractions, toast: showToast };
