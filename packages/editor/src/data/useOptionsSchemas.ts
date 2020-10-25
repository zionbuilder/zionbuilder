import { ref } from 'vue'

const schemas = ref({
	element_advanced: window.ZnRestConfig.schemas.element_advanced,
	element_styles: window.ZnRestConfig.schemas.styles,
	typographyOptionSchema: window.ZnRestConfig.schemas.typography,
	videoOptionSchema: window.ZnRestConfig.schemas.video,
	backgroundImageSchema: window.ZnRestConfig.schemas.background_image,
	shadowSchema: window.ZnRestConfig.schemas.shadow,
	pageSettingsSchema: window.ZnPbInitalData.page_settings.schema,
})

export const useOptionsSchemas = () => {
	const getSchema = (schemaId) => {
		return schemas.value[schemaId] || {}
	}

	return {
		schemas,
		getSchema
	}
}