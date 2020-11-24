import './scss/main.scss'

(function ($) {
	$(document).on('click', '.zb-el-alert__closeIcon', function () {
		$(this).closest('.zb-el-alert').hide()
	})
})(jQuery)
