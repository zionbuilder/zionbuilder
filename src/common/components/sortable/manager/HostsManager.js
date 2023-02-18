export default () => {
	let hosts = [];
	let iframes = [];

	const getHosts = () => {
		return hosts;
	};

	const getIframes = () => {
		return iframes;
	};

	const resetHosts = () => {
		hosts = [document];
		iframes = [];
	};

	const fetchHosts = () => {
		// Reset the cached hosts
		resetHosts();

		// Fetch new hosts
		const DOMIframes = document.querySelectorAll('iframe');

		DOMIframes.forEach(iframe => {
			if (iframe.contentDocument) {
				hosts.push(iframe.contentDocument);
				iframes.push(iframe);
			}
		});

		return this;
	};

	return {
		getHosts,
		getIframes,
		fetchHosts,
	};
};
