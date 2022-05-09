import './scss/main.scss';

document.addEventListener('click', onAccordionClick);
document.addEventListener('keydown', onAccordionKeydown);

function onAccordionClick(event: MouseEvent) {
	if (event.target) {
		initAccordion(event.target);
	}
}

function onAccordionKeydown(event: KeyboardEvent) {
	if (event.code == 'Space') {
		initAccordion(event.target);
	}
}

function initAccordion(domNode: HTMLElement) {
	if (!domNode.classList.contains('zb-el-accordions-accordionTitle')) {
		return;
	}

	const wrapperElement = <HTMLElement>domNode.closest('.zb-el-accordions');
	const accordionType = wrapperElement.dataset.accordionType || 'accordion';
	const $clickedItem = <HTMLElement>domNode.closest('.zb-el-accordions-accordionWrapper');

	if (accordionType === 'accordion') {
		if ($clickedItem?.classList.contains('zb-el-accordions--active')) {
			closeItem($clickedItem);
		} else {
			const activeAccordions = wrapperElement.querySelectorAll('.zb-el-accordions--active');
			activeAccordions.forEach(accordion => {
				if (accordion instanceof HTMLElement) {
					closeItem(accordion);
				}
			});

			openItem($clickedItem);
		}
	} else {
		if ($clickedItem?.classList.contains('zb-el-accordions--active')) {
			closeItem($clickedItem);
		} else {
			openItem($clickedItem);
		}
	}
}

function closeItem(item: HTMLElement) {
	const contentWrapper = <HTMLElement>item.querySelector('.zb-el-accordions-accordionContent');

	if (!contentWrapper) {
		return;
	}

	window.requestAnimationFrame(() => {
		const height = contentWrapper.clientHeight;
		contentWrapper.style.height = `${height}px`;
		item.classList.add('zb-el-accordions--transition');

		window.requestAnimationFrame(() => {
			contentWrapper.style.height = `${0}px`;
		});

		const complete = () => {
			contentWrapper.style.height = null;
			item.classList.remove('zb-el-accordions--transition');
			item.classList.remove('zb-el-accordions--active');
			contentWrapper.removeEventListener('transitionend', complete);
		};

		contentWrapper.addEventListener('transitionend', complete);
	});
}

function openItem(item: HTMLElement) {
	const contentWrapper = item.querySelector('.zb-el-accordions-accordionContent');
	window.requestAnimationFrame(() => {
		item.classList.add('zb-el-accordions--active');
		const height = contentWrapper.clientHeight;
		item.classList.remove('zb-el-accordions--active');
		contentWrapper.style.height = `${0}px`;

		window.requestAnimationFrame(() => {
			item.classList.add('zb-el-accordions--transition');
			item.classList.add('zb-el-accordions--active');
			contentWrapper.style.height = `${height}px`;
		});
	});

	const complete = () => {
		contentWrapper.style.height = null;
		item.classList.remove('zb-el-accordions--transition');
		contentWrapper.removeEventListener('transitionend', complete);
	};

	contentWrapper.addEventListener('transitionend', complete);
}

// (function ($) {
// 	$(document).on('click keydown', '.zb-el-accordions-accordionTitle', function (event) {
// 		if (event.type === 'keydown' && event.which !== 32) {
// 			return;
// 		}

// 		const $wrapperElement = $(event.target).closest('.zb-el-accordions');
// 		const accordionType = $wrapperElement.attr('data-accordion-type') || 'accordion';
// 		const $clickedItem = $(event.target).closest('.zb-el-accordions-accordionWrapper');

// 		if (accordionType === 'accordion') {
// 			if ($clickedItem.hasClass('zb-el-accordions--active')) {
// 				closeItem($clickedItem);
// 			} else {
// 				const $activeAccordions = $wrapperElement.children('.zb-el-accordions--active');
// 				closeItem($activeAccordions);
// 				openItem($clickedItem);
// 			}
// 		} else {
// 			if ($clickedItem.hasClass('zb-el-accordions--active')) {
// 				closeItem($clickedItem);
// 			} else {
// 				openItem($clickedItem);
// 			}
// 		}

// 		function closeItem($items) {
// 			if ($items.length > 0) {
// 				$items.each((index, item) => {
// 					const $item = $(item);
// 					const $contentWrapper = $item.find('.zb-el-accordions-accordionContent').first();

// 					window.requestAnimationFrame(() => {
// 						const height = $contentWrapper[0].clientHeight;
// 						$contentWrapper[0].style.height = `${height}px`;
// 						$item.addClass('zb-el-accordions--transition');

// 						window.requestAnimationFrame(() => {
// 							$contentWrapper[0].style.height = `${0}px`;
// 						});

// 						const complete = () => {
// 							$contentWrapper[0].style.height = null;
// 							$item.removeClass('zb-el-accordions--transition');
// 							$item.removeClass('zb-el-accordions--active');
// 							$contentWrapper.off('transitionend', complete);
// 						};

// 						$contentWrapper.on('transitionend', complete);
// 					});
// 				});
// 			}
// 		}

// 		function openItem($item) {
// 			const $contentWrapper = $item.find('.zb-el-accordions-accordionContent').first();
// 			window.requestAnimationFrame(() => {
// 				$item.addClass('zb-el-accordions--active');
// 				const height = $contentWrapper[0].clientHeight;
// 				$item.removeClass('zb-el-accordions--active');
// 				$contentWrapper[0].style.height = `${0}px`;

// 				window.requestAnimationFrame(() => {
// 					$item.addClass('zb-el-accordions--transition');
// 					$item.addClass('zb-el-accordions--active');
// 					$contentWrapper[0].style.height = `${height}px`;
// 				});
// 			});

// 			const complete = () => {
// 				$contentWrapper[0].style.height = null;
// 				$item.removeClass('zb-el-accordions--transition');
// 				$contentWrapper.off('transitionend', complete);
// 			};

// 			$contentWrapper.on('transitionend', complete);
// 		}
// 	});
// })(window.jQuery);
