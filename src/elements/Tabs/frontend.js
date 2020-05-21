import './scss/main.scss'

(function ($) {
	$(document).on('click', '.zb-el-tabs-nav-title', function (event) {
		const $clickedItem = $(this)
		const $tabsWrapper = $clickedItem.closest('.zb-el-tabs')
		const $tabLinks = $tabsWrapper.find('.zb-el-tabs-nav-title')
		const $tabsContent = $tabsWrapper.find('.zb-el-tabsItem')
		const clickedItemIndex = Array.from($tabLinks).indexOf(this)

		// Remove active class from existing items
		$tabLinks.removeClass('zb-el-tabs-nav--active')
		$tabsContent.removeClass('zb-el-tabs-nav--active')

		// Add active class to clicked item
		$clickedItem.addClass('zb-el-tabs-nav--active')
		$tabsContent.eq(clickedItemIndex).addClass('zb-el-tabs-nav--active')
	})
})(window.jQuery)
