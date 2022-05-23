import { ref, markRaw, Ref } from 'vue';
import ServerComponent from '../components/ServerComponent.vue';
import InvalidElement from '../components/InvalidElement.vue';
import { applyFilters } from '@common/modules/hooks';

import { ScriptsLoader } from '../modules/ScriptsLoader';

export function useElementComponent(element) {
	const elementComponent: Ref = ref(null);

	const fetchElementComponent = () => {
		loadElementAssets().then(() => {
			const elementType = element.elementTypeModel;
			let component;

			if (elementType.element_type === 'invalid') {
				component = markRaw(InvalidElement);
			} else if (elementType.component) {
				component = elementType.component;
			} else {
				component = markRaw(ServerComponent);
			}

			elementComponent.value = applyFilters('zionbuilder/element/component', component, element);
		});
	};

	const loadElementAssets = () => {
		const { loadScript } = ScriptsLoader();

		return Promise.all([
			...Object.keys(element.elementTypeModel.scripts).map(scriptHandle => {
				// Script can sometimes be false
				const scriptConfig = element.elementTypeModel.scripts[scriptHandle];

				// Set the handle if it was not provided
				scriptConfig.handle = scriptConfig.handle ? scriptConfig.handle : scriptHandle;

				if (scriptConfig.src) {
					return loadScript(scriptConfig);
				}
			}),
			...Object.keys(element.elementTypeModel.styles).map(scriptHandle => {
				// Script can sometimes be false
				const scriptConfig = element.elementTypeModel.styles[scriptHandle];

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
