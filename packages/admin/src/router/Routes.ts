import Route from './Route'

class Routes {
	routes = {}

	constructor (routes = {}) {
		Object.keys(routes).forEach(routeId => {
			const routeConfig = routes[routeId]
			this.routes[routeId] = new Route(routeId, routeConfig)
		})
	}

	getRouteConfig (pathString) {
		const paths = pathString.split('.')
		let searchSchema = this
		for (let index = 0; index < paths.length; index++) {
			const path = paths[index]

			if (!searchSchema) {
				return null
			}

			if (index === paths.length - 1) {
				return searchSchema.getRoute(path)
			}

			searchSchema = searchSchema.getRoute(path)
		}
	}

	getRoute (path) {
		return this.routes[path]
	}

	addRoute (routeId, routeConfig) {
		this.routes[routeId] = new Route(routeConfig)

		return this.routes[routeId]
	}

	replaceRoute () {

	}

	removeRoute () {

	}

	getConfigForRouter () {
		const routes = []

		Object.keys(this.routes).forEach(routeId => {
			const routeInstance = this.routes[routeId]
			routes.push(routeInstance.getConfigForRouter())
		})

		return routes
	}
}

export default Routes
