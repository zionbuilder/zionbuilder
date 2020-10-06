import Model from './Model'
import { forEach, find, filter } from 'lodash-es'
import { reactive } from 'vue'

export default class Collection {
	models: Model[] = reactive([])

	constructor (data = []) {
		this.add(data)
	}

	getModel() {
		return Model
	}

	add (data: {[key: string]: any}): void
	add (data: Model): void {
		const validModel = this.getModel()
		console.log(typeof data)
		if (typeof data === validModel) {
			this.models.push(data)
		}

		forEach(data, (value) => {
			this.models.push( new validModel(value) )
		})
	}

	find(where) {
		return find(this.models, where);
	}

	filter(where) {
		return filter(this.models, where);
	}
}