import './scss/main.scss'

// const $ = window.jQuery
(function ($) {
	$(document).on('click keydown', '.zb-el-accordions-accordionTitle', function (event) {
		if (event.type === 'keydown' && event.which !== 32) {
			return
		}
		const $wrapperElement = $(event.target).closest('.zb-el-accordions')
		const accordionType = $wrapperElement.attr('data-accordion-type') || 'accordion'
		const $clickedItem = $(event.target).closest('.zb-el-accordions-accordionWrapper')

		if (accordionType === 'accordion') {
			if ($clickedItem.hasClass('zb-el-accordions--active')) {
				closeItem($clickedItem)
			} else {
				const $activeAccordions = $wrapperElement.children('.zb-el-accordions--active')
				closeItem($activeAccordions)
				openItem($clickedItem)
			}
		} else {
			if ($clickedItem.hasClass('zb-el-accordions--active')) {
				closeItem($clickedItem)
			} else {
				openItem($clickedItem)
			}
		}

		function closeItem ($items) {
			if ($items.length > 0) {
				$items.each((index, item) => {
					const $item = $(item)
					const $contentWrapper = $item.find('.zb-el-accordions-accordionContent').first()

					window.requestAnimationFrame(() => {
						const height = $contentWrapper[0].clientHeight
						$contentWrapper[0].style.height = `${height}px`
						$item.addClass('zb-el-accordions--transition')

						window.requestAnimationFrame(() => {
							$contentWrapper[0].style.height = `${0}px`
						})

						const complete = () => {
							$contentWrapper[0].style.height = null
							$item.removeClass('zb-el-accordions--transition')
							$item.removeClass('zb-el-accordions--active')
							$contentWrapper.off('transitionend', complete)
						}

						$contentWrapper.on('transitionend', complete)
					})
				})
			}
		}

		function openItem ($item) {
			const $contentWrapper = $item.find('.zb-el-accordions-accordionContent').first()
			window.requestAnimationFrame(() => {
				$item.addClass('zb-el-accordions--active')
				const height = $contentWrapper[0].clientHeight
				$item.removeClass('zb-el-accordions--active')
				$contentWrapper[0].style.height = `${0}px`

				window.requestAnimationFrame(() => {
					$item.addClass('zb-el-accordions--transition')
					$item.addClass('zb-el-accordions--active')
					$contentWrapper[0].style.height = `${height}px`
				})
			})

			const complete = () => {
				$contentWrapper[0].style.height = null
				$item.removeClass('zb-el-accordions--transition')
				$contentWrapper.off('transitionend', complete)
			}

			$contentWrapper.on('transitionend', complete)
		}
	})
})(window.jQuery)
