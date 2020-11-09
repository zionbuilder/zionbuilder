import { ref } from 'vue'
import { saveOptions, getSavedOptions } from '@zionbuilder/rest'

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

	return {
		fetchOptions,
		options
	}
}