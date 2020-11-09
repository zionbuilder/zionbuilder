import { ref } from 'vue'
import { getGoogleFonts } from '@zb/rest'
import { getFontsDataSet } from '@zionbuilder/rest'

const googleFonts = ref([])
const fetchedOptions = ref(false)

export const useGoogleFonts = () => {
	const fetchGoogleFonts = (force = false) => {
		// Don't refetch the options if  they were already fetched
		if (fetchedOptions.value && !force) {
			return Promise.resolve(googleFonts.value)
		}

		return getGoogleFonts().then((response) => {
			googleFonts.value = response.data
		})
	}

	const getFontData = (family: String) => {
		return googleFonts.value.find((font) => {
			return font['family'] == family
		})
	}

	return {
		googleFonts,
		fetchGoogleFonts,
		getFontData
	}
}