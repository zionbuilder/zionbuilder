import { each, merge } from 'lodash-es'
import Collection from './Collection'

export type genericObject = {
	[key: string]: any
}

export default class Model {
	[key: string]: any
	private readonly collection: Collection

	constructor (values: genericObject = {}, collection: Collection) {
		const valuesWithDefaults = merge(this.defaults(), values)
		// TODO: implement this
		const mutatedValues = this.applyMutations(valuesWithDefaults)

		// Assign the values
		Object.assign(this, mutatedValues)

		// Assign the collection
		this.collection = collection
	}

	defaults () {
		return {}
	}

	getCollection(): Collection {
		return this.collection
	}

	getValuesWithDefaults (values: genericObject) {
		return
	}

	set (id: string, value: any) {
		this[id] = value
	}

	/**
	 * Allows data mutation
	 */
	mutations() {
		return null
	}

	applyMutations (valuesWithDefaults) {
		const mutations = this.mutations()

		if (mutations) {
			each(mutations, (mutationCallback, mutationValueKey) => {
				if (typeof valuesWithDefaults[mutationValueKey] !== 'undefined') {
					valuesWithDefaults[mutationValueKey] = mutationCallback( valuesWithDefaults[mutationValueKey], valuesWithDefaults )
				}
			})
		}
		return valuesWithDefaults
	}
}