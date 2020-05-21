export default class ScriptsLoader {
	constructor () {
		this.loadedScripts = this.getAvailableScripts()
		this.loadedStyles = this.getAvailableStyles()
	}

	getAvailableScripts () {
		let scripts = {}
		const allScripts = window.document.getElementsByTagName('script')

		Array.from(allScripts).forEach(domNode => {
			if (domNode.src) {
				scripts[domNode.src] = 'done'
			}
		})

		return scripts
	}

	getAvailableStyles () {
		let styles = {}
		const allStyles = window.document.getElementsByTagName('link')

		Array.from(allStyles).forEach(domNode => {
			if (domNode.rel === 'stylesheet') {
				styles[domNode.href] = 'done'
			}
		})

		return styles
	}

	loadScript (scriptConfig, context = window.document) {
		const scriptType = scriptConfig.src.indexOf('.js') !== -1 ? 'javascript' : scriptConfig.src.indexOf('.css') !== -1 ? 'css' : false

		if (scriptType === 'javascript') {
			if (scriptConfig.data) {
				this.addInlineJavascript(scriptConfig.data, context)
			}

			if (scriptConfig.before) {
				this.addInlineJavascript(scriptConfig.before, context)
			}

			return this.loadJavaScriptFile(scriptConfig.src, context)
				.then(() => {
					if (scriptConfig.after) {
						this.addInlineJavascript(scriptConfig.after, context)
					}
				})
				.catch(err => console.error(err))
		} else if (scriptType === 'css') {
			return this.loadCssFile(scriptConfig.src, context)
				.catch(err => console.error(err))
		}
	}

	addInlineJavascript (code, context = window.document) {
		const javascriptTag = context.createElement('script')
		javascriptTag.type = 'text/javascript'
		const inlineScript = context.createTextNode(code)
		javascriptTag.appendChild(inlineScript)
		context.body.appendChild(javascriptTag)
	}

	loadJavaScriptFile (url, context = window.document) {
		// Check to see if the asset was already loaded or is pending
		if (typeof this.loadedScripts[url] === 'object') {
			return this.loadedScripts[url]
		} else if (this.loadedScripts[url] === 'done') {
			return Promise.resolve(url)
		} else if (this.loadedScripts[url] === 'error') {
			return Promise.reject(url)
		}

		const promise = new Promise((resolve, reject) => {
			const javascriptTag = context.createElement('script')
			javascriptTag.src = url

			javascriptTag.onload = () => {
				resolve(context)
				this.loadedScripts[url] = 'done'
			}

			javascriptTag.onerror = () => {
				reject(context)
				this.loadedScripts[url] = 'error'
			}

			context.body.appendChild(javascriptTag)
		})

		// add the file to imported files
		this.loadedScripts[url] = promise

		return promise
	}

	loadCssFile (url, context = window.document) {
		// Check to see if the asset was already loaded or is pending
		if (typeof this.loadedStyles[url] === 'object') {
			return this.loadedStyles[url]
		} else if (this.loadedStyles[url] === 'done') {
			return Promise.resolve(url)
		} else if (this.loadedStyles[url] === 'error') {
			return Promise.reject(url)
		}

		const promise = new Promise((resolve, reject) => {
			const styleLink = context.createElement('link')
			styleLink.type = 'text/css'
			styleLink.rel = 'stylesheet'
			styleLink.href = url

			styleLink.onload = () => {
				resolve(context)
				this.loadedStyles[url] = 'done'
			}

			styleLink.onerror = () => {
				reject(context)
				this.loadedStyles[url] = 'error'
			}

			context.getElementsByTagName('head')[0].appendChild(styleLink)
		})

		// add the file to imported files
		this.loadedStyles[url] = promise

		return promise
	}
}
