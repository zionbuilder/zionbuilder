
const state = {
	typographyOptionSchema: window.ZnPbInitalData.element_default_options_schema.typography_schema,
	videoOptionSchema: window.ZnPbInitalData.element_default_options_schema.video_schema,
	backgroundImageSchema: window.ZnPbInitalData.element_default_options_schema.background_image_schema,
	shadowSchema: window.ZnPbInitalData.element_default_options_schema.shadow,
	pageSettingsSchema: window.ZnPbInitalData.page_settings.schema
}

const getters = {
	getTypographyOptionSchema: state => state.typographyOptionSchema,
	getVideoOptionSchema: state => state.videoOptionSchema,
	getBackgroundImageOptionSchema: state => state.backgroundImageSchema,
	getShadowSchema: state => state.shadowSchema,
	getPageSettingsSchema: state => state.pageSettingsSchema
}

const actions = {
}

const mutations = {
}

export default {
	state,
	getters,
	actions,
	mutations
}
