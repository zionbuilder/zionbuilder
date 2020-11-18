import { ref, Ref } from 'vue'
import { TemplatePartConfig, TemplatePart } from './models'
import { find } from 'lodash-es'

// Global template parts store
const templateParts: Ref<{[key: string]: TemplatePart}> = ref({})

export function useTemplateParts() {
	const registerTemplatePart = (areaConfig: TemplatePartConfig): TemplatePart => {
		const templatePartInstance = new TemplatePart(areaConfig)
		templateParts.value[areaConfig.id] = templatePartInstance

		return templatePartInstance
	}

	const getTemplatePart = (id: string): TemplatePartConfig => {
		return templateParts.value[id]
	}

	return {
		templateParts,
		registerTemplatePart,
		getTemplatePart
	}
}