const ZnPbLocalization = {
	install: function (Vue, LocalizationInstance) {
		Vue.prototype.$translate = function (string) {
			return LocalizationInstance.translate(string)
		}
	}
}

export default ZnPbLocalization

// Automatic installation if Vue has been added to the global scope.
if (typeof window !== 'undefined' && window.Vue) {
	window.Vue.use(ZnPbLocalization.install)
	if (ZnPbLocalization.install.installed) {
		ZnPbLocalization.install.installed = false
	}
}
