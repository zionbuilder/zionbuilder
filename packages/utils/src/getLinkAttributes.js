export default function (linkConfig) {
	const valueToReturn = {}

	if (linkConfig && linkConfig.link) {
		valueToReturn.href = linkConfig.link

		if (linkConfig.title) {
			valueToReturn.title = linkConfig.title
		}

		if (linkConfig.target) {
			valueToReturn.target = linkConfig.target
		}
		// Check for link attributes
		if (Array.isArray(linkConfig.attributes)) {
			linkConfig.attributes.forEach(attributeConfig => {
				if (typeof attributeConfig.key !== 'undefined' && attributeConfig.key.length > 0) {
					valueToReturn[attributeConfig.key] = attributeConfig.value
				}
			});
		}
	}

	return valueToReturn
}
