import { defineStore } from 'pinia';
import { ref } from 'vue';
import { saveOptions, getSavedOptions } from '../api';
import { get, update, unset, cloneDeep, debounce } from 'lodash-es';

export const useBuilderOptionsStore = defineStore('builderOptions', () => {
	const isLoading = ref(false);
	let fetched = false;
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
		users_permissions: {},
		custom_code: '',
	});

	if (!fetched) {
		fetchOptions();
	}

	function fetchOptions(force = false) {
		// Don't refetch the options if  they were already fetched
		if (fetched && !force) {
			return Promise.resolve(options.value);
		}

		return getSavedOptions()
			.then(response => {
				const data = response.data;

				// Set data
				if (Array.isArray(data.user_roles_permissions)) {
					data.user_roles_permissions = {};
				}

				// Set data
				if (Array.isArray(data.users_permissions)) {
					data.users_permissions = {};
				}

				options.value = {
					...options.value,
					...data,
				};
			})
			.finally(() => {
				fetched = true;
			});
	}

	function getOptionValue(optionId: string, defaultValue = null) {
		return get(options.value, optionId, defaultValue);
	}

	function updateOptionValue(path: string, newValue: any, saveOptions = true) {
		update(options.value, path, () => newValue);

		if (saveOptions) {
			saveOptionsToDB();
		}
	}

	function deleteOptionValue(path: string, saveOptions = true) {
		const clonedValues = cloneDeep(options.value);
		unset(clonedValues, path);
		options.value = clonedValues;

		if (saveOptions) {
			saveOptionsToDB();
		}
	}
	async function saveOptionsToDB() {
		isLoading.value = true;

		try {
			return await saveOptions(options.value);
		} finally {
			isLoading.value = false;
		}
	}

	const debouncedSaveOptions = debounce(saveOptionsToDB, 700);

	function updateGoogleFont(fontFamily: string, newValue: object) {
		const savedFont = options.value.google_fonts.find(fontItem => fontItem.font_family === fontFamily);

		if (savedFont) {
			const fontIndex = options.value.google_fonts.indexOf(savedFont);
			options.value.google_fonts.splice(fontIndex, 1, newValue);
		}

		saveOptionsToDB();
	}

	function removeGoogleFont(fontFamily: string) {
		const savedFont = options.value.google_fonts.find(fontItem => fontItem.font_family === fontFamily);
		if (savedFont) {
			const fontIndex = options.value.google_fonts.indexOf(savedFont);
			options.value.google_fonts.splice(fontIndex, 1);
		} else {
			console.warn('Font for deletion was not found');
		}

		saveOptionsToDB();
	}

	function addGoogleFont(fontFamily: string) {
		options.value.google_fonts.push({
			font_family: fontFamily,
			font_variants: ['regular'],
			font_subset: ['latin'],
		});

		saveOptionsToDB();
	}

	function addLocalColor(color: string) {
		options.value.local_colors.push(color);

		saveOptionsToDB();
	}

	function deleteLocalColor(color: string) {
		const colorIndex = options.value.local_colors.indexOf(color);

		if (colorIndex !== -1) {
			options.value.local_colors.splice(colorIndex, 1);
		}

		saveOptionsToDB();
	}

	function editLocalColor(color, newColor, saveToDB = true) {
		const colorIndex = options.value.local_colors.indexOf(color);

		if (colorIndex !== -1) {
			options.value.local_colors.splice(colorIndex, 1, newColor);
		}

		if (saveToDB) {
			saveOptionsToDB();
		}
	}

	function addGlobalColor(color) {
		options.value.global_colors.push(color);

		saveOptionsToDB();
	}

	function deleteGlobalColor(color) {
		const colorIndex = options.value.global_colors.indexOf(color);

		if (colorIndex !== -1) {
			options.value.global_colors.splice(colorIndex, 1);
		}

		saveOptionsToDB();
	}

	function editGlobalColor(index, newColor, saveToDB = true) {
		const colorToChange = { ...options.value.global_colors[index] };
		colorToChange['color'] = newColor;
		options.value.global_colors.splice(index, 1, colorToChange);

		if (saveToDB) {
			saveOptionsToDB();
		}
	}

	function addCustomFont(font) {
		options.value.custom_fonts.push(font);
		saveOptionsToDB();
	}

	function updateCustomFont(fontFamily: string, newValue: object) {
		const savedFont = options.value.custom_fonts.find(fontItem => fontItem.font_family === fontFamily);

		if (savedFont) {
			const fontIndex = options.value.custom_fonts.indexOf(savedFont);
			options.value.custom_fonts.splice(fontIndex, 1, newValue);
		}

		saveOptionsToDB();
	}

	function deleteCustomFont(fontFamily: string) {
		const savedFont = options.value.custom_fonts.find(fontItem => fontItem.font_family === fontFamily);
		if (savedFont) {
			const fontIndex = options.value.custom_fonts.indexOf(savedFont);
			options.value.custom_fonts.splice(fontIndex, 1);
		} else {
			console.warn('Font for deletion was not found');
		}

		saveOptionsToDB();
	}

	function addLocalGradient(gradient) {
		options.value.local_gradients.push(gradient);
		saveOptionsToDB();
	}

	function deleteLocalGradient(gradient) {
		const gradientIndex = options.value.local_gradients.indexOf(gradient);

		if (gradientIndex !== -1) {
			options.value.local_gradients.splice(gradientIndex, 1);
		}

		saveOptionsToDB();
	}

	function editLocalGradient(gradientId, newgradient) {
		const editedGradient = options.value.local_gradients.find(gradient => gradient.id === gradientId);

		if (editedGradient) {
			editedGradient.config = newgradient;
		}
	}

	function addGlobalGradient(gradient) {
		options.value.global_gradients.push(gradient);
		saveOptionsToDB();
	}

	function deleteGlobalGradient(gradient) {
		const gradientIndex = options.value.global_gradients.indexOf(gradient);

		if (gradientIndex !== -1) {
			options.value.global_gradients.splice(gradientIndex, 1);
		}

		saveOptionsToDB();
	}

	function editGlobalGradient(gradientId, newgradient) {
		const editedGradient = options.value.global_gradients.find(gradient => gradient.id === gradientId);

		if (editedGradient) {
			editedGradient.config = newgradient;
		}
	}

	function addTypeKitToken(token) {
		options.value.typekit_token = token;
	}

	function addFontProject(fontId) {
		const fontIndex = options.value.typekit_fonts.indexOf(fontId);

		if (fontIndex === -1) {
			options.value.typekit_fonts.push(fontId);
		}
		saveOptionsToDB();
	}

	function removeFontProject(fontId) {
		const fontIndex = options.value.typekit_fonts.indexOf(fontId);

		if (fontIndex !== -1) {
			options.value.typekit_fonts.splice(fontIndex, 1);
		}
		saveOptionsToDB();
	}

	function addUserPermissions(user) {
		options.value.users_permissions[user.id] = {
			allowed_access: true,
			permissions: {
				only_content: false,
				features: [],
				post_types: [],
			},
		};

		saveOptionsToDB();
	}

	function editUserPermission(userID, newValues) {
		options.value.users_permissions[userID] = newValues;

		saveOptionsToDB();
	}

	function deleteUserPermission(userID) {
		delete options.value.users_permissions[userID];
		saveOptionsToDB();
	}

	function getUserPermissions(userID) {
		return options.value.users_permissions[userID];
	}

	function getRolePermissions(roleID) {
		return (
			options.value.user_roles_permissions[roleID] || {
				allowed_access: false,
				permissions: {
					only_content: false,
					features: [],
					post_types: [],
				},
			}
		);
	}

	function editRolePermission(roleID: string, newValues) {
		options.value.user_roles_permissions[roleID] = newValues;

		saveOptionsToDB();
	}

	return {
		// refs
		isLoading,
		fetched,
		options,

		// Actions
		fetchOptions,
		getOptionValue,
		updateOptionValue,
		deleteOptionValue,
		saveOptionsToDB,
		editRolePermission,
		getRolePermissions,
		getUserPermissions,
		editUserPermission,
		deleteUserPermission,
		addUserPermissions,
		removeFontProject,
		addFontProject,
		addTypeKitToken,
		editGlobalGradient,
		deleteGlobalGradient,
		addGlobalGradient,
		editLocalGradient,
		deleteLocalGradient,
		addLocalGradient,
		deleteCustomFont,
		updateCustomFont,
		addCustomFont,
		editGlobalColor,
		removeGoogleFont,
		updateGoogleFont,
		addGoogleFont,
		addLocalColor,
		deleteLocalColor,
		editLocalColor,
		addGlobalColor,
		deleteGlobalColor,
		debouncedSaveOptions,
	};
});
