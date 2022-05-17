export function getIconUnicode(unicodeValue) {
	return JSON.parse(('"\\' + unicodeValue + '"'))
}

export default function (iconConfig) {
	const valueToReturn = {}

	if (iconConfig && iconConfig.family) {
		valueToReturn['data-znpbiconfam'] = iconConfig.family
		valueToReturn['data-znpbicon'] = getIconUnicode(iconConfig.unicode)
	}

	return valueToReturn
}
