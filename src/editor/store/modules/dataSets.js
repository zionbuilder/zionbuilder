import * as types from '../mutation-types'
import { getFontsDataSet } from '@/api/DataSets.js'
import {
	deleteIconsPackage
} from '@/api/DataSets'

const state = {
	dataSets: {
		fonts_list: {
			google_fonts: [],
			custom_fonts: [],
			typekit_fonts: []
		},
		user_roles: [],
		post_types: [],
		icons: [],
		image_sizes: []
	}
}

const getters = {
	getFontListForOption: state => {
		let option = [
			{
				'id': 'Arial',
				'name': 'Arial'
			},
			{
				'id': 'Times New Roman',
				'name': 'Times New Roman'
			},
			{
				'id': 'Verdana',
				'name': 'Verdana'
			},
			{
				'id': 'Trebuchet',
				'name': 'Trebuchet'
			},
			{
				'id': 'Georgia',
				'name': 'Georgia'
			},
			{
				'id': 'Segoe UI',
				'name': 'Segoe UI'
			}
		]
		let googleFonts = state.dataSets.fonts_list.google_fonts
		let typekitFonts = state.dataSets.fonts_list.typekit_fonts
		if (googleFonts) {
			googleFonts.forEach(font => {
				option.push({
					name: font.font_family,
					id: font.font_family
				})
			})
		}
		if (typekitFonts) {
			typekitFonts.forEach(font => {
				Object.keys(font).forEach(function (item) {
					const a = font[item]
					Object.entries(a).forEach(([key, value]) => {
						if (key === 'families') {
							a[key].forEach(fam => {
								if (fam.hasOwnProperty('name')) {
									option.push({
										name: fam.name,
										id: fam.name
									})
								}
							})
						}
					})
				})
			})
		}

		return option
	},
	getUserRoles: state => state.dataSets.user_roles,
	getPostTypes: state => state.dataSets.post_types,
	getIconsList: state => state.dataSets.icons
}

const actions = {
	initialiseDataSets: ({ commit }) => {
		if (localStorage.getItem('zion_builder_data_sets')) {
			commit(types.SET_DATA_SETS, localStorage.getItem('zion_builder_data_sets'))
		} else {
			return getFontsDataSet().then((response) => {
				commit(types.SET_DATA_SETS, response.data)
			})
		}
	},
	deleteIconSet: ({ commit }, payload) => {
		deleteIconsPackage(payload).then(() => {
			// Delete from store
			commit(types.DELETE_ICON_SET, payload)
		})
	},
	addIconsSet: ({ commit }, payload) => {
		// Delete from store
		commit(types.ADD_ICON_SET, payload)
	}

}

const mutations = {
	[types.SET_DATA_SETS] (state, dataSets) {
		state.dataSets = dataSets
	},
	[types.DELETE_ICON_SET] (state, payload) {
		const iconsPackage = state.dataSets.icons.find((iconSet) => {
			return iconSet.id === payload
		})

		if (iconsPackage !== undefined) {
			const iconsPackageIndex = state.dataSets.icons.indexOf(iconsPackage)
			state.dataSets.icons.splice(iconsPackageIndex, 1)
		}
	},
	[types.ADD_ICON_SET] (state, payload) {
		state.dataSets.icons.push(payload)
	}
}

export default {
	state,
	getters,
	actions,
	mutations
}
