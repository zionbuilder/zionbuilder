import { ref, Ref, computed } from 'vue'
import { getFontsDataSet } from '@zb/rest'

const dataSets: Ref<{[key: string]: object}> = ref({
	fonts_list: {
		google_fonts: [],
		custom_fonts: [],
		typekit_fonts: []
	},
	user_roles: [],
	post_types: [],
	icons: [],
	image_sizes: []
})

getFontsDataSet().then((response) => {
	dataSets.value = response.data
})

export const useDataSets = () => {

	const fontsListForOption = computed(() => {
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

		// Add fonts
		Object.keys(state.dataSets.fonts_list).forEach(fontProviderId => {
			const fontsList = state.dataSets.fonts_list[fontProviderId]

			option = [...option, ...fontsList]
		})

		return option
	})

	return {
		dataSets,
		fontsListForOption
	}
}