<template>
	<ElementLoading v-if="loading" />

	<component
		v-else-if="isActive && !(data.options._isVisible === false && isPreviewMode)"
		:is="component"
		class="znpb-element__wrapper zb-element"
		:id="`${elementCssId}`"
		:options="options"
		:data="data"
		@mouseenter="onMouseEnter"
		@mouseleave="onMouseLeave"
		@click="onElementClick"
		@dblclick="editElement"
		@contextmenu="showContextMenu"
		v-bind="getExtraAttributes"
	>

		<ElementToolbox
			slot="start"
			v-if="canShowToolbox"
			:data="data"
			:parentUid="parentUid"
			:can-hide-toolbox.sync="canHideToolbox"
			:is-toolbox-dragging.sync="isToolboxDragging"
		/>

		<!-- ELEMENT VIDEO HERE -->
		<VideoBackground
			slot="start"
			v-if="videoConfig"
			:video-config="videoConfig"
		/>

		<ElementStyles
			slot="start"
			:styles="customCSS"
		/>

		<transition
			name="znpb-fade"
			slot="end"
		>
			<div
				class="znpb-hidden-element-container"
				v-if="data.options._isVisible === false"
			>

				<div class="znpb-hidden-element-placeholder">
					<Icon
						icon="eye"
						@click.stop="restoreHiddenElement"
					>
					</Icon>
				</div>
			</div>
		</transition>
	</component>
</template>

<script>
// Utils
import { mapGetters, mapActions } from 'vuex'
import { debounce } from 'lodash-es'
import { generateElements, getStyles, getOptionValue, camelCase, clearTextSelection } from '@zb/utils'
import importCSS from '@zionbuilder/importcss'
import ElementToolbox from './ElementToolbox/ElementToolbox.vue'
import ElementStyles from './ElementStyles.vue'
import ElementLoading from './ElementLoading.vue'
import VideoBackground from './VideoBackground.vue'
import { applyFilters } from '@zb/hooks'
import Options from '../Options'

// Components
import ServerComponent from './ServerComponent.vue'
import { trigger } from '@zb/hooks'

export default {
	name: 'Element',
	provide () {
		const elementInfo = {}

		Object.defineProperty(elementInfo, 'data', {
			enumerable: true,
			get: () => this.data
		})

		Object.defineProperty(elementInfo, 'parentUid', {
			enumerable: true,
			get: () => this.parentUid
		})

		Object.defineProperty(elementInfo, 'elementModel', {
			enumerable: true,
			get: () => this.elementModel
		})

		Object.defineProperty(elementInfo, 'options', {
			enumerable: true,
			get: () => this.options
		})

		Object.defineProperty(elementInfo, 'elementInstance', {
			enumerable: true,
			get: () => this
		})

		return {
			elementInfo
		}
	},
	components: {
		ElementToolbox,
		VideoBackground,
		ElementLoading,
		ElementStyles
	},
	props: {
		uid: {
			type: String,
			required: true
		},
		data: {
			type: Object,
			required: true
		},
		parentUid: {
			type: String,
			required: true
		}
	},
	data () {
		return {
			loading: false,
			options: {},
			optionsWithDefaults: {},
			isActive: false,
			component: null,
			// From base component
			showToolbox: false,
			canHideToolbox: true,
			isToolboxDragging: false,
			toolboxWatcher: null,
			renderAttributes: {},
			customCSS: '',
			registeredEvents: {}
		}
	},
	created () {
		this.setupModel(this.data.options)

		this.getElementComponent()
		this.isActive = true

		if (Array.isArray(this.elementModel.content_composition) && this.elementModel.content_composition.length > 0) {
			if (this.data.content.length === 0 && Object.keys(this.options).length === 0) {
				const childElements = generateElements(this.elementModel.content_composition)

				this.insertElements({
					parentUid: this.data.uid,
					index: 0,
					childElements: childElements.childElements,
					parentElements: childElements.parentElements
				})

				// Check to see if the element has slots
				if (childElements.slots) {
					this.attachSlots({
						elementData: this.data,
						slots: childElements.slots
					})
				}
			}
		}
	},
	mounted () {
		this.$nextTick(() => {
			// Wait 100ms so all childs are rendered
			setTimeout(() => {
				trigger(`element/mounted`, this.getDefaultEventResponse())
			}, 100)
		})
	},
	beforeUnmount () {
		this.trigger('destroyed')
	},
	watch: {
		'data.options': {
			handler (newValue, oldValue) {
				this.setupModel(newValue)
				this.debounceUpdate()
			},
			deep: true
		},
		'data.content' (newValue, oldValue) {
			this.debounceUpdate()
		},
		shouldScrollIntoView (scrollInViewport) {
			if (scrollInViewport) {
				this.$el.scrollIntoView({
					behavior: 'smooth'
				})

				this.setElementFocus({
					...this.getElementFocus,
					scrollIntoView: false
				})

				setTimeout(() => {
					this.setRightClickMenu({
						previewScrollTop: window.pageYOffset
					})
				}, 1000)
			}
		}
	},
	computed: {
		...mapGetters([
			'getElementOptionValue',
			'getElementFocus',
			'getElementById',
			'isPreviewMode',
			'getRightClickMenu',
			'getCuttedElement'
		]),
		elementModel () {
			return this.getElementById(this.data.element_type)
		},
		stylesConfig () {
			return this.options._styles || {}
		},
		getClasses () {
			const elementClass = camelCase(this.data.element_type)
			const classes = {
				[`zb-el-${elementClass}`]: true,
				[`znpb-element__wrapper--toolbox-dragging`]: this.isToolboxDragging,
				'znpb-element__wrapper--cutted': this.getCuttedElement && this.data.uid === this.getCuttedElement.uid,
				'znpb-element--loading': this.loading
			}

			// Add animation classes
			const appearAnimation = (this.options._advanced_options || {})._appear_animation
			if (appearAnimation) {
				classes['animated'] = true
				classes[appearAnimation] = true
			}

			if (this.stylesConfig.wrapper) {
				const wrapperConfig = this.stylesConfig.wrapper
				if (wrapperConfig.classes) {
					wrapperConfig.classes.forEach(classSelector => {
						classes[classSelector] = true
					})
				}
			}

			// Add classes added by render attributes
			const wrapperClasses = typeof (this.renderAttributes.wrapper || {}).class !== 'undefined' ? this.renderAttributes.wrapper.class : []
			wrapperClasses.forEach(cssClass => {
				classes[cssClass] = true
			})

			return classes
		},
		getExtraAttributes () {
			let attributes = this.renderAttributes.wrapper || {}

			// Add render attributes classes
			return {
				...attributes,
				class: this.getClasses,
				api: {
					getStyleClasses: this.getStyleClasses
				}
			}
		},
		isElementVisible () {
			return this.data.options._isVisible !== false
		},
		canShowToolbox () {
			return this.isElementVisible && this.showToolbox && !this.isPreviewMode && !this.elementModel.is_child
		},
		elementCssId () {
			return (this.options._advanced_options || {})._element_id || this.data.uid
		},
		shouldScrollIntoView () {
			return this.getElementFocus && this.getElementFocus.uid === this.data.uid && this.getElementFocus.scrollIntoView
		},
		canShowElement () {
			if (this.isPreviewMode) {
				return !(this.data.options._isVisible === false)
			}
			return true
		},
		videoConfig () {
			return getOptionValue(this.options, '_styles.wrapper.styles.default.default.background-video', {})
		}
	},
	methods: {
		...mapActions([
			'insertElements',
			'attachSlots',
			'setActiveElement',
			'saveElementsOrder',
			'updateElementOptionValue',
			'setRightClickMenu',
			'setElementFocus'
		]),
		debounceUpdate: debounce(function () {
			this.$nextTick(() => {
				this.trigger('updated')
			})
		}),
		setupModel (model) {
			const schema = this.elementModel.options || {}
			const cssSelector = `#${this.elementCssId}`
			const optionsInstance = new Options(schema, model, cssSelector, {
				onLoadingStart: this.onLoadingStart,
				onLoadingEnd: this.onLoadingEnd
			})

			let { options, renderAttributes, customCSS } = optionsInstance.parseData()

			if (this.elementModel.style_elements) {
				Object.keys(this.elementModel.style_elements).forEach(styleId => {
					if (options._styles && options._styles[styleId] && options._styles[styleId].styles) {
						const styleConfig = this.elementModel.style_elements[styleId]
						const formattedSelector = styleConfig.selector.replace('{{ELEMENT}}', `#${this.elementCssId}`)
						customCSS += getStyles(formattedSelector, options._styles[styleId].styles)
					}
				})
			}

			// Filter the custom css
			customCSS = applyFilters('zionbuilder/element/custom_css', customCSS, optionsInstance, this)

			this.options = options
			this.renderAttributes = renderAttributes
			this.customCSS = customCSS

			// Add custom css classes
			this.applyCustomClassesToRenderTags()
		},
		applyCustomClassesToRenderTags () {
			const elementSavedStyles = getOptionValue(this.options, '_styles', {})
			const stylesConfig = this.elementModel.style_elements

			Object.keys(elementSavedStyles).forEach(styleConfigId => {
				const { classes } = elementSavedStyles[styleConfigId]

				if (typeof stylesConfig[styleConfigId] !== 'undefined') {
					const { render_tag: renderTag } = stylesConfig[styleConfigId]
					if (renderTag && classes && classes.length > 0) {
						classes.forEach(cssClass => {
							this.addRenderAttribute(renderTag, 'class', cssClass)
						})
					}
				}
			})
		},

		addRenderAttribute (tagId, attribute, value, replace = false) {
			if (!this.renderAttributes[tagId]) {
				this.renderAttributes[tagId] = {}
			}

			const currentAttributes = this.renderAttributes[tagId]

			if (!currentAttributes[attribute]) {
				currentAttributes[attribute] = []
			}

			if (replace) {
				currentAttributes[attribute] = [value]
			} else {
				currentAttributes[attribute].push(value)
			}
		},

		onLoadingStart () {
			if (!this.loading) {
				this.loading = true
			}
		},
		onLoadingEnd () {
			if (this.loading) {
				this.loading = false
			}
		},
		getOptionSchemaFromPath (optionPath) {
			if (!this.elementModel.options) {
				return false
			}

			const pathArray = optionPath.split('.')
			let activeSchema = this.elementModel.options
			const pathLength = pathArray.length
			let foundSchema = false

			pathArray.forEach((pathItem, i) => {
				if (i + 1 === pathLength) {
					foundSchema = activeSchema[pathItem]
				}

				if (typeof activeSchema[pathItem] !== 'undefined') {
					activeSchema = activeSchema[pathItem]
				}
			})

			return foundSchema
		},

		async getElementComponent () {
			await this.loadElementAssets()
			const component = window.ZionBuilderApi.ElementsManager.getElementComponent(this.data.element_type)

			if (component) {
				this.component = component
			} else {
				this.component = ServerComponent
			}
		},

		loadElementAssets () {
			return Promise.all(
				[
					...Object.keys(this.elementModel.scripts).map(scriptHandle => {
						// Script can sometimes be false
						const scriptConfig = this.elementModel.scripts[scriptHandle]

						// Set the handle if it was not provided
						scriptConfig.handle = scriptConfig.handle ? scriptConfig.handle : scriptHandle

						if (scriptConfig.src) {
							return this.$zb.preview.scripts.loadScript(scriptConfig, window.document)
						}
					}),
					...Object.keys(this.elementModel.styles).map(scriptHandle => {
						// Script can sometimes be false
						const scriptConfig = this.elementModel.styles[scriptHandle]

						// Set the handle if it was not provided
						scriptConfig.handle = scriptConfig.handle ? scriptConfig.handle : scriptHandle

						if (scriptConfig.src) {
							return this.$zb.preview.scripts.loadScript(scriptConfig, window.document)
						}
					})
				]
			)
		},

		/**
		 * On Mouse enter
		 */
		onMouseEnter () {
			this.showToolbox = true
		},

		/**
		 * On element mouseleave
		 */
		onMouseLeave () {
			if (this.canHideToolbox) {
				this.showToolbox = false
			} else {
				if (!this.toolboxWatcher) {
					// Set a watcher so we can hide the toolbox
					this.toolboxWatcher = this.$watch('canHideToolbox', (newValue, oldValue) => {
						if (newValue) {
							this.showToolbox = false
							this.toolboxWatcher()
							this.toolboxWatcher = null
						}
					})
				}
			}
		},
		/**
		 * On context menu open
		 */
		showContextMenu (event) {
			if (!this.isPreviewMode) {
				event.preventDefault()
				event.stopPropagation()
				this.setElementFocus({
					uid: this.data.uid,
					parentUid: this.parentUid,
					insertParent: this.elementModel.wrapper ? this.data.uid : this.parentUid
				})

				this.setRightClickMenu({
					uid: this.data.uid,
					visibility: true,
					previewScrollTop: window.pageYOffset,
					initialScrollTop: parseInt(window.pageYOffset),
					position: {
						top: event.clientY,
						left: event.clientX
					},
					source: 'preview'
				})
			}
		},

		onElementClick (event) {
			// don't process if this was already handled
			if (window.ZionBuilderApi.editor.ElementFocusMarshall.isHandled || this.elementModel.is_child) {
				return
			}

			// Reset handled click
			setTimeout(() => {
				window.ZionBuilderApi.editor.ElementFocusMarshall.reset()
			}, 0)

			window.ZionBuilderApi.editor.ElementFocusMarshall.handle()

			if (this.getRightClickMenu && this.getRightClickMenu.visibility) {
				this.setRightClickMenu({
					visibility: false
				})
			}

			this.setElementFocus({
				uid: this.data.uid,
				parentUid: this.parentUid,
				insertParent: this.elementModel.wrapper ? this.data.uid : this.parentUid
			})
		},
		/**
		 * Edit element
		 *
		 * Triggered when double click on the element
		 */
		editElement (event) {
			event.stopPropagation()

			if (!this.isPreviewMode) {
				this.setActiveElement(this.data.uid)
				this.$zb.panels.openPanel('PanelElementOptions')

				// Clear text selection that may appear
				clearTextSelection(window)
				clearTextSelection(window.parent)
			}
		},

		restoreHiddenElement () {
			this.updateElementOptionValue({
				elementUid: this.data.uid,
				path: '_isVisible',
				newValue: true,
				type: 'visibility'
			})
		},
		/**
		 * Register an event for an action
		 */
		on (eventType, callback) {
			if (typeof this.registeredEvents[eventType] === 'undefined') {
				this.registeredEvents[eventType] = []
			}

			this.registeredEvents[eventType].push(callback)
		},
		/**
		 * Remove an event listener
		 */
		off (eventType, callback) {
			if (typeof this.registeredEvents[eventType] === 'undefined' && this.registeredEvents[eventType].includes(callback)) {
				const callbackIndex = this.registeredEvents[eventType].indexOf(callback)
				if (callbackIndex !== -1) {
					this.registeredEvents[eventType].splice(callbackIndex, 1)
				}
			}
		},
		/**
		 * Remove all events
		 */
		offAll () {
			this.registeredEvents = {}
		},
		getDefaultEventResponse () {
			return {
				elementType: this.data.element_type,
				element: this.$el,
				options: this.options || {},
				elementUid: this.data.uid,

				// Actions that the user can subscribe to
				on: this.on,
				off: this.off,
				offAll: this.offAll
			}
		},
		trigger (eventType, data) {
			const defaultData = this.getDefaultEventResponse()
			if (typeof this.registeredEvents[eventType] !== 'undefined') {
				this.registeredEvents[eventType].forEach(callbackFunction => {
					callbackFunction({
						...defaultData,
						...data
					})
				})
			}
		},
		getStyleClasses (styleId, extraClasses = {}) {
			const classes = {}

			if (this.stylesConfig[styleId]) {
				const elementStylesClasses = this.stylesConfig[styleId]
				if (elementStylesClasses.classes) {
					elementStylesClasses.classes.forEach(classSelector => {
						classes[classSelector] = true
					})
				}
			}

			return classes
		}
	}
}
</script>

<style lang="scss">
.znpb-element {
	&--loading {
		opacity: .2;
	}

	&--needs-data {
		padding: 10px;
		color: #4e4e4e;
		text-align: center;
		background: #eaeaea;
		border: 1px solid #e0e0e0;
	}
}

.znpb-element__wrapper--cutted {
	opacity: .2;
	pointer-events: none;
}
@keyframes znpb-scale-down {
	0% {
		transform: scale(1.05);
		opacity: 0;
	}
	100% {
		transform: scale(1);
		opacity: 1;
	}
}

.znpb-element__wrapper--panel-hovered {
	box-shadow: 0 0 0 2px rgba($secondary, .3);
}
.znpb-element__wrapper {
	position: relative;
	transition: opacity .2s;

	.znpb-hidden-element-container {
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 100%;
		height: 100%;
		background: rgba(255, 255, 255, .7);
	}

	&:hover, &--toolbox-dragging {
		position: relative;
	}

	&:hover {
		cursor: move;
	}

	.znpb-hidden-element-placeholder {
		position: relative;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 34px;
		height: 34px;
		font-size: 18px;
		&:before {
			@extend %iconbg;
			background: $red;
		}
		.znpb-editor-icon-wrapper {
			position: relative;
			color: $surface;
			cursor: pointer;
		}
		&:hover {
			&:before {
				transform: scale(1.1);
			}
		}
	}
}
.znpb-element-utilities__margin-top-helper, .znpb-element-utilities__margin-right-helper, .znpb-element-utilities__margin-bottom-helper, .znpb-element-utilities__margin-left-helper {
	position: absolute;
}

.znpb-element-utilities__margin-top-helper, .znpb-element-utilities__margin-bottom-helper {
	left: 0;
	width: 100%;
	min-height: 2px;
	cursor: n-resize;
}

.znpb-element-utilities__margin-left-helper, .znpb-element-utilities__margin-right-helper {
	top: 0;
	width: 2px;
	height: 100%;
	cursor: e-resize;
}

.znpb-element-utilities__margin-top-helper {
	top: 0;
}
.znpb-element-utilities__margin-bottom-helper {
	bottom: 0;
}
.znpb-element-utilities__margin-left-helper {
	left: 0;
}
.znpb-element-utilities__margin-right-helper {
	right: 0;
}
.znpb-element-utilities__options {
	&-wrapper {
		position: absolute;
		top: 0;
		left: 0;
		display: flex;
		color: #fff;
		background: blue;
	}
	&-item {
		padding: 10px;
	}
}
</style>
