import { min } from 'lodash-es'
import { reactive } from 'vue'
import Element from './PageElement'

type InteractionsType = {
	activeElement: Element | null,
	copiedElement: Element | null,
}

export default class Interactions {
	data: InteractionsType = reactive({
		activeElement: null,
		copiedElement: null
	})

	setActiveElement(element) {
		this.data.activeItem = element
	}

	removeActiveElement() {
		this.data.activeItem = null
	}
}