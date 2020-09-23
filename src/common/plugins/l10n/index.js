export default class {
	strings = {}

	install (app, strings) {
		// Add the strings
		this.addStrings(strings)

		// Add helper method
		app.config.globalProperties.$translate = (string) => {
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
