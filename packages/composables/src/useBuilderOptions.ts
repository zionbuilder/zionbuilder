import { ref, Ref } from 'vue'
import { saveOptions, getSavedOptions } from '@zionbuilder/rest'
import { get, update, find } from 'lodash-es'

const isLoading: Ref<boolean> = ref(false)
const fetchedOptions = ref(false)
const options = ref({
	allowed_post_types: ['post', 'page'],
	google_fonts: [],
	custom_fonts: [],
	typekit_token: '',
	local_colors: [],
	global_colors: [],
	local_gradients: [],
	global_gradients: [],
	user_roles_permissions: {},
	users_permissions: {}
})

export const useBuilderOptions = () => {
	const fetchOptions = (force = false) => {
		// Don't refetch the options if  they were already fetched
		if (fetchedOptions.value && !force) {
			return Promise.resolve(options.value)
		}

		return getSavedOptions().then((response) => {
			const newOptions = {
				...options.value,
				...response.data
			}
			options.value = newOptions
		})
	}

	const getOptionValue = (optionId: string, defaultValue = null) => {
		return get(options.value, optionId, defaultValue)
	}

	const updateOptionValue = (path: string, newValue: any, saveOptions = true) => {
		update(options.value, path, () => newValue)

		if (saveOptions) {
			saveOptionsToDB()
		}
	}

	const saveOptionsToDB = () => {
		isLoading.value = true

		return saveOptions(options.value).finally(() => {
			isLoading.value = false
		})
	}

	const updateGoogleFont = (fontFamily: string, newValue: object) => {
		const savedFont = options.value.google_fonts.find(fontItem => fontItem.font_family === fontFamily)

		if (savedFont) {
			const fontIndex = options.value.google_fonts.indexOf(savedFont)
			options.value.google_fonts.splice(fontIndex, 1, newValue)
		}

		saveOptionsToDB()
	}

	const removeGoogleFont = (fontFamily: string) => {
		const savedFont = options.value.google_fonts.find(fontItem => fontItem.font_family === fontFamily)
		if (savedFont) {
			const fontIndex = options.value.google_fonts.indexOf(savedFont)
			options.value.google_fonts.splice(fontIndex, 1)
		} else {
			console.warn('Font for deletion was not found')
		}

		saveOptionsToDB()
	}

	const addGoogleFont = (fontFamily: string) => {
		options.value.google_fonts.push({
			font_family: fontFamily,
			font_variants: ['regular'],
			font_subset: ['latin']
		})

		saveOptionsToDB()
	}

	const addLocalColor = (color) => {
		options.value.local_colors.push(color)

		saveOptionsToDB()
	}

	const deleteLocalColor = (color) => {
		const colorIndex = options.value.local_colors.indexOf(color)

		if (colorIndex !== -1) {
			options.value.local_colors.splice(colorIndex, 1)
		}

		saveOptionsToDB()
	}

	const editLocalColor = (color, newColor, saveToDB = true) => {
		const colorIndex = options.value.local_colors.indexOf(color)

		if (colorIndex !== -1) {
			options.value.local_colors.splice(colorIndex, 1, newColor)
		}

		if (saveToDB) {
			saveOptionsToDB()
		}
	}

	const addGlobalColor = (color) => {
		options.value.global_colors.push(color)

		saveOptionsToDB()
	}

	const deleteGlobalColor = (color) => {
		const colorIndex = options.value.global_colors.indexOf(color)

		if (colorIndex !== -1) {
			options.value.global_colors.splice(colorIndex, 1)
		}

		saveOptionsToDB()
	}

	const editGlobalColor = (color, newColor, saveToDB = true) => {
		const colorIndex = options.value.global_colors.indexOf(color)

		if (colorIndex !== -1) {
			options.value.global_colors.splice(colorIndex, 1, newColor)
		}

		if (saveToDB) {
			saveOptionsToDB()
		}
	}

	const addCustomFont = (font) => {
		options.value.custom_fonts.push(font)
		saveOptionsToDB()
	}

	const updateCustomFont = (fontFamily: string, newValue: object) => {
		const savedFont = options.value.custom_fonts.find(fontItem => fontItem.font_family === fontFamily)

		if (savedFont) {
			const fontIndex = options.value.custom_fonts.indexOf(savedFont)
			options.value.custom_fonts.splice(fontIndex, 1, newValue)
		}

		saveOptionsToDB()
	}

	const deleteCustomFont = (fontFamily: string) => {
		const savedFont = options.value.custom_fonts.find(fontItem => fontItem.font_family === fontFamily)
		if (savedFont) {
			const fontIndex = options.value.custom_fonts.indexOf(savedFont)
			options.value.custom_fonts.splice(fontIndex, 1)
		} else {
			console.warn('Font for deletion was not found')
		}

		saveOptionsToDB()
	}

	const addLocalGradient = (gradient) => {
		options.value.local_gradients.push(gradient)
		saveOptionsToDB()
	}

	const deleteLocalGradient = (gradient) => {
		const gradientIndex = options.value.local_gradients.indexOf(gradient)

		if (gradientIndex !== -1) {
			options.value.local_gradients.splice(gradientIndex, 1)
		}

		saveOptionsToDB()
	}

	const editLocalGradient = (gradientId, newgradient) => {
		const editedGradient = options.value.local_gradients.find(gradient => gradient.id === gradientId)

		if (editedGradient) {
			editedGradient.config = newgradient


		}
	}

	const addGlobalGradient = (gradient) => {
		options.value.global_gradients.push(gradient)
		saveOptionsToDB()
	}

	const deleteGlobalGradient = (gradient) => {
		const gradientIndex = options.value.global_gradients.indexOf(gradient)

		if (gradientIndex !== -1) {
			options.value.global_gradients.splice(gradientIndex, 1)
		}

		saveOptionsToDB()
	}

	const editGlobalGradient = (gradientId, newgradient) => {
		const editedGradient = options.value.global_gradients.find(gradient => gradient.id === gradientId)

		if (editedGradient) {
			editedGradient.config = newgradient
		}
	}

	const addTypeKitToken = (token) => {
		options.value.typekit_token = token
	}


	const addUserPermissions = (user) => {
		options.value.users_permissions[user.id] = {
			allowed_access: true,
			permissions: {
				only_content: false,
				features: [],
				post_types: []
			}
		}

		saveOptionsToDB()
	}

	const editUserPermission = (userID, newValues) => {
		options.value.users_permissions[userID] = newValues

		saveOptionsToDB()
	}

	const deleteUserPermission = (userID) => {
		delete options.value.users_permissions[userID]
		saveOptionsToDB()
	}

	const getUserPermissions = (userID) => {
		return options.value.users_permissions[userID]
	}

	return {
		fetchOptions,
		getOptionValue,
		updateOptionValue,
		saveOptionsToDB,

		// Google fonts
		addGoogleFont,
		removeGoogleFont,
		updateGoogleFont,

		// Colors
		addLocalColor,
		deleteLocalColor,
		editLocalColor,
		addGlobalColor,
		deleteGlobalColor,
		editGlobalColor,

		//Custom Fonts
		addCustomFont,
		updateCustomFont,
		deleteCustomFont,

		// Gradients
		addLocalGradient,
		deleteLocalGradient,
		editLocalGradient,
		addGlobalGradient,
		deleteGlobalGradient,
		editGlobalGradient,
		// Typekit token
		addTypeKitToken,

		// Permissions
		addUserPermissions,
		getUserPermissions,
		deleteUserPermission,
		editUserPermission,

		// General
		options,
		isLoading
	}
}