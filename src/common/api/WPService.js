import axios from 'axios';

let restConfig = window.ZnRestConfig;

export const WPService = axios.create({
	baseURL: `${restConfig.rest_root}wp/v2`,
	headers: {
		'X-WP-Nonce': restConfig.nonce,
		Accept: 'application/json',
		'Content-Type': 'application/json',
	},
});
