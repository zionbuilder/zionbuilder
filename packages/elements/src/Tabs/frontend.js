import './scss/main.scss';

(function ($) {
	window.ZionBuilderFrontend.registerScript('counter', {
		run(scope) {
			const $tabs = $(scope).find('.zb-el-tabs').addBack('.zb-el-tabs');

			if ($tabs.length > 0) {
				$tabs.each((i, el) => {
					const $el = $(el);
					const $tabsWrapper = $el.closest('.zb-el-tabs');
					const $tabLinksContainer = $tabsWrapper.find('.zb-el-tabs-nav');
					const $tabLinks = $tabsWrapper.find('.zb-el-tabs-nav-title');
					const $tabsContent = $tabsWrapper.find('.zb-el-tabsItem');

					let tabFocus = 0;
					$tabLinksContainer.on('keydown', function (e) {
						// Move right
						if (e.keyCode === 39 || e.keyCode === 37) {
							$tabLinks[tabFocus].setAttribute('tabindex', -1);
							if (e.keyCode === 39) {
								tabFocus++;

								// If we're at the end, go to the start
								if (tabFocus >= $tabLinks.length) {
									tabFocus = 0;
								}

								// Move left
							} else if (e.keyCode === 37) {
								tabFocus--;
								// If we're at the start, move to the end
								if (tabFocus < 0) {
									tabFocus = $tabLinks.length - 1;
								}
								// Activate tab on space and enter
							}

							$tabLinks[tabFocus].setAttribute('tabindex', 0);
							$tabLinks[tabFocus].focus();
						}

						if (e.keyCode === 32 || e.keyCode === 13) {
							activateTab(tabFocus);
						}
					});

					function activateTab(index) {
						// Remove active class from existing items
						$tabLinks.removeClass('zb-el-tabs-nav--active');
						$tabsContent.removeClass('zb-el-tabs-nav--active');

						const $clickedItem = $($tabLinks[index]);
						// Add active class to clicked item
						$clickedItem.addClass('zb-el-tabs-nav--active');
						$tabsContent.eq(index).addClass('zb-el-tabs-nav--active');
					}

					$tabLinks.on('click', function (event) {
						const clickedItemIndex = Array.from($tabLinks).indexOf(this);

						activateTab(clickedItemIndex);
					});
				});
			}
		},
	});
})(window.jQuery);
