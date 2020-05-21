import './scss/main.scss'

(function ($) {
	window.ZionBuilderFrontend.registerScript('progressBars', {
		run (scope) {
			const $skillBarContainers = $(scope).find('.zb-el-progressBars').addBack('.zb-el-progressBars')

			$.each($skillBarContainers, function (i, ul) {
				let $liElements = $('li', ul)
				let start = 0

				$.each($liElements, function (i, $li) {
					let $i = $('.zb-el-progressBars__barProgress', $li)

					let percentage = $i.attr('data-width')

					/* increment transition step */
					start += 0.2

					$i.css('-o-transition-delay', start + 's')
					$i.css('-webkit-transition-delay', start + 's')
					$i.css('-moz-transition-delay: ' + start + 's')
					$i.css(' transition-delay: ' + start + 's')
					$i.css('width', percentage + '%')
				})
			})
		}
	})
})(window.jQuery)
