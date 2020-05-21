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
	}

	return valueToReturn
}
