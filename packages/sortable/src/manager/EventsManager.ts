export default () => {
	let handled = false

	const handle = () => {
		handled = true
	}

	const isHandled = () => {
		return handled
	}

	const reset = () => {
		handled = false
	}

	return {
		handle,
		isHandled,
		reset
	}
}
