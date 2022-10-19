import { getService } from './ZionService';

export function uploadFile(data) {
	return getService().post('upload', data, {
		headers: {
			'Content-Type': 'multipart/form-data',
		},
	});
}
