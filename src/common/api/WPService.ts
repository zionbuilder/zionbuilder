import axios from 'axios';

export function createWPService() {
	return axios.create({
		baseURL: `${window.ZBCommonData.rest.rest_root}wp/v2`,
		headers: {
			'X-WP-Nonce': window.ZBCommonData.rest.nonce,
			Accept: 'application/json',
			'Content-Type': 'application/json',
		},
	});
}
