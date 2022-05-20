import axios from 'axios';

const restConfig = window.ZnRestConfig;

const ZionService = axios.create({
	baseURL: `${restConfig.rest_root}zionbuilder/v1/`,
	headers: {
		'X-WP-Nonce': restConfig.nonce,
		Accept: 'application/json',
		'Content-Type': 'application/json',
	},
});

export default ZionService;
