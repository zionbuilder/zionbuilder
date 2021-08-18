import {
	pageLoad,
	pageScroll,
	pageClick,
	exitIntent,
	selectorClick
} from './triggers/index.js'

const modals = []
const defaultTriggers = [
	pageLoad,
	pageScroll,
	pageClick,
	exitIntent,
	selectorClick
]

export function createModal(selector, options = {}) {
	let instance
	options = {
		triggers: [],
		closeOnBackdropClick: true,
		...options
	}

	function open() {
		selector.classList.add('zb-modal--open')
	}

	if (options.closeOnBackdropClick) {
		selector.addEventListener('click', closeOnBackdrop)
	}

	function close() {
		selector.classList.remove('zb-modal--open')
	}

	function destroy() {
		const modalIndex = modals.indexOf(instance)
		if (modalIndex !== -1) {
			modals.slice(modalIndex, 1)
		}

		if (options.closeOnBackdropClick) {
			selector.removeEventListener('click', closeOnBackdrop)
		}
	}

	function closeOnBackdrop(e) {
		if (e.target === selector) {
			close()
		}
	}

	instance = {
		open,
		close,
		destroy,
		selector
	}

	if (options.triggers) {
		options.triggers.forEach(triggerConfig => {
			const {
				type,
				fn,
				options
			} = triggerConfig

			if (typeof fn === 'function') {
				fn(instance, options)
			} else {
				const triggerConfig = defaultTriggers.find(trigger => trigger.name === type)
				if (triggerConfig) {
					const {
						fn
					} = triggerConfig
					fn(instance, options)
				}
			}
		})
	}

	modals.push(instance)

	return instance
}

export function getModalInstance(selector = null) {
	if (null === selector) {
		return modals[modals.length - 1]
	}

	return modals.find(modal => modal.selector = selector)
}

export function openModal(selector = null) {
	const modalInstance = getModalInstance(selector)

	if (modalInstance) {
		modalInstance.open()
	}
}

export function closeModal(selector = null) {
	const modalInstance = getModalInstance(selector)
	if (modalInstance) {
		modalInstance.close()
	}
}
