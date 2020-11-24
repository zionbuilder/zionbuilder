let scripts = {}
let loaded = false
let loadedScripts
let loadedStyles

export const ScriptsLoader = () => {
	const getAvailableScripts = () => {
		const allScripts = window.document.getElementsByTagName('script')

		Array.from(allScripts).forEach(domNode => {
			if (domNode.src) {
				scripts[domNode.src] = 'done'
			}
		})

		return scripts
	}

	function reset () {
		let scripts = {}
		let loaded = false
		let loadedScripts = {}
		let loadedStyles = {}
	}

	const getAvailableStyles = () => {
		let styles = {}
		const allStyles = window.document.getElementsByTagName('link')

		Array.from(allStyles).forEach(domNode => {
			if (domNode.rel === 'stylesheet') {
				styles[domNode.href] = 'done'
			}
		})

		return styles
	}

	if (!loaded) {
		loadedScripts = getAvailableScripts()
		loadedStyles = getAvailableStyles()
		loaded = true
	}

	const loadScript = (scriptConfig) => {
		const scriptType = scriptConfig.src.indexOf('.js') !== -1 ? 'javascript' : scriptConfig.src.indexOf('.css') !== -1 ? 'css' : false

		if (scriptType === 'javascript') {
			if (scriptConfig.data) {
				addInlineJavascript(scriptConfig.data)
			}

			if (scriptConfig.before) {
				addInlineJavascript(scriptConfig.before)
			}

			return loadJavaScriptFile(scriptConfig.src)
				.then(() => {
					if (scriptConfig.after) {
						addInlineJavascript(scriptConfig.after)
					}
				})
				.catch(err => console.error(err))
		} else if (scriptType === 'css') {
			return loadCssFile(scriptConfig.src)
				.catch(err => console.error(err))
		}
	}

	const addInlineJavascript = (code) => {
		const javascriptTag = window.document.createElement('script')
		javascriptTag.type = 'text/javascript'
		const inlineScript = window.document.createTextNode(code)
		javascriptTag.appendChild(inlineScript)
		window.document.body.appendChild(javascriptTag)
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
			const javascriptTag = window.document.createElement('script')
			javascriptTag.src = url

			javascriptTag.onload = () => {
				loadedScripts[url] = 'done'
				resolve(window.document)
			}

			javascriptTag.onerror = () => {
				loadedScripts[url] = 'error'
				reject(window.document)
			}

			window.document.body.appendChild(javascriptTag)
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
			const styleLink = window.document.createElement('link')
			styleLink.type = 'text/css'
			styleLink.rel = 'stylesheet'
			styleLink.href = url

			styleLink.onload = () => {
				resolve(window.document)
				loadedStyles[url] = 'done'
			}

			styleLink.onerror = () => {
				reject(window.document)
				loadedStyles[url] = 'error'
			}

			window.document.getElementsByTagName('head')[0].appendChild(styleLink)
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
		loadCssFile,
		reset,
		scripts,
		loadedScripts,
		loadedStyles
	}
}