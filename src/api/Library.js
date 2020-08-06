import axios from 'axios'

let restConfig = window.ZnPbRestConfig

const ZionService = axios.create({
	baseURL: `http://zion-builder.test/wp-json/zionbuilder-library/v1/`,
	headers: {
		'Accept': 'application/json',
		'Content-Type': 'application/json'
	}
})

export const getLibraryItems = function (useCache = true) {
	const url = 'items-and-categories'
	if (useCache) {
		return ZionService.get(url)
	} else {
		return ZionService.get(`${url}?timestamp=${new Date().getTime()}`)
	}
}

export const getTemplateDownloadURL = function (useCache = true) {
	const url = 'items-and-categories'
	if (useCache) {
		return ZionService.get(url)
	} else {
		return ZionService.get(`${url}?timestamp=${new Date().getTime()}`)
	}
}
