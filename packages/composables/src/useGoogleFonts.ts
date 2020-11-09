import { ref } from 'vue'
import { getGoogleFonts } from '@zb/rest'

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


	return {
		googleFonts,
		fetchGoogleFonts
	}
}