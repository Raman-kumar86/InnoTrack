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

function openModal(selector) {
	const modal = document.querySelector(selector);

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
initCharts();
