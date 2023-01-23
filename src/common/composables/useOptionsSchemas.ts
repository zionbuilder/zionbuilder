import { ref } from 'vue';
import { cloneDeep } from 'lodash-es';

const schemas = ref<Record<string, object>>({
	element_advanced: window.ZBCommonData.schemas.element_advanced,
	element_styles: window.ZBCommonData.schemas.styles,
	typography: window.ZBCommonData.schemas.typography,
	videoOptionSchema: window.ZBCommonData.schemas.video,
	backgroundImageSchema: window.ZBCommonData.schemas.background_image,
	shadowSchema: window.ZBCommonData.schemas.shadow,
	styles: window.ZBCommonData.schemas.styles,
});

export const useOptionsSchemas = () => {
	const getSchema = (schemaId: string) => {
		return cloneDeep(schemas.value[schemaId]) || {};
	};

	const registerSchema = (schemaId: string, schema: Record<string, object>) => {
		schemas.value[schemaId] = schema;
	};

	return {
		schemas,
		getSchema,
		registerSchema,
	};
};
