import { ref, Ref } from 'vue'
import { saveOptions, getSavedOptions } from '@zionbuilder/rest'
import { get, update } from 'lodash-es'

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

	return {
		fetchOptions,
		getOptionValue,
		updateOptionValue,
		saveOptionsToDB,
		options,
		isLoading
	}
}