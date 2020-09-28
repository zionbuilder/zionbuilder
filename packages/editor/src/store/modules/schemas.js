
const state = {
	typographyOptionSchema: window.ZnRestConfig.schemas.typography,
	videoOptionSchema: window.ZnRestConfig.schemas.video,
	backgroundImageSchema: window.ZnRestConfig.schemas.background_image,
	shadowSchema: window.ZnRestConfig.schemas.shadow,
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
