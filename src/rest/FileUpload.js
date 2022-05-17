import ZionService from './ZionService'

export const uploadFile = function (data) {
	return ZionService.post('upload', data, {
		headers: {
			'Content-Type': 'multipart/form-data'
		}
	})
}
