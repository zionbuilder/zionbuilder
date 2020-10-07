import Model from '../Model'

export default class PageArea extends Model {
	defaults () {
		return {
			areaName: '',
			content: [],
			id: 0
		}
	}

	get areaName (): string {
		return this.areaName
	}

	get id (): string {
		return this.id
	}
}