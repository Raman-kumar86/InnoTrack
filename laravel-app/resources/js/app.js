import Chart from 'chart.js/auto';

window.Chart = Chart;

const root = document.documentElement;
const storageKey = 'startup-india-theme';

function applyTheme(theme) {
	const darkMode = theme === 'dark';
	root.classList.toggle('dark', darkMode);
	root.setAttribute('data-theme', theme);
	try {
		window.localStorage.setItem(storageKey, theme);
	} catch (error) {
		return;
	}
}

function getPreferredTheme() {
	try {
		const savedTheme = window.localStorage.getItem(storageKey);
		if (savedTheme) {
			return savedTheme;
		}
	} catch (error) {
		// Ignore storage issues in restricted environments.
	}

	return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function setSidebarState(open) {
	const sidebar = document.querySelector('[data-sidebar]');
	const overlay = document.querySelector('[data-sidebar-overlay]');

	if (!sidebar || !overlay) {
		return;
	}

	sidebar.classList.toggle('-translate-x-full', !open);
	overlay.classList.toggle('hidden', !open);
	document.body.classList.toggle('overflow-hidden', open);
}

function initThemeToggle() {
	document.querySelectorAll('[data-theme-toggle]').forEach((toggle) => {
		toggle.addEventListener('click', () => {
			applyTheme(root.classList.contains('dark') ? 'light' : 'dark');
		});
	});
}

function initSidebarToggle() {
	document.addEventListener('click', (event) => {
		const target = event.target.closest('[data-sidebar-open], [data-sidebar-close], [data-sidebar-overlay]');

		if (!target) {
			return;
		}

		if (target.matches('[data-sidebar-open]')) {
			setSidebarState(true);
		}

		if (target.matches('[data-sidebar-close]') || target.matches('[data-sidebar-overlay]')) {
			setSidebarState(false);
		}
	});
}

function initPasswordToggles() {
	document.querySelectorAll('[data-password-toggle]').forEach((button) => {
		button.addEventListener('click', () => {
			const selector = button.getAttribute('data-password-toggle');
			const input = selector ? document.querySelector(selector) : null;

			if (!input) {
				return;
			}

			const nextType = input.type === 'password' ? 'text' : 'password';
			input.type = nextType;
			button.setAttribute('aria-pressed', nextType === 'text' ? 'true' : 'false');
		});
	});
}

function readStorageValue(key, fallback = null) {
	try {
		const value = window.localStorage.getItem(key);

		return value === null ? fallback : value;
	} catch (error) {
		return fallback;
	}
}

function writeStorageValue(key, value) {
	try {
		window.localStorage.setItem(key, value);
	} catch (error) {
		return;
	}
}

function updatePreferenceStatus(key, enabled) {
	const chips = document.querySelectorAll(`[data-setting-status="${key}"], [data-setting-summary="${key}"]`);

	chips.forEach((chip) => {
		chip.textContent = enabled ? 'Enabled' : 'Disabled';
		chip.classList.toggle('bg-emerald-500/15', enabled);
		chip.classList.toggle('text-emerald-500', enabled);
		chip.classList.toggle('bg-slate-200', !enabled);
		chip.classList.toggle('text-slate-500', !enabled);
		chip.classList.toggle('dark:bg-slate-800', !enabled);
		chip.classList.toggle('dark:text-slate-400', !enabled);
		chip.classList.toggle('dark:bg-emerald-500/15', enabled);
	});

	document.querySelectorAll(`[data-setting-display="${key}"]`).forEach((input) => {
		input.checked = enabled;
	});
}

function updateThemeSelection(theme) {
	document.querySelectorAll('[data-theme-option]').forEach((button) => {
		const active = button.getAttribute('data-theme-option') === theme;
		button.classList.toggle('bg-emerald-500/10', active);
		button.classList.toggle('text-emerald-600', active);
		button.classList.toggle('dark:bg-emerald-500/15', active);
		button.classList.toggle('dark:text-emerald-400', active);
	});

	document.querySelectorAll('[data-theme-card]').forEach((card) => {
		const cardTheme = card.getAttribute('data-theme-card');
		const active = cardTheme === theme;

		card.classList.toggle('opacity-70', !active);
		card.classList.toggle('border-emerald-400/30', active);
		card.classList.toggle('bg-emerald-500/5', active);
	});

	document.querySelectorAll('[data-theme-status], [data-theme-summary]').forEach((chip) => {
		const active = theme === 'dark' ? chip.matches('[data-theme-status]') || chip.matches('[data-theme-summary]') : chip.matches('[data-theme-summary]');
		chip.classList.toggle('bg-emerald-500/15', active);
		chip.classList.toggle('text-emerald-500', active);
		chip.classList.toggle('bg-slate-200', !active);
		chip.classList.toggle('text-slate-500', !active);
		chip.classList.toggle('dark:bg-slate-800', !active);
		chip.classList.toggle('dark:text-slate-400', !active);
		chip.classList.toggle('dark:bg-emerald-500/15', active);

		if (chip.matches('[data-theme-summary]')) {
			chip.textContent = theme === 'dark' ? 'Dark' : 'Light';
		}

		const statusLabel = chip.querySelector('[data-theme-status-label]');
		if (statusLabel) {
			statusLabel.textContent = theme === 'dark' ? 'Active' : 'Inactive';
		}
	});
}

function openModal(selector) {
	if (!selector) {
		return;
	}

	const normalizedSelector = selector.startsWith('#') || selector.startsWith('.') ? selector : `#${selector}`;
	const modal = document.querySelector(normalizedSelector);

	if (!modal) {
		return;
	}

	modal.classList.remove('hidden');
	modal.classList.add('flex');
	document.body.classList.add('overflow-hidden');
}

function closeModal(modal) {
	if (!modal) {
		return;
	}

	modal.classList.add('hidden');
	modal.classList.remove('flex');
	document.body.classList.remove('overflow-hidden');
}

function initModals() {
	document.addEventListener('click', (event) => {
		const openTrigger = event.target.closest('[data-modal-open]');
		const closeTrigger = event.target.closest('[data-modal-close]');
		const modalBackdrop = event.target.closest('[data-modal]');

		if (openTrigger) {
			openModal(openTrigger.getAttribute('data-modal-open'));
			return;
		}

		if (closeTrigger) {
			closeModal(closeTrigger.closest('[data-modal]'));
			return;
		}

		if (modalBackdrop && event.target === modalBackdrop) {
			closeModal(modalBackdrop);
		}
	});
}

function setSectorModalView(modal, view) {
	if (!modal || !view) {
		return;
	}

	modal.querySelectorAll('[data-sector-view-tab]').forEach((tab) => {
		const active = tab.getAttribute('data-sector-view-tab') === view;
		tab.classList.toggle('bg-indigo-600', active);
		tab.classList.toggle('text-white', active);
		tab.classList.toggle('shadow-lg', active);
		tab.classList.toggle('shadow-indigo-600/20', active);
		tab.classList.toggle('bg-slate-100', !active);
		tab.classList.toggle('text-slate-600', !active);
		tab.classList.toggle('dark:bg-slate-800', !active);
		tab.classList.toggle('dark:text-slate-300', !active);
	});

	modal.querySelectorAll('[data-sector-view-panel]').forEach((panel) => {
		panel.classList.toggle('hidden', panel.getAttribute('data-sector-view-panel') !== view);
	});
}

function initSectorModalViews() {
	document.addEventListener('click', (event) => {
		const trigger = event.target.closest('[data-sector-view-tab]');

		if (!trigger) {
			return;
		}

		const modal = trigger.closest('[data-sector-modal]');
		const view = trigger.getAttribute('data-sector-view-tab');

		if (!modal || !view) {
			return;
		}

		setSectorModalView(modal, view);
	});
}

function initFlashMessages() {
	const dismissFlashMessages = () => {
		document.querySelectorAll('[data-flash-message]').forEach((element) => element.remove());
	};

	document.querySelectorAll('[data-flash-close]').forEach((button) => {
		button.addEventListener('click', () => {
			button.closest('[data-flash-message]')?.remove();
		});
	});

	window.setTimeout(dismissFlashMessages, 4500);
}

function initSettingsPreferences() {
	const storagePrefix = 'settings-pref:';

	document.querySelectorAll('[data-setting-toggle]').forEach((toggle) => {
		const key = toggle.getAttribute('data-setting-key');

		if (!key) {
			return;
		}

		const storedValue = readStorageValue(`${storagePrefix}${key}`);

		if (storedValue !== null) {
			toggle.checked = storedValue === '1';
		}

		updatePreferenceStatus(key, toggle.checked);

		toggle.addEventListener('change', () => {
			updatePreferenceStatus(key, toggle.checked);
			writeStorageValue(`${storagePrefix}${key}`, toggle.checked ? '1' : '0');
		});
	});

	document.querySelectorAll('[data-theme-option]').forEach((button) => {
		button.addEventListener('click', () => {
			const theme = button.getAttribute('data-theme-option');

			if (theme !== 'light' && theme !== 'dark') {
				return;
			}

			applyTheme(theme);
			updateThemeSelection(theme);
		});
	});

	updateThemeSelection(root.getAttribute('data-theme') || getPreferredTheme());
}

function readJson(value, fallback) {
	if (!value) {
		return fallback;
	}

	try {
		return JSON.parse(value);
	} catch (error) {
		return fallback;
	}
}

function initCharts() {
	document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
		const chartType = canvas.dataset.chart;
		const labels = readJson(canvas.dataset.labels, []);
		const datasets = readJson(canvas.dataset.datasets, []);
		const values = readJson(canvas.dataset.values, []);
		const horizontal = canvas.dataset.horizontal === 'true';

		const sharedOptions = {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: { display: false },
				tooltip: {
					backgroundColor: 'rgba(15, 23, 42, 0.95)',
					padding: 12,
					titleColor: '#f8fafc',
					bodyColor: '#cbd5e1',
					borderColor: 'rgba(148, 163, 184, 0.2)',
					borderWidth: 1,
				},
			},
		};

		const config = {
			type: chartType === 'sparkline' ? 'line' : chartType,
			data: {
				labels,
				datasets:
					datasets.length > 0
						? datasets
						: [
								{
									data: values,
									borderColor: '#4f46e5',
									backgroundColor: 'rgba(79, 70, 229, 0.12)',
									tension: 0.45,
									fill: chartType !== 'doughnut',
								},
							],
			},
			options: {
				...sharedOptions,
				indexAxis: horizontal ? 'y' : 'x',
				scales:
					chartType === 'doughnut'
						? {}
						: {
								x: {
									grid: { color: 'rgba(148, 163, 184, 0.12)' },
									ticks: { color: '#64748b' },
								},
								y: {
									grid: { color: 'rgba(148, 163, 184, 0.12)' },
									ticks: { color: '#64748b' },
								},
							},
			},
		};

		if (chartType === 'sparkline') {
			config.options.plugins.legend = { display: false };
			config.options.scales = { x: { display: false }, y: { display: false } };
			config.options.elements = { point: { radius: 0 } };
			config.options.plugins.tooltip.enabled = false;
			config.options.layout = { padding: 0 };
		}

		if (chartType === 'doughnut') {
			config.options.cutout = '68%';
		}

		new Chart(canvas, config);
	});
}

applyTheme(getPreferredTheme());
initThemeToggle();
initSidebarToggle();
initPasswordToggles();
initModals();
initSectorModalViews();
initFlashMessages();
initSettingsPreferences();
initCharts();
