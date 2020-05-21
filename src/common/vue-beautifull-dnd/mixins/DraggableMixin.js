import {
	HostsManager,
	EventsManager
} from '../manager'
import memoizeOne from 'memoize-one'
import { getBox } from 'css-box-model'
import rafSchd from 'raf-schd'

const hosts = HostsManager()
const eventsManager = EventsManager()

const getOffset = (currentDocument) => {
	const frameElement = hosts.getIframes().find((iframe) => {
		return iframe.contentDocument === currentDocument
	})

	if (undefined !== frameElement) {
		const { left, top } = frameElement.getBoundingClientRect()
		return {
			left,
			top
		}
	}

	return {
		left: 0,
		top: 0
	}
}

const memoizedGetOffset = memoizeOne(getOffset)

const onMove = (event, initialX, initialY, el) => {
	let { clientX, clientY } = event

	const { document: currentDocument } = event.view

	// Calculate offset in case of iframes
	if (document !== currentDocument) {
		const { left, top } = memoizedGetOffset(currentDocument)
		clientX += left
		clientY += top
	}

	// Calculate moves
	const movedX = clientX - initialX
	const movedY = clientY - initialY

	// Apply style
	el.style.transform = `translate3d(${movedX}px, ${movedY}px, 0)`
}
// Move callback
const memoizedMove = memoizeOne(onMove)
const move = rafSchd((event, initialX, initialY, el) => memoizedMove(event, initialX, initialY, el))

export default {
	props: {
		disabled: {
			type: Boolean,
			required: false,
			default: false
		}
	},
	data () {
		return {
			draggableHandle: null,
			initialX: null,
			initialY: null,
			currentDocument: null
		}
	},
	mounted () {
		this.draggableHandle = this.$el

		// Add drag events
		if (!this.disabled) {
			this.enableDraggable()
		}
	},
	methods: {
		enableDraggable () {
			this.draggableHandle.addEventListener('mousedown', this.attachEvents)
		},

		disableDraggable () {
			this.draggableHandle.removeEventListener('mousedown', this.attachEvents)
		},

		attachEvents (event) {
			if (eventsManager.isHandled()) {
				return
			}
			eventsManager.handle()

			// Set dimensions
			this.dimensions = getBox(this.$el)

			// Set initial position
			const { clientX, clientY } = event
			this.initialX = clientX
			this.initialY = clientY

			// Set current document so we know if we start from iframe or not
			this.currentDocument = document

			// Dimensions
			const { marginBox, paddingBox } = this.dimensions

			// Make transition faster
			this.$el.style.willChange = 'transform'
			this.$el.style.pointerEvents = 'none'
			this.$el.style.position = 'fixed'
			this.$el.style.left = `${marginBox.left}px`
			this.$el.style.top = `${marginBox.top}px`
			this.$el.style.width = `${paddingBox.width}px`
			this.$el.style.height = `${paddingBox.height}px`

			hosts.fetchHosts()

			hosts.getHosts().forEach((host) => {
				host.addEventListener('mousemove', this.onDraggableMouseMove)
				host.addEventListener('mouseup', this.finishDrag)
			})
		},

		detachEvents () {
			hosts.getHosts().forEach((host) => {
				host.removeEventListener('mousemove', this.onDraggableMouseMove)
				host.removeEventListener('mouseup', this.finishDrag)
			})

			eventsManager.reset()
		},

		finishDrag () {
			this.$el.style.willChange = null
			this.$el.style.pointerEvents = null
			this.$el.style.position = null
			this.$el.style.left = null
			this.$el.style.right = null
			this.$el.style.width = null
			this.$el.style.height = null

			this.currentDocument = null
			this.initialX = null
			this.initialY = null
			this.dimensions = null

			this.$el.style.transform = null

			this.detachEvents()
		},

		onDraggableMouseMove (event) {
			move(event, this.initialX, this.initialY, this.$el)
		}
	},
	beforeDestroy () {
		this.disableDraggable()
		this.detachEvents()
	}
}
