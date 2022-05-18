import ZionService from './ZionService'

export const saveOptions = function (options) {
	return ZionService.post('options', options)
}

export const getSavedOptions = function (options) {
	return ZionService.get('options')
}
