const registeredLocations: {[key:string]: []} = {}

export const useInjections = () => {

	/**
	 * Register a component for a location
	 */
	const registerComponent = (location: string, component: any, priority = 10) => {
		if (!location && !component) {
			// eslint-disable-next-line
			console.warn('You need to specify a location and a component in order to register an injection component.', {
				location,
				component
			})

			return false
		}

		if (!Array.isArray(registeredLocations[location])) {
			registeredLocations[location] = []
		}

		registeredLocations[location].push(component)
	}

	/**
	 *
	 * @param {String} location The location for which we need to return the injection components
	 */
	const getComponentsForLocation = (location: string) => {
		if (!location) {
			// eslint-disable-next-line
			console.warn('You need to specify a location and a component in order to get injection components.', {
				location
			})

			return false
		}

		if (!Array.isArray(registeredLocations[location])) {
			return []
		}

		return registeredLocations[location]
	}


	return {
		registerComponent,
		getComponentsForLocation,
		registeredLocations
	}
}