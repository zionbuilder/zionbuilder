import { ref, Ref } from 'vue'
import { getFontsDataSet } from '@zionbuilder/rest'
const dataSets: Ref<Object> = ref({
	fonts_list: {
		google_fonts: [],
		custom_fonts: [],
		typekit_fonts: []
	},
	user_roles: [],
	post_types: [],
	icons: []
})

export function useDataSets() {
	const getFontListForOption = () => {

		let option = []
		let googleFonts = dataSets.value.fonts_list.google_fonts
		if (googleFonts) {
			googleFonts.forEach(font => {
				option.push({
					name: font.font_family,
					id: font.font_family
				})
			})
		}

		return option
	}

	const getUserRoles = () => {
		return dataSets.value.user_roles
	}

	const initialiseDataSets = () => {
		if (localStorage.getItem('zion_builder_data_sets')) {
			dataSets.value = localStorage.getItem('zion_builder_data_sets')

		} else {
			return getFontsDataSet().then((response) => {
				dataSets.value = response.data
			})
		}
		console.log('dataSets', dataSets.value)
	}

	const getPostTypes = () => {
		return dataSets.value.post_types
	}

	const getIconsList = () => {
		return dataSets.value.icons
	}

	const deleteIconSet = (icons) => {
		const iconsPackage = dataSets.value.icons.find((iconSet) => {
			return iconSet.id === icons
		})

		if (iconsPackage !== undefined) {
			const iconsPackageIndex = dataSets.value.icons.indexOf(iconsPackage)
			dataSets.value.icons.splice(iconsPackageIndex, 1)
		}

	}

	const addIconsSet = (iconSet) => {
		dataSets.value.icons.push(iconSet)
	}

	return {
		getFontListForOption,
		getUserRoles,
		getPostTypes,
		getIconsList,
		deleteIconSet,
		addIconsSet,
		initialiseDataSets
	}
}