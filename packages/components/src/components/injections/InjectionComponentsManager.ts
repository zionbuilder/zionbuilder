export default class InjectionComponentsManager {
	registeredLocations: any
	constructor () {
		// Holds a refference to all locations and coponents registered
		this.registeredLocations = {}
	}

	/**
	 * Register a component for a location
	 */
	registerComponent = (location: string, component: any, priority = 10) => {
		if (!location && !component) {
			// eslint-disable-next-line
			console.warn('You need to specify a location and a component in order to register an injection component.', {
				location,
				component
			})

			return false
		}

		if (!Array.isArray(this.registeredLocations[location])) {
			this.registeredLocations[location] = []
		}

		this.registeredLocations[location].push(component)
	}

	/**
	 *
	 * @param {String} location The location for which we need to return the injection components
	 */
	getComponentsForLocation = (location: string) => {
		if (!location) {
			// eslint-disable-next-line
			console.warn('You need to specify a location and a component in order to get injection components.', {
				location
			})

			return false
		}

		if (!Array.isArray(this.registeredLocations[location])) {
			return []
		}

		return this.registeredLocations[location]
	}
}
