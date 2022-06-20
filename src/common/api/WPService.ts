import axios from 'axios';

export function createWPService() {
	return axios.create({
		baseURL: `${window.ZnRestConfig.rest_root}wp/v2`,
		headers: {
			'X-WP-Nonce': window.ZnRestConfig.nonce,
			Accept: 'application/json',
			'Content-Type': 'application/json',
		},
	});
}
