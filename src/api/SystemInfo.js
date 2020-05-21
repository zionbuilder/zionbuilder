import ZionService from './ZionService'

export const getSystemInfo = function (options) {
	return ZionService.get('system-info')
}
