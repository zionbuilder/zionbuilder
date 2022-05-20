import ZionService from './ZionService';

export function uploadFile(data) {
	return ZionService.post('upload', data, {
		headers: {
			'Content-Type': 'multipart/form-data',
		},
	});
}
