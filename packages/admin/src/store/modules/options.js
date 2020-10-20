import * as types from '../mutation-types'
import { saveOptions, getSavedOptions } from '@zionbuilder/rest'

// Move to model collection

const state = {
	options: {
		allowed_post_types: ['post', 'page'],
		google_fonts: [],
		custom_fonts: [],
		typekit_token: '',
		typekit_fonts: [],
		local_colors: [],
		global_colors: [],
		local_gradients: [],
		global_gradients: [],
		user_roles_permissions: {},
		users_permissions: {}
	},
	loading: false
}

const getters = {
	get_options: state => state.options,
	isLoading: state => state.loading,
	getOptionValue: state => (optionId, defaultValue = null) => {
		if (typeof state.options[optionId] !== 'undefined') {
			return state.options[optionId]
		} else {
			return defaultValue
		}
	},
	getLocalGradients: state => state.options.local_gradients,
	getGlobalGradients: state => state.options.global_gradients,
	getPermissions: state => (userRole) => {
		const roleConfig = state.options.user_roles_permissions[userRole]
		const defaults = {
			allowed_access: false,
			permissions: {
				only_content: false,
				features: [],
				post_types: []
			}
		}

		return {
			...defaults,
			...(roleConfig || {})
		}
	},
	getUserPermissions: state => state.options.users_permissions,
	getUserPermissionsById: state => (id) => {
		const userConfig = state.options.users_permissions[id]
		const userdefaults = {
			allowed_access: false,
			permissions: {
				only_content: false,
				features: [],
				post_types: []
			}
		}

		return {
			...userdefaults,
			...(userConfig || {})
		}
	},
	getAllowedPosts: state => state.options.allowed_post_types
}

const actions = {
	fetchOptions: ({ commit }) => {
		return getSavedOptions().then((response) => {
			const newOptions = {
				...state.options,
				...response.data
			}
			commit(types.SET_OPTIONS, newOptions)
		})
	},

	deleteGoogleFont: ({ commit, dispatch }, payload) => {
		commit(types.DELETE_GOOGLE_FONT, payload)
		dispatch('saveOptions')
	},
	// updateGoogleFont: ({ commit, dispatch }, { font, value }) => {
	// 	commit(types.UPDATE_GOOGLE_FONT, { font, value })
	// 	dispatch('saveOptions')
	// },
	addGoogleFont: ({ commit, dispatch }, payload) => {
		commit(types.ADD_GOOGLE_FONT, payload)
		dispatch('saveOptions')
	},
	addTypeKitToken: ({ commit }, payload) => {
		commit(types.ADD_TYPEKIT_TOKEN, payload)
	},
	addTypekitFont: ({ commit, dispatch }, kitId) => {
		commit(types.ADD_TYPEKIT_FONT, kitId)
		dispatch('saveOptions')
	},
	removeTypekitFont: ({ commit, dispatch }, kitId) => {
		commit(types.REMOVE_TYPEKIT_FONT, kitId)
		dispatch('saveOptions')
	},
	saveOptions: ({ commit, dispatch }, payload) => {
		commit(types.SET_LOADING_STATE, true)
		return new Promise((resolve, reject) => {
			saveOptions(state.options)
				.then((response) => { })
				.catch(function (error) {
					reject(error)
				})
				.finally(() => {
					commit(types.SET_LOADING_STATE, false)
					resolve()
				})
		})
	},
	setOptions: ({ commit }, payload) => {
		commit(types.SET_OPTIONS, payload)
	},
	addCustomFont: ({ commit, dispatch }, payload) => {
		commit(types.ADD_CUSTOM_FONT, payload)
		dispatch('saveOptions')
	},
	deleteCustomFont: ({ commit, dispatch }, payload) => {
		commit(types.DELETE_CUSTOM_FONT, payload)
		dispatch('saveOptions')
	},
	addLocalColor: ({ commit, dispatch }, payload) => {
		commit(types.ADD_LOCAL_COLOR, payload)
		dispatch('saveOptions')
	},
	deleteLocalColor: ({ commit, dispatch }, payload) => {
		commit(types.DELETE_LOCAL_COLOR, payload)
		dispatch('saveOptions')
	},
	editLocalColor: ({ commit, dispatch }, { index, color }) => {
		commit(types.EDIT_LOCAL_COLOR, { index, color })
		dispatch('saveOptions')
	},
	addGlobalColor: ({ commit, dispatch }, payload) => {
		commit(types.ADD_GLOBAL_COLOR, payload)
		dispatch('saveOptions')
	},
	editGlobalColor: ({ commit, dispatch }, { index, color }) => {
		commit(types.EDIT_GLOBAL_COLOR, { index, color })
		dispatch('saveOptions')
	},
	deleteGlobalColor: ({ commit, dispatch }, payload) => {
		commit(types.DELETE_GLOBAL_COLOR, payload)
		dispatch('saveOptions')
	},
	// Gradients
	updateGradient: ({ commit }, payload) => {
		commit(types.UPDATE_GRADIENT, payload)
	},
	addLocalGradient: ({ commit }, payload) => {
		let arrayLength = state.options.global_gradients.length
		let dynamicId = `gradient${arrayLength + 1}`

		commit(types.ADD_LOCAL_GRADIENT, {
			id: dynamicId,
			name: dynamicId,
			...payload
		})
	},
	addGlobalGradient: ({ commit, state }, payload) => {
		let arrayLength = state.options.global_gradients.length
		let dynamicId = `gradient${arrayLength + 1}`

		commit(types.ADD_GLOBAL_GRADIENT, {
			id: dynamicId,
			name: dynamicId,
			...payload
		})
	},
	deleteLocalGradient: ({ commit, dispatch }, payload) => {
		commit(types.DELETE_LOCAL_GRADIENT, payload)
		dispatch('saveOptions')
	},
	deleteGlobalGradient: ({ commit, dispatch }, payload) => {
		commit(types.DELETE_GLOBAL_GRADIENT, payload)
		dispatch('saveOptions')
	},
	// user permissions
	editUserRole: ({ commit, dispatch }, { role, value }) => {
		commit(types.EDIT_USER_ROLE, { role, value })
		dispatch('saveOptions')
	},
	editUserPermission: ({ commit, dispatch }, { role, value }) => {
		commit(types.EDIT_USER_PERMISSION, { role, value })
		dispatch('saveOptions')
	},
	deleteUserPermission: ({ commit, dispatch }, payload) => {
		commit(types.DELETE_USER_PERMISSION, payload)
		dispatch('saveOptions')
	},
	// allowed post types
	addAllowedPostType: ({ commit, dispatch }, payload) => {
		commit(types.ADD_ALLOWED_POST_TYPE, payload)
		dispatch('saveOptions')
	},
	deleteAllowedPostType: ({ commit, dispatch }, payload) => {
		commit(types.DELETE_ALLOWED_POST_TYPE, payload)
		dispatch('saveOptions')
	}

}

const mutations = {
	[types.DELETE_GOOGLE_FONT] (state, payload) {
		let fontConfig = state.options.google_fonts.find((font) => {
			return font.font_family === payload
		})

		if (fontConfig) {
			let fontIndex = state.options.google_fonts.indexOf(fontConfig)
			if (fontIndex !== undefined) {
				state.options.google_fonts.splice(fontIndex, 1)
			} else {
				// eslint-disable-next-line
				console.warn('Font for deletion was not found')
			}
		}
	},
	[types.UPDATE_GOOGLE_FONT] (state, { font, value }) {
		let fontIndex = state.options.google_fonts.indexOf(font)
		state.options.google_fonts.splice(fontIndex, 1, value)
	},
	[types.ADD_GOOGLE_FONT] (state, payload) {
		state.options.google_fonts.push(payload)
	},
	[types.ADD_TYPEKIT_FONT] (state, kitId) {
		state.options.typekit_fonts.push(kitId)
	},
	[types.REMOVE_TYPEKIT_FONT] (state, kitId) {
		let index = state.options.typekit_fonts.indexOf(kitId)
		state.options.typekit_fonts.splice(index, 1)
	},
	[types.SET_OPTIONS] (state, payload) {
		state.options = payload
	},
	[types.ADD_CUSTOM_FONT] (state, payload) {
		state.options.custom_fonts.push(payload)
	},
	[types.DELETE_CUSTOM_FONT] (state, payload) {
		let fontIndex = state.options.custom_fonts.indexOf(payload)
		state.options.custom_fonts.splice(fontIndex, 1)
	},
	[types.SET_LOADING_STATE] (state, payload) {
		state.loading = payload
	},

	[types.ADD_TYPEKIT_TOKEN] (state, payload) {
		state.options.typekit_token = payload
	},
	[types.ADD_LOCAL_COLOR] (state, payload) {
		state.options.local_colors.push(payload)
	},
	[types.DELETE_LOCAL_COLOR] (state, payload) {
		state.options.local_colors.splice(payload, 1)
	},
	[types.EDIT_LOCAL_COLOR] (state, { index, color }) {
		state.options.local_colors.splice(index, 1, color)
	},
	[types.ADD_GLOBAL_COLOR] (state, payload) {
		state.options.global_colors.push(payload)
	},
	[types.DELETE_GLOBAL_COLOR] (state, payload) {
		state.options.global_colors.splice(payload, 1)
	},
	[types.EDIT_GLOBAL_COLOR] (state, payload) {
		state.options.global_colors[payload.index] = payload.color
	},
	// Gradients
	[types.UPDATE_GRADIENT] (state, { gradient, newValue }) {
		gradient.config = newValue
	},
	[types.ADD_LOCAL_GRADIENT] (state, payload) {
		state.options.local_gradients.push(payload)
	},
	[types.ADD_GLOBAL_GRADIENT] (state, payload) {
		state.options.global_gradients.push(payload)
	},
	[types.DELETE_LOCAL_GRADIENT] (state, payload) {
		state.options.local_gradients.splice(payload, 1)
	},
	[types.DELETE_GLOBAL_GRADIENT] (state, payload) {
		state.options.global_gradients.splice(payload, 1)
	},
	// user roles
	[types.EDIT_USER_ROLE] (state, { role, value }) {
		const updatedRoles = Object.assign({}, state.options.user_roles_permissions, {
			[role]: value
		})
		state.options.user_roles_permissions = updatedRoles
	},
	// wordpress users permissions
	[types.EDIT_USER_PERMISSION] (state, { role, value }) {
		const updatedUsers = Object.assign({}, state.options.users_permissions, {
			[role]: value
		})
		state.options.users_permissions = updatedUsers
	},
	[types.DELETE_USER_PERMISSION] (state, payload) {
		delete state.options.users_permissions[payload]
	},
	// ALLOWED POST TYPES
	[types.ADD_ALLOWED_POST_TYPE] (state, post) {
		state.options.allowed_post_types.push(post)
	},
	[types.DELETE_ALLOWED_POST_TYPE] (state, post) {
		let index = state.options.allowed_post_types.indexOf(post)
		state.options.allowed_post_types.splice(index, 1)
	}

}

export default {
	state,
	getters,
	actions,
	mutations
}
