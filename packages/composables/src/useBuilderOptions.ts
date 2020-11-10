import { ref, Ref } from 'vue'
import { saveOptions, getSavedOptions } from '@zionbuilder/rest'
import { get, update } from 'lodash-es'

interface GoogleFontSavedItem {
	font_family: string,
	font_variants: string[],
	font_subset: string[],
}

const isLoading: Ref<boolean> = ref(false)
const fetchedOptions = ref(false)
const options = ref({
	allowed_post_types: ['post', 'page'],
	google_fonts: [],
	custom_fonts: [],
	typekit_token: '',
	typekit_fonts: [],
	local_colors: [],
	global_colors: [],
	local_gradients: [],
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

		// General
		options,
		isLoading
	}
}