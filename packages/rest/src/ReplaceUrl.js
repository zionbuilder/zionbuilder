import ZionService from './ZionService'

export const replaceUrl = function (url) {
	return ZionService.post('replace-url', url)
}
