export const ScriptsLoader = (context = window.document) => {
	const getAvailableScripts = () => {
		let scripts = {}
		const allScripts = context.getElementsByTagName('script')

		Array.from(allScripts).forEach(domNode => {
			if (domNode.src) {
				scripts[domNode.src] = 'done'
			}
		})

		return scripts
	}

	const getAvailableStyles = () => {
		let styles = {}
		const allStyles = context.getElementsByTagName('link')

		Array.from(allStyles).forEach(domNode => {
			if (domNode.rel === 'stylesheet') {
				styles[domNode.href] = 'done'
			}
		})

		return styles
	}

	const loadedScripts = getAvailableScripts()
	const loadedStyles = getAvailableStyles()

	const loadScript = (scriptConfig) => {
		const scriptType = scriptConfig.src.indexOf('.js') !== -1 ? 'javascript' : scriptConfig.src.indexOf('.css') !== -1 ? 'css' : false
		console.log({scriptType});
		if (scriptType === 'javascript') {
			if (scriptConfig.data) {
				addInlineJavascript(scriptConfig.data, context)
			}

			if (scriptConfig.before) {
				addInlineJavascript(scriptConfig.before, context)
			}

			return loadJavaScriptFile(scriptConfig.src, context)
				.then(() => {
					if (scriptConfig.after) {
						addInlineJavascript(scriptConfig.after, context)
					}
				})
				.catch(err => console.error(err))
		} else if (scriptType === 'css') {
			return loadCssFile(scriptConfig.src, context)
				.catch(err => console.error(err))
		}
	}

	const addInlineJavascript = (code) => {
		const javascriptTag = context.createElement('script')
		javascriptTag.type = 'text/javascript'
		const inlineScript = context.createTextNode(code)
		javascriptTag.appendChild(inlineScript)
		context.body.appendChild(javascriptTag)
	}

	const loadJavaScriptFile = (url) => {
		// Check to see if the asset was already loaded or is pending
		if (typeof loadedScripts[url] === 'object') {
			return loadedScripts[url]
		} else if (loadedScripts[url] === 'done') {
			return Promise.resolve(url)
		} else if (loadedScripts[url] === 'error') {
			return Promise.reject(url)
		}

		const promise = new Promise((resolve, reject) => {
			const javascriptTag = context.createElement('script')
			javascriptTag.src = url

			javascriptTag.onload = () => {
				resolve(context)
				loadedScripts[url] = 'done'
			}

			javascriptTag.onerror = () => {
				reject(context)
				loadedScripts[url] = 'error'
			}

			context.body.appendChild(javascriptTag)
		})

		// add the file to imported files
		loadedScripts[url] = promise

		return promise
	}

	const loadCssFile = (url) => {
		// Check to see if the asset was already loaded or is pending
		if (typeof loadedStyles[url] === 'object') {
			return loadedStyles[url]
		} else if (loadedStyles[url] === 'done') {
			return Promise.resolve(url)
		} else if (loadedStyles[url] === 'error') {
			return Promise.reject(url)
		}

		const promise = new Promise((resolve, reject) => {
			const styleLink = context.createElement('link')
			styleLink.type = 'text/css'
			styleLink.rel = 'stylesheet'
			styleLink.href = url

			styleLink.onload = () => {
				resolve(context)
				loadedStyles[url] = 'done'
			}

			styleLink.onerror = () => {
				reject(context)
				loadedStyles[url] = 'error'
			}

			context.getElementsByTagName('head')[0].appendChild(styleLink)
		})

		// add the file to imported files
		loadedStyles[url] = promise

		return promise
	}

	return {
		getAvailableScripts,
		getAvailableStyles,
		loadScript,
		addInlineJavascript,
		loadJavaScriptFile,
		loadCssFile
	}
}