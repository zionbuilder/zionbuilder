import { Ref, shallowRef } from 'vue';
import ServerComponent from '../components/ServerComponent.vue';
import InvalidElement from '../components/InvalidElement.vue';
import { applyFilters } from '/@/common/modules/hooks';

import { ScriptsLoader } from '../modules/ScriptsLoader';
import { useElementDefinitionsStore } from '/@/editor/store';

export function useElementComponent(element: ZionElement) {
	const elementComponent: Ref = shallowRef(null);
	const elementsDefinitionStore = useElementDefinitionsStore();
	const elementType = elementsDefinitionStore.getElementDefinition(element.element_type);

	const fetchElementComponent = () => {
		loadElementAssets().then(() => {
			let component;

			if (elementType.element_type === 'invalid') {
				component = InvalidElement;
			} else if (elementType.component) {
				component = elementType.component;
			} else {
				component = ServerComponent;
			}

			elementComponent.value = applyFilters('zionbuilder/element/component', component, element);
		});
	};

	const loadElementAssets = () => {
		const { loadScript } = ScriptsLoader(window.frames[0]);

		return Promise.all([
			...Object.keys(elementType.scripts).map(scriptHandle => {
				// Script can sometimes be false
				const scriptConfig = elementType.scripts[scriptHandle];

				// Set the handle if it was not provided
				scriptConfig.handle = scriptConfig.handle ? scriptConfig.handle : scriptHandle;

				if (scriptConfig.src) {
					return loadScript(scriptConfig);
				}
			}),
			...Object.keys(elementType.styles).map(scriptHandle => {
				// Script can sometimes be false
				const scriptConfig = elementType.styles[scriptHandle];

				// Set the handle if it was not provided
				scriptConfig.handle = scriptConfig.handle ? scriptConfig.handle : scriptHandle;

				if (scriptConfig.src) {
					return loadScript(scriptConfig);
				}
			}),
		]);
	};

	return {
		elementComponent,
		fetchElementComponent,
	};
}
