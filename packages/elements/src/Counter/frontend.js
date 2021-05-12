import './scss/main.scss'

(function ($) {
	window.ZionBuilderFrontend.registerScript('counter', {
		run(scope) {
			const $counter = $(scope).find('.zb-el-counter').addBack('.zb-el-counter')

			if ($counter.length > 0) {
				$counter.each((i, el) => {
					const $el = $(el)

					window.zb.animateJs({
						selector: el,
						mode: 'event'
					})

					function animate() {
						const $counterText = $(el).find('.zb-el-counter__number')
						const start = parseInt($el.attr('data-start') || 0)
						const end = parseInt($el.attr('data-end') || 100)
						const duration = parseInt($el.attr('data-duration') || 2000)

						$({
							value: start
						}).animate({
							value: end
						}, {
							duration: parseInt(duration, 10),
							start: start,
							step: function (now) {
								const value = parseInt(now)
								$($counterText).text(value)
							}
						})
					}

					el.addEventListener('inViewport', animate)
				})
			}
		}
	})
})(window.jQuery)
