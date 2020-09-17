export default class {
	strings = {}

	install (Vue, strings) {
		// Add the strings
		this.addStrings(strings)

		// Add helper method
		Vue.prototype.$translate = (string) => {
			return this.translate(string)
		}
	}

	addStrings = (strings) => {
		this.strings = {
			...this.strings,
			...strings
		}
	}

	translate = (stringId) => {
		if (typeof this.strings[stringId] !== 'undefined') {
			return this.strings[stringId]
		}

		// eslint-disable-next-line
		console.error(`String with id ${stringId} was not found.`)
	}
}
