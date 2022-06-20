import axios from 'axios';

export function getService() {
	return axios.create({
		baseURL: `${window.ZnRestConfig.rest_root}zionbuilder/v1/`,
		headers: {
			'X-WP-Nonce': window.ZnRestConfig.nonce,
			Accept: 'application/json',
			'Content-Type': 'application/json',
		},
	});
}
