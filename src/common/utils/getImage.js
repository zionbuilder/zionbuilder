import { ServerRequest } from '@/common/utils';

export default function (imageConfig) {
	const serverRequester = new ServerRequest();

	return new Promise((resolve, reject) => {
		// Check to see if we actually need to retrieve the image
		if (imageConfig && imageConfig.image && imageConfig.image_size && imageConfig.image_size !== 'full') {
			// Check to see if we have the image in cache
			let size = imageConfig.image_size;

			// Check to see if we have a custom size
			if (size === 'custom') {
				const customSize = imageConfig.custom_size || {};
				let { width = 0, height = 0 } = customSize;
				width = width || 0;
				height = height || 0;
				size = `zion_custom_${width}x${height}`;
			}

			// New server Request feature
			serverRequester.request(
				{
					type: 'get_image',
					config: imageConfig,
					useCache: true,
				},
				response => {
					// Send back the image
					resolve(response[size]);
				},
				function (message) {
					// eslint-disable-next-line
				console.log('server Request fail', message)
					reject(new Error('image could not be retrieved'));
				},
			);
		} else if (imageConfig.image) {
			resolve(imageConfig.image);
		} else {
			reject(new Error('bad config for image', imageConfig));
		}
	});
}
