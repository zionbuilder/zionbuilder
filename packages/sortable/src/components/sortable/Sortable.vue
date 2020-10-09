<template>
	<component
		:is="tag"
		@[mousedown]="onMouseDown"
		@dragstart="onDragStart"
		:class="{
			[`vuebdnd__placeholder-empty-container`]: canShowEmptyPlaceholder
		}"
	>
		<slot name="start" />

		<slot />

		<slot
			name="empty-placeholder"
			v-if="canShowEmptyPlaceholder"
		/>

		<slot name="end" />
	</component>
</template>

<script>
import Vue from '@zionbuilder/vue'

import {
	HostsManager,
	EventsManager
} from '../../manager'
import {
	closest
} from '../../utils'
import {
	EventScheduler,
	MoveEvent,
	StartEvent,
	EndEvent,
	ChangeEvent,
	DropEvent
} from '../../events'
import memoizeOne from 'memoize-one'
import { getBox } from 'css-box-model'

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

// Finish callback
export default {
	name: 'Sortable',
	props: {
		modelValue: {
			required: false,
			type: Array,
			default () {
				return null
			}
		},
		tag: {
			type: String,
			required: false,
			default: 'div'
		},
		dragTreshold: {
			type: Number,
			required: false,
			default: 5
		},
		dragDelay: {
			type: Number,
			required: false,
			default: 0
		},
		handle: {
			type: String,
			required: false,
			default: null
		},
		draggable: {
			type: String,
			required: false,
			default: '> *'
		},
		disabled: {
			type: Boolean,
			required: false,
			default: false
		},
		group: {
			type: [String, Object, Array],
			required: false,
			default: null
		},
		sort: {
			type: Boolean,
			required: false,
			default: true
		},
		placeholder: {
			type: Boolean,
			required: false,
			default: true
		},
		cssClasses: {
			type: Object,
			required: false,
			default () {
				return {}
			}
		},
		revert: {
			type: Boolean,
			required: false,
			default: true
		},
		axis: {
			type: String,
			required: false,
			default: null
		}
	},
	data () {
		return {
			sortableContainer: null,
			draggedItem: null,
			dragging: null,
			eventScheduler: null,
			dragItemInfo: null,
			sortableItems: [],
			initialX: null,
			initialY: null
		}
	},
	computed: {
		mousedown () {
			return this.disabled ? null : 'mousedown'
		},
		canShowEmptyPlaceholder () {
			console.log(this.sortableItems);
			return this.sortableItems.length === 0 || (this.dragging && this.sortableItems.length === 1)
		},
		getPlaceholder () {
			if (this.$slots.placeholder && this.$slots.placeholder.length > 0) {
				const placeholder = new Vue({
					render: (h) => {
						return h('div', {
							class: {
								'znpb-sortable__placeholder': true
							}
						}, [this.$slots.placeholder])
					}
				})

				placeholder.$mount()

				return placeholder.$el
			}

			return false
		},
		getHelper () {
			if (this.$slots.helper && this.$slots.helper.length > 0) {
				const helper = new Vue({
					render: (h) => {
						return h('div', {
							class: {
								'zion-editor__sortable-helper': true
							}
						}, [this.$slots.helper])
					}
				})

				helper.$mount()

				return helper.$el
			}

			return false
		},

		sortableInfo () {
			return {
				group: this.group,
				getInfoFromTarget: this.getInfoFromTarget,
				canPut: this.canPut
			}
		},
		getNodes () {
			return this.$slots.default.map((vNode) => {
				return vNode.elm
			}).filter(HTMLElement => HTMLElement !== undefined)
		},
		groupInfo () {
			let group = this.group

			if (!group || typeof group !== 'object') {
				group = {
					name: group
				}
			}

			return group
		},
		computedCssClasses () {
			const defaultClasses = {
				// Body when dragging
				'body': 'vuebdnd-draggable--active',
				// Element that initialised dragging
				'source': 'vuebdnd__source--dragging',
				// Container from which the draggable started
				'source:container': 'vuebdnd__source-container--dragging',
				// Helper that follows the mouse
				'helper': 'vuebdnd__helper',
				// Placeholder that displays the position of dragged element
				'placeholder': 'vuebdnd__placeholder',
				// Container that the mouse is currently hovering
				'placeholder:container': 'vuebdnd__placeholder-container'
			}

			return {
				...defaultClasses,
				...this.cssClasses
			}
		}
	},
	mounted () {
		this.setSortableItems()
		this.eventScheduler = EventScheduler(this.getEvents())
		this.sortableContainer.__SORTABLE_INFO__ = this.sortableInfo
	},
	created () {
		this.movePlaceholderMemoized = memoizeOne(this.movePlaceholder)
	},

	watch: {
		modelValue (newValue) {
			this.setSortableItems()
		}
	},
	methods: {
		getCssClass (cssClass) {
			return this.computedCssClasses[cssClass] || null
		},
		setSortableItems () {
			const defaultSlots = this.$slots.default()
			let isTransitionMode = false

			// TODO: deomment this
			// Check to see if we have a transition group
			// if (defaultSlots && defaultSlots.length > 0) {
			// 	const { componentOptions } = defaultSlots[0]
			// 	if (componentOptions) {
			// 		isTransitionMode = this.componentIsTransitionGroup(componentOptions.tag)
			// 	}
			// }

			const contentContainer = isTransitionMode ? defaultSlots[0].componentInstance.$slots.default : defaultSlots
			this.sortableContainer = isTransitionMode ? defaultSlots[0].elm : this.$el

			if (contentContainer) {
				this.$nextTick(() => {
					this.sortableItems = contentContainer
						.filter(vnode => vnode.component)
						.map(vNode => vNode)
				})
				console.log(this.sortableItems);
			} else {
				this.sortableItems = []
			}
		},

		/**
		 * Prevent HTML 5 DragAndDrop
		 */
		onDragStart (event) {
			event.preventDefault()
		},

		getEvents () {
			return {
				onStart: [this.onDragStart],
				onMove: this.onMouseMove
			}
		},

		onMouseDown (event) {
			// Don't proceed if the event was already handled
			if (eventsManager.isHandled()) {
				return
			}

			// Check if we should start the dragg event
			if (event.button !== 0 || event.ctrlKey || event.metaKey) {
				return
			}

			// Don't proceed if we are on an editable content element
			if (event.target.isContentEditable) {
				return
			}

			// Check handle
			this.draggedItem = closest(event.target, this.draggable, this.sortableContainer)

			// Don't proceed if the dragged item is not part of sortable nodes
			const sortableDomElements = this.sortableItems.map(vNode => vNode.elm).filter(el => el && el.nodeType === 1)
			if (!this.draggedItem || !sortableDomElements.includes(this.draggedItem)) {
				return
			}

			// Check if we have a handle and it was clicked
			if (this.handle && !closest(event.target, this.handle)) {
				return
			}

			// Get information about dragged item
			this.dragItemInfo = this.getInfoFromTarget(this.draggedItem)

			// Don't proceed if we cannot pull the item
			if (!this.canPull()) {
				return
			}

			// Flag for drag delay
			this.dragDelayCompleted = !this.dragDelay

			if (this.dragDelay) {
				clearTimeout(this.dragTimeout)
				this.dragTimeout = setTimeout(() => {
					this.dragDelayCompleted = true
				}, this.dragDelay)
			}

			// Set dimensions
			this.dimensions = getBox(this.draggedItem)

			// Set initial position
			const { clientX, clientY } = event
			this.initialX = clientX
			this.initialY = clientY

			// Set current document so we know if we start from iframe or not
			this.currentDocument = document

			hosts.fetchHosts()

			hosts.getHosts().forEach((host) => {
				host.addEventListener('mousemove', this.onDraggableMouseMove)
				host.addEventListener('mouseup', this.finishDrag)
			})

			eventsManager.handle()
		},

		disableDraggable () {
			this.draggableHandle.removeEventListener('mousedown', this.attachEvents)
		},
		detachEvents () {
			hosts.getHosts().forEach((host) => {
				host.removeEventListener('mousemove', this.onDraggableMouseMove)
				host.removeEventListener('mouseup', this.finishDrag)
			})

			eventsManager.reset()
		},

		startDrag () {
			// Trigger move event
			const startEvent = new StartEvent(this.dragItemInfo)

			// Send the event
			this.$emit('start', startEvent)

			if (startEvent.isCanceled()) {
				// Finish dragging
				this.finishDrag()
				return
			}

			document.body.style.userSelect = 'none'

			// Dimensions
			const { marginBox, paddingBox } = this.dimensions

			// Attach placeholder if we are not cloning it
			this.attachPlaceholder()

			// Attach Helper
			this.attachHelper()

			// Add css classes
			this.addCssClass('body')
			this.addCssClass('source')
			this.addCssClass('source:container')
			this.addCssClass('placeholder:container')

			// Make transition faster
			this.helperNode.style.willChange = 'transform'
			this.helperNode.style.zIndex = 99999
			this.helperNode.style.pointerEvents = 'none'
			this.helperNode.style.position = 'fixed'

			// Only set dimensions if the helper is not user generated
			if (this.getHelper) {
				this.draggedItem.style.display = 'none'
				const { width, height } = this.helperNode.getBoundingClientRect()
				this.helperNode.style.left = `${this.initialX - width / 2}px`
				this.helperNode.style.top = `${this.initialY - height / 2}px`
			} else {
				this.helperNode.style.left = `${marginBox.left}px`
				this.helperNode.style.top = `${marginBox.top}px`
				this.helperNode.style.width = `${paddingBox.width}px`
				this.helperNode.style.height = `${paddingBox.height}px`
			}
		},

		applyCssClass (type, action) {
			const cssClass = this.getCssClass(type)
			let node = null

			// Don't proceed if we do not have a valid class
			if (!cssClass) {
				return
			}

			if (type === 'body') {
				node = document.body
			} else if (type === 'helper') {
				node = this.helperNode
			} else if (type === 'placeholder') {
				node = this.placeholderNode
			} else if (type === 'source') {
				node = this.draggedItem
			} else if (type === 'source:container') {
				node = this.draggedItem.parentNode
			} else if (type === 'placeholder:container') {
				node = this.placeholderNode.parentNode
			}

			if (node) {
				node.classList[action](cssClass)
			}
		},

		addCssClass (type) {
			this.applyCssClass(type, 'add')
		},

		removeCssClass (type) {
			this.applyCssClass(type, 'remove')
		},

		attachHelper () {
			if (this.getHelper) {
				this.helperNode = this.getHelper
				// this.sortableContainer.insertBefore(this.helperNode, this.draggedItem)
				this.draggedItem.insertAdjacentElement('afterend', this.helperNode)
			} else if (this.groupInfo.pull === 'clone') {
				const clone = this.draggedItem.cloneNode(true)
				this.sortableContainer.insertBefore(clone, this.draggedItem)
				this.helperNode = clone
			} else {
				this.helperNode = this.draggedItem
			}

			this.addCssClass('helper')
		},

		detachHelper () {
			if (this.helperNode) {
				// Remove css class
				this.removeCssClass('helper')

				if (this.getHelper || this.groupInfo.pull === 'clone') {
					// Remove helper
					const helperContainer = this.helperNode.parentNode
					// There are cases where the placeholder is not yet present in the dom
					if (helperContainer) {
						helperContainer.removeChild(this.helperNode)
					}
				}
			}
		},

		attachPlaceholder () {
			if (!this.placeholder) {
				return
			}

			if (this.getPlaceholder) {
				this.placeholderNode = this.getPlaceholder
			} else {
				this.placeholderNode = this.draggedItem.cloneNode(true)
				this.placeholderNode.style.visibility = 'hidden'
			}

			if (this.placeholderNode && this.groupInfo.pull !== 'clone') {
				this.sortableContainer.insertBefore(this.placeholderNode, this.draggedItem)
			}

			this.addCssClass('placeholder')
		},

		detachPlaceholder () {
			if (this.placeholderNode) {
				// Remove placeholder css class
				this.removeCssClass('placeholder')

				const placeholderContainer = this.placeholderNode.parentNode
				// There are cases where the placeholder is not yet present in the dom
				if (placeholderContainer) {
					placeholderContainer.removeChild(this.placeholderNode)
				}
			}
		},

		finishDrag () {
			clearTimeout(this.dragTimeout)
			this.detachEvents()

			if (this.dragging) {
				document.body.style.userSelect = null
				// Add css class for body
				this.removeCssClass('body')
				this.removeCssClass('source')
				this.removeCssClass('source:container')
				this.removeCssClass('placeholder:container')

				// If the revert option is set to true the element will regain initial position
				if (this.revert) {
					this.helperNode.style.position = null
					this.helperNode.style.left = null
					this.helperNode.style.top = null
					this.helperNode.style.width = null
					this.helperNode.style.height = null
					this.helperNode.style.zIndex = null
					this.helperNode.style.transform = null
				}

				this.helperNode.style.willChange = null
				this.helperNode.style.pointerEvents = null
				this.helperNode.style.zIndex = null

				// Remove placeholder
				this.detachPlaceholder()
				this.detachHelper()

				if (this.getHelper) {
					this.draggedItem.style.display = null
				}
				const { from, to, startIndex, newIndex, placeBefore } = this.lastEvent.data

				// Check to see if a change was made
				if (from && to && newIndex !== -1) {
					// Send event for second list
					const toVm = this.getVmFromElement(to)

					// Update values if exists
					if (this.modelValue !== null) {
						let modifiedNewIndex = placeBefore ? newIndex : newIndex + 1
						if (from === to && startIndex !== newIndex) {
							this.updatePositionInList(startIndex, modifiedNewIndex)
						} else if (from !== to) {
							const item = this.modelValue[startIndex]
							// Send 2 events for each container
							// Remove from first list
							this.removeItemFromList(startIndex)
							toVm.addItemToList(item, modifiedNewIndex)
						}
					}

					// Send drop Event
					const dropEvent = new DropEvent({
						...this.lastEvent.data,
						toVm
					})
					toVm.$emit('drop', dropEvent)
				}

				// Trigger move event
				const endEvent = new EndEvent()

				// Send the event
				this.$emit('end', endEvent)
				this.eventScheduler.cancel()

				// Cleanup
				this.currentDocument = null
				this.initialX = null
				this.initialY = null
				this.dimensions = null
				this.draggedItem = null
				this.dragging = null
				this.dragDelayCompleted = null
				this.offset = 0
				this.dragItemInfo = null
			}
		},

		updatePositionInList (oldIndex, newIndex) {
			if (this.modelValue) {
				const list = [...this.modelValue]
				if (oldIndex >= newIndex) {
					list.splice(newIndex, 0, list.splice(oldIndex, 1)[0])
				} else {
					list.splice(newIndex - 1, 0, list.splice(oldIndex, 1)[0])
				}
				this.$emit('update:modelValue', list)
			}
		},

		addItemToList (item, index) {
			if (this.modelValue) {
				const list = [...this.modelValue]
				list.splice(index, 0, item)
				this.$emit('update:modelValue', list)
			}
		},

		removeItemFromList (index) {
			if (this.modelValue) {
				const list = [...this.modelValue]
				list.splice(index, 1)
				this.$emit('update:modelValue', list)
			}
		},

		onDraggableMouseMove (event) {
			if (this.dragging) {
				this.eventScheduler.move(event)
			} else {
				const { clientX, clientY } = event
				const xDistance = Math.abs(clientX - this.initialX)
				const yDistance = Math.abs(clientY - this.initialY)
				if (this.dragDelayCompleted && (xDistance >= this.dragTreshold || yDistance >= this.dragTreshold)) {
					this.dragging = true
					this.startDrag()
				}
			}
		},

		/**
		 * Returns information like HTMLElement, index and other usefull info from an HTMLElement
		 * @param {HTMLElement} target The target for which we need to get the info.
		 * @returns {object} The information about the requested target
		 */
		getInfoFromTarget (target) {
			const validItem = closest(target, this.draggable, this.sortableContainer)
			const sortableDomElements = this.sortableItems.map(vNode => vNode.elm).filter(el => el && el.nodeType === 1)
			const item = sortableDomElements.includes(validItem) ? validItem : false
			const index = sortableDomElements.indexOf(item)

			return {
				container: this.sortableContainer,
				item,
				index,
				newIndex: index,
				group: this.groupInfo
			}
		},

		/**
		 * Checks if the dragged item can be placed inside the current container
		 * @param {object} dragItemInfo The drag item info.
		 * @returns {boolean} If the dragged item can be placed inside current container
		 */
		canPut (dragItemInfo) {
			const groupInfo = this.groupInfo
			const dragGroupInfo = dragItemInfo.group
			const sameGroup = dragGroupInfo.name === groupInfo.name
			const put = groupInfo.put || null

			if (put === null && sameGroup) {
				return true
			} else if (put === null || put === false) {
				return false
			} else if (typeof put === 'function') {
				return put(dragItemInfo, groupInfo)
			} else {
				if (put === true) {
					return true
				} else if (typeof put === 'string') {
					return put === dragGroupInfo.name
				} else if (Array.isArray(put)) {
					return put.indexOf(dragGroupInfo.name) > -1
				}
			}

			return false
		},

		/**
		 * Checks if the target can be pulled from list
		 * @param {object} dragItemInfo The drag item info.
		 * @returns {boolean} If the dragged item can be placed inside current container
		 */
		canPull () {
			if (this.groupInfo.pull === false) {
				return false
			}

			return true
		},

		/**
		 * Returns true if provided component tag string is of type transition-group
		 *
		 */
		componentIsTransitionGroup (componentTag) {
			return ['transition-group', 'TransitionGroup'].includes(componentTag)
		},

		/**
		 * Return the VUE instance from target
		 */
		getVmFromElement ({ __vue__ }) {
			if (!__vue__ || !__vue__.$options || !this.componentIsTransitionGroup(__vue__.$options._componentTag)) {
				return __vue__
			}
			return __vue__.$parent
		},

		onMouseMove (event) {
			let { clientX, clientY } = event
			let offset = {
				left: 0,
				top: 0
			}
			const { document: currentDocument } = event.view

			// Calculate offset in case of iframes
			if (document !== currentDocument) {
				offset = memoizedGetOffset(currentDocument)
			}

			// Calculate moves
			const movedX = clientX + offset.left - this.initialX
			const movedY = clientY + offset.top - this.initialY

			// Apply style
			this.helperNode.style.transform = `translate3d(${movedX}px, ${movedY}px, 0)`

			// Set defaults
			let overItem = {
				container: null,
				item: null,
				index: -1
			}

			const target = currentDocument.elementFromPoint(clientX, clientY)
			if (target) {
				const to = closest(target, this.getSortableContainer)
				const sameContainer = to === this.sortableContainer

				if (sameContainer && !this.sort) {
					// Do nothing if we cannot sort inside same container
				} else if (to) {
					const targetVM = this.getVmFromElement(to)

					// check if we can drop
					const overItemInfo = targetVM.getInfoFromTarget(target)
					overItem = {
						...overItem,
						...overItemInfo
					}

					// Set draggable info for target
					this.dragItemInfo.to = overItem.container
					this.dragItemInfo.toItem = overItem.item

					if (overItem.container) {
						if (overItem.item) {
							const collisionInfo = this.collisionInfo(event, overItem.item, targetVM)
							this.dragItemInfo.placeBefore = collisionInfo.before
							const whereToPutPlaceholder = targetVM.getItemFromList(overItem.index)
							const nextSibling = whereToPutPlaceholder.nextElementSibling
							const insertBeforeElement = this.dragItemInfo.placeBefore ? whereToPutPlaceholder : nextSibling

							// Only move if it actually moved position
							this.movePlaceholderMemoized(overItem.container, insertBeforeElement, this.dragItemInfo.placeBefore)
							this.dragItemInfo.newIndex = overItem.index
						} else {
							if (targetVM.value && targetVM.value.length === 0) {
								// Empty sortable container
								this.movePlaceholderMemoized(overItem.container, null, this.dragItemInfo.placeBefore)
								this.dragItemInfo.newIndex = 0
							} else if (sameContainer && this.modelValue.length === 1) {
								this.movePlaceholderMemoized(overItem.container, null, this.dragItemInfo.placeBefore)
							}
						}
					}
				}
			}

			const {
				container: from,
				item,
				index:
				startIndex,
				to,
				newIndex,
				toItem,
				placeBefore
			} = this.dragItemInfo

			// Trigger move event
			const moveEvent = new MoveEvent({
				from,
				item,
				startIndex,
				to,
				newIndex,
				toItem,
				nativeEvent: event,
				placeBefore
			})

			// Send the event
			this.$emit('move', moveEvent)

			if (moveEvent.isCanceled()) {
				// Finish dragging
				this.finishDrag()
			}

			this.lastEvent = moveEvent
		},

		movePlaceholder (container, element, before) {
			// Remove css class from last container
			if (this.dragItemInfo.lastContainer !== container) {
				this.removeCssClass('placeholder:container')
			}

			// Move placeholder if we are allowed to move it
			if (this.placeholder) {
				container.insertBefore(this.placeholderNode, element)
			}

			if (this.dragItemInfo.lastContainer !== container) {
				this.addCssClass('placeholder:container')
			}

			const {
				container: from,
				item,
				index:
				startIndex,
				to,
				newIndex,
				toItem
			} = this.dragItemInfo

			// Trigger change
			const changeEvent = new ChangeEvent({
				from,
				item,
				startIndex,
				to,
				newIndex,
				toItem,
				before
			})

			this.dragItemInfo.lastContainer = container

			// Send the event
			this.$emit('change', changeEvent)
		},

		collisionInfo (event, overItem, targetVm) {
			const { clientX, clientY } = event
			const itemRect = overItem.getBoundingClientRect()
			const orientation = this.detectOrientation(targetVm)
			const center = orientation === 'horizontal' ? itemRect.width / 2 : itemRect.height / 2
			const before = orientation === 'horizontal' ? clientX < itemRect.left + center : clientY < itemRect.top + center

			return {
				before
			}
		},

		detectOrientation (targetVm) {
			return targetVm.axis || 'vertical'
		},

		getItemFromList (index) {
			const sortableDomElements = this.sortableItems.map(vNode => vNode.elm).filter(el => el && el.nodeType === 1)
			return sortableDomElements[index]
		},

		getSortableContainer (target) {
			return target && target.__SORTABLE_INFO__ && this.getVmFromElement(target).canPut(this.dragItemInfo)
		}
	}
}
</script>

<style lang="scss">
</style>
