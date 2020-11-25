import Routes from './Routes'

class Route {
	routeConfig: Object;

	constructor (routeConfig = {}) {
		const { children, ...remainingRouteConfig } = routeConfig
		this.routeConfig = remainingRouteConfig

		// Check if the route has children
		if (typeof children !== 'undefined') {
			Object.keys(children).forEach(routeId => {
				this.addRoute(routeId, children[routeId])
			})
		}
	}

	addRoute (routeId, routeConfig) {
		if (!(this.routeConfig.children instanceof Routes)) {
			this.routeConfig.children = new Routes()
		}

		return this.routeConfig.children.addRoute(routeId, routeConfig)
	}

	getRoute (path) {
		return this.routeConfig.children.getRoute(path)
	}

	getConfigForRouter () {
		const routeConfig = { ...this.routeConfig }

		if (routeConfig.children instanceof Routes) {
			routeConfig.children = routeConfig.children.getConfigForRouter()
		}

		return routeConfig
	}

	set (key, value) {
		this.routeConfig[key] = value
	}

	remove (key) {
		delete this.routeConfig[key]
	}

	get (key) {
		delete this.routeConfig[key]
	}
}

export default Route
