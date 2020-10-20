import { ref, markRaw, Ref } from 'vue'
import ServerComponent from '../components/ServerComponent.vue'
import { ScriptsLoader } from '../ScriptsLoader'
import { useElementTypes } from '@zb/editor'

export function useElementComponent(element) {
	const elementComponent: Ref = ref(null)

	const fetchElementComponent = () => {
		loadElementAssets().then(() => {
			const { getElementType } = useElementTypes()
			const elementType = getElementType(element.element_type)

			if (elementType.component) {
				elementComponent.value = elementType.component
			} else {
				elementComponent.value = ServerComponent
			}
		})
	}

	const loadElementAssets = () => {
		const { loadScript } = ScriptsLoader()

		return Promise.all(
			[
				...Object.keys(element.elementTypeModel.scripts).map(scriptHandle => {
					// Script can sometimes be false
					const scriptConfig = element.elementTypeModel.scripts[scriptHandle]

					// Set the handle if it was not provided
					scriptConfig.handle = scriptConfig.handle ? scriptConfig.handle : scriptHandle

					if (scriptConfig.src) {
						return loadScript(scriptConfig)
					}
				}),
				...Object.keys(element.elementTypeModel.styles).map(scriptHandle => {
					// Script can sometimes be false
					const scriptConfig = element.elementTypeModel.styles[scriptHandle]

					// Set the handle if it was not provided
					scriptConfig.handle = scriptConfig.handle ? scriptConfig.handle : scriptHandle

					if (scriptConfig.src) {
						return loadScript(scriptConfig)
					}
				})
			]
		)
	}

	return {
		elementComponent,
		fetchElementComponent
	}
}

