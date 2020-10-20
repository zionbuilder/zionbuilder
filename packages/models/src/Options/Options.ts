import Collection from '../Collection'
import Option from './Option'

export default class Options extends Collection {
	getModel() {
		return Option
	}

	// getOptionValue(optionId: String) {
	// 	if (typeof Option[optionId] !== 'undefined') {
	// 		return Option[optionId]
	// 	}
	// },

	saveOptions(Option) {
		return new Promise((resolve, reject) => {
			saveOptions(Option)
				.then((response) => { })
				.catch(function (error) {
					reject(error)
				})
				.finally(() => {

					resolve()
				})
		})
	}

}