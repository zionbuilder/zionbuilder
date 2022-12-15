import axios from 'axios';

export function getService() {
	return axios.create({
		baseURL: `${window.ZBCommonData.rest.rest_root}zionbuilder/v1/`,
		headers: {
			'X-WP-Nonce': window.ZBCommonData.rest.nonce,
			Accept: 'application/json',
			'Content-Type': 'application/json',
		},
	});
}
