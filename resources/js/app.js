import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

window.showEmployeeLoader = (message = 'Please wait while we complete your request.') => {
	const loader = document.getElementById('employee-page-loader');
	const loaderMessage = document.getElementById('employee-page-loader-message');

	if (!loader) {
		return;
	}

	if (loaderMessage) {
		loaderMessage.textContent = message;
	}

	loader.classList.remove('hidden');
	loader.classList.add('flex');
	document.body.classList.add('overflow-hidden');
};

window.hideEmployeeLoader = () => {
	const loader = document.getElementById('employee-page-loader');

	if (!loader) {
		return;
	}

	loader.classList.add('hidden');
	loader.classList.remove('flex');
	document.body.classList.remove('overflow-hidden');
};

document.addEventListener('submit', (event) => {
	const form = event.target.closest('[data-swal-confirm]');

	if (!form) {
		return;
	}

	event.preventDefault();

	Swal.fire({
		title: form.dataset.swalTitle || 'Are you sure?',
		text: form.dataset.swalText || '',
		icon: form.dataset.swalIcon || 'warning',
		showCancelButton: true,
		confirmButtonColor: '#2563eb',
		cancelButtonColor: '#64748b',
		confirmButtonText: form.dataset.swalConfirmText || 'Yes, continue',
		cancelButtonText: 'Cancel',
		reverseButtons: true,
	}).then((result) => {
		if (result.isConfirmed) {
			const button = form.querySelector('button[type="submit"], button:not([type])');

			if (button) {
				button.disabled = true;
				button.innerHTML = '<span class="inline-flex items-center gap-2"><span class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>Processing...</span>';
				button.classList.add('cursor-wait', 'opacity-70');
			}

			form.removeAttribute('data-swal-confirm');
			form.submit();
		}
	});
});

document.addEventListener('DOMContentLoaded', () => {
	const flash = document.querySelector('[data-swal-flash]');

	if (flash) {
		Swal.fire({
		toast: true,
		position: 'top-end',
		icon: flash.dataset.swalType || 'success',
		title: flash.dataset.swalMessage,
		showConfirmButton: false,
		timer: 2600,
		timerProgressBar: true,
		});
	}

	const healthBadge = document.querySelector('[data-system-health]');

	if (!healthBadge) {
		return;
	}

	const refreshHealth = async () => {
		try {
			const response = await fetch(healthBadge.dataset.healthEndpoint, {
				headers: { Accept: 'application/json' },
				credentials: 'same-origin',
			});

			if (!response.ok) {
				return;
			}

			const health = await response.json();
			healthBadge.classList.remove('bg-emerald-50', 'text-emerald-700', 'bg-amber-50', 'text-amber-700', 'bg-red-50', 'text-red-700');
			healthBadge.classList.add(...(health.status_class || 'bg-red-50 text-red-700').split(' '));
			healthBadge.querySelector('[data-health-status]').textContent = health.status;
			healthBadge.querySelector('[data-health-query]').textContent = health.query_ms ?? '—';
			healthBadge.querySelector('[data-health-queued]').textContent = health.queued_jobs;
			healthBadge.title = `${health.queued_jobs} queued jobs, ${health.failed_jobs} historical failed jobs, ${health.query_ms ?? '—'} ms query`;
		} catch {
			// Keep the last known health state when a refresh request fails.
		}
	};

	window.setInterval(refreshHealth, Number(healthBadge.dataset.healthRefresh || 5000));
});

document.addEventListener('submit', (event) => {
	const form = event.target.closest('[data-loading]');

	if (!form) {
		return;
	}

	if (form.hasAttribute('data-swal-confirm')) {
		return;
	}

	window.showEmployeeLoader?.(form.dataset.loadingMessage || 'Saving your changes...');

	const button = form.querySelector('button[type="submit"], button:not([type])');

	if (button) {
		button.disabled = true;
		button.dataset.originalText = button.innerHTML;
		button.innerHTML = '<span class="inline-flex items-center gap-2"><span class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>Processing...</span>';
		button.classList.add('cursor-wait', 'opacity-70');
	}
});

document.addEventListener('click', (event) => {
	const link = event.target.closest('[data-loading-link]');

	const navigationLink = event.target.closest('a[href]');

	if (!link && (!navigationLink || navigationLink.target === '_blank' || navigationLink.getAttribute('href')?.startsWith('#'))) {
		return;
	}

	if ((link || navigationLink)?.target === '_blank') {
		return;
	}

	const target = link || navigationLink;
	window.showEmployeeLoader?.(target.dataset.loadingMessage || 'Opening the next screen...');

	if (target.hasAttribute('data-loading-download')) {
		window.setTimeout(() => window.hideEmployeeLoader?.(), 1200);
	}
});

Alpine.start();
