import { defineStore } from 'pinia';
import { saveOptions, getSavedOptions } from '../api';
import { get, update, unset, cloneDeep, debounce } from 'lodash-es';

export const useBuilderOptionsStore = defineStore('builderOptions', {
	state: () => {
		return {
			// all these properties will have their type inferred automatically
			isLoading: false,
			fetched: false,
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
				users_permissions: {},
			},
		};
	},
	actions: {
		fetchOptions(force = false) {
			// Don't refetch the options if  they were already fetched
			if (this.fetched && !force) {
				return Promise.resolve(this.options);
			}

			return getSavedOptions().then(response => {
				const data = response.data;

				// Set data
				if (Array.isArray(data.user_roles_permissions)) {
					data.user_roles_permissions = {};
				}

				// Set data
				if (Array.isArray(data.users_permissions)) {
					data.users_permissions = {};
				}
				console.log({ data });

				this.options = {
					...this.options,
					...data,
				};
				console.log({ options: this.options });
			});
		},
		getOptionValue(optionId: string, defaultValue = null) {
			return get(this.options, optionId, defaultValue);
		},
		updateOptionValue(path: string, newValue: any, saveOptions = true) {
			update(this.options, path, () => newValue);

			if (saveOptions) {
				this.saveOptionsToDB();
			}
		},
		deleteOptionValue(path: string, saveOptions = true) {
			const clonedValues = cloneDeep(this.options);
			unset(clonedValues, path);
			this.options = clonedValues;

			if (saveOptions) {
				this.saveOptionsToDB();
			}
		},
		async saveOptionsToDB() {
			this.isLoading = true;

			try {
				return await saveOptions(this.options);
			} finally {
				this.isLoading = false;
			}
		},
		debouncedSaveOptions() {
			return debounce(this.saveOptionsToDB, 700);
		},
	},
});
