import { ref, readonly, Ref } from 'vue'
import { TemplatePartConfig, TemplatePart } from './models'
import { find } from 'lodash-es'

// Global template parts store
const templateParts: Ref<TemplatePart[]> = ref([])

export function useTemplateParts() {
	const registerTemplatePart = (areaConfig: TemplatePartConfig): TemplatePart => {
		const templatePartInstance = new TemplatePart(areaConfig)
		templateParts.value.push(templatePartInstance)

		return templatePartInstance
	}

	const getTemplatePart = (id: string) => {
		return find(templateParts.value, {id: id})
	}

	return {
		templateParts,
		registerTemplatePart,
		getTemplatePart
	}
}