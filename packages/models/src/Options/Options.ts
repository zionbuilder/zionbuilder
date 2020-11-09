import Model from '../Model'

import { saveOptions, getSavedOptions } from '@zionbuilder/rest'
import { ref, Ref } from 'vue'

const isLoading: Ref<boolean> = ref(false)
export default class Options extends Model {

	defaults() {
		const options: Ref = ref({
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
		return {
			options
		}
	}


	getOptionValue(optionId: String, defaultValue = null) {

		if (typeof this.options.value[optionId] !== 'undefined') {
			return this.options.value[optionId]
		} else {
			return defaultValue
		}
	}

	updateOptionValue(optionId, { key, value }) {
		let optionIndex = this.options.value[optionId].indexOf(key)
		this.options.value[optionId].splice(optionIndex, 1, value)
		this.saveOptions()
	}

	addOptionValue(optionId, key) {
		this.options.value[optionId].push(key)
		this.saveOptions()
	}

	deleteOptionValue(optionId, key) {
		let optionIndex = this.options.value[optionId].indexOf(key)
		if (optionIndex !== undefined) {
			this.options.value[optionId].splice(optionIndex, 1)
		} else {
			// eslint-disable-next-line
			console.warn('option for deletion was not found')
		}
		this.saveOptions()

	}

	saveOptions() {
		isLoading.value = true
		return new Promise((resolve, reject) => {
			saveOptions(this.options.value)
				.then((response) => { })
				.catch(function (error) {
					reject(error)
				})
				.finally(() => {
					isLoading.value = false
					resolve()
				})
		})
	}

	isLoading() {
		return isLoading.value
	}

	fetchOptions() {
		return getSavedOptions().then((response) => {
			const newOptions = {
				...this.options.value,
				...response.data
			}
			this.options.value = newOptions
		})
	}

	editGlobalColor({ index, color }) {
		this.options.value.global_colors[index] = color
		this.saveOptions()
	}

	addGradient(optionId, gradient) {
		let arrayLength = this.options.value[optionId].length
		let dynamicId = `gradient${arrayLength + 1}`
		this.options.value[optionId].push({
			id: dynamicId,
			name: dynamicId,
			...gradient
		})
	}

	addTypeKitToken(token: String) {
		this.options.value['typekit_token'] = token
	}

	getUserPermissions() {
		return this.options.value.users_permissions
	}

	getPermissions(userRole) {
		const roleConfig = this.options.value.user_roles_permissions[userRole]
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
	}

	editUserRole({ role, value }) {
		const updatedRoles = Object.assign({}, this.options.value.user_roles_permissions, {
			[role]: value
		})
		this.options.value.user_roles_permissions = updatedRoles

		this.saveOptions()
	}

	editUserPermission({ role, value }) {
		const updatedUsers = Object.assign({}, this.options.value.users_permissions, {
			[role]: value
		})
		this.options.value.users_permissions = updatedUsers
		this.saveOptions()
	}

	deleteUserPermission(value) {
		delete this.options.value.users_permissions[value]
		this.saveOptions()
	}
}