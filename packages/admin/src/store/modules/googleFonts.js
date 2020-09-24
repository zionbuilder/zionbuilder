import * as types from '../mutation-types'
import { getGoogleFonts } from '@zb/rest'

const state = {
	fonts: [],
	loaded: false
}

const getters = {
	getGoogleFonts: state => state.fonts,
	getFontData: state => (fontFamily) => state.fonts.find((font) => {
		return font.family === fontFamily
	})
}

const actions = {
	fetchGoogleFonts: ({ commit }) => {
		return getGoogleFonts().then((response) => {
			commit(types.SET_GOOGLE_FONTS, response.data)
		})
	}
}

const mutations = {
	[types.SET_GOOGLE_FONTS] (state, payload) {
		state.fonts = payload
	}
}

export default {
	state,
	getters,
	actions,
	mutations
}
