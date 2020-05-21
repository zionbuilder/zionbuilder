import axios from 'axios'

let restConfig = window.ZnPbRestConfig

const ZionService = axios.create({
	baseURL: `https://library.zionbuilder.io/wp-json/zionbuilder-library/v1/`,
	headers: {
		'Accept': 'application/json',
		'Content-Type': 'application/json'
	}
})

export const getLibraryItems = function (useCace = true) {
	const url = 'items-and-categories'

	if (useCace) {
		return ZionService.get(url)
	} else {
		return ZionService.get(`${url}?timestamp=${new Date().getTime()}`)
	}
}
