import { ref, readonly, Ref } from 'vue'
import { TemplatePartConfig, TemplatePart } from './models'
import { find } from 'lodash-es'

// Global template parts store
const templatePartsRef: Ref<TemplatePart[]> = ref([])

export function useTemplateParts() {
	const registerTemplatePart = (areaConfig: TemplatePartConfig): TemplatePart => {
		const templatePartInstance = new TemplatePart(areaConfig)
		templatePartsRef.value.push(templatePartInstance)

		return templatePartInstance
	}

	const getTemplatePart = (id: string) => {
		return find(templatePartsRef.value, {id: id})
	}

	const templateParts = readonly(templatePartsRef.value)

	return {
		templateParts,
		registerTemplatePart,
		getTemplatePart
	}
}