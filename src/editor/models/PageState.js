export default class {
	constructor () {
		this.activeResponsiveDevice = 'default'
		this.elementFocus = null
	}

	getElementFocus = () => {
		return this.elementFocus
	}

	setElementFocus = (element) => {
		this.elementFocus = element
	}

	setActiveResponsiveDevice = (deviceId) => {
		this.activeResponsiveDevice = deviceId
	}
}
