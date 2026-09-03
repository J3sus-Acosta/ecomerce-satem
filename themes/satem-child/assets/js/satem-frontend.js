/**
 * SATEM Frontend Interactive Functionality
 *
 * @package SatemChild
 */

document.addEventListener('DOMContentLoaded', function () {
	// 1. Mobile Menu Toggle logic
	const toggleBtn = document.querySelector('.satem-nav-toggle');
	const navMenu = document.querySelector('.satem-nav-main');

	if (toggleBtn && navMenu) {
		toggleBtn.addEventListener('click', function () {
			const expanded = toggleBtn.getAttribute('aria-expanded') === 'true' || false;
			toggleBtn.setAttribute('aria-expanded', !expanded);
			navMenu.classList.toggle('is-active');
		});
	}

	// 2. Sticky Header Elevation Shadow on Scroll
	const siteHeader = document.querySelector('.satem-site-header');
	if (siteHeader) {
		window.addEventListener('scroll', function () {
			if (window.scrollY > 20) {
				siteHeader.classList.add('is-scrolled');
			} else {
				siteHeader.classList.remove('is-scrolled');
			}
		});
	}

	// 3. Shop Filter Toggle for Mobile Viewports
	const filterToggle = document.querySelector('.satem-filter-toggle-btn');
	const sidebarFilter = document.querySelector('.satem-shop-sidebar');

	if (filterToggle && sidebarFilter) {
		filterToggle.addEventListener('click', function () {
			sidebarFilter.classList.toggle('is-open');
		});
	}

	// 4. Case Pack Quantity Step Increment Helper Buttons
	const quantityInputs = document.querySelectorAll('.woocommerce .quantity input.qty');
	quantityInputs.forEach(function (input) {
		const step = parseInt(input.getAttribute('step')) || 1;
		const min = parseInt(input.getAttribute('min')) || 1;

		if (step > 1 && !input.parentNode.querySelector('.satem-qty-btn')) {
			const container = input.parentNode;
			container.classList.add('satem-qty-wrapper');

			const decBtn = document.createElement('button');
			decBtn.type = 'button';
			decBtn.className = 'satem-qty-btn satem-qty-minus';
			decBtn.textContent = '-';
			decBtn.setAttribute('aria-label', 'Decrease quantity');

			const incBtn = document.createElement('button');
			incBtn.type = 'button';
			incBtn.className = 'satem-qty-btn satem-qty-plus';
			incBtn.textContent = '+';
			incBtn.setAttribute('aria-label', 'Increase quantity');

			container.insertBefore(decBtn, input);
			container.appendChild(incBtn);

			decBtn.addEventListener('click', function () {
				let currentVal = parseInt(input.value) || min;
				if (currentVal - step >= min) {
					input.value = currentVal - step;
					input.dispatchEvent(new Event('change', { bubbles: true }));
				}
			});

			incBtn.addEventListener('click', function () {
				let currentVal = parseInt(input.value) || min;
				input.value = currentVal + step;
				input.dispatchEvent(new Event('change', { bubbles: true }));
			});
		}
	});
});
