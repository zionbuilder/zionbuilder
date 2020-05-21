export default class Localization {
	strings = {}

	constructor (strings) {
		this.strings = strings
	}

	translate (stringId) {
		if (typeof this.strings[stringId] !== 'undefined') {
			return this.strings[stringId]
		}

		// eslint-disable-next-line
		console.error(`String with id ${stringId} was not found.`)
	}
}
