<template>
	<ElementLoading v-if="loading" />

	<component
		v-else-if="elementComponent && !(element.isVisible === false && isPreviewMode)"
		:is="elementComponent"
		class="znpb-element__wrapper zb-element"
		:class="{'znpb-element__wrapper--panel-hovered': element.isHighlighted}"
		:id="`${element.elementCssId}`"
		:options="options"
		:data="element"
		@mouseenter="onMouseEnter"
		@mouseleave="onMouseLeave"
		@click.stop="onElementClick"
		@dblclick="editElement"
		@contextmenu.prevent.stop="showElementMenu"
		v-bind="getExtraAttributes"
	>

		<template #start>
			<ElementToolbox
				v-if="canShowToolbox"
				:element="element"
				:data="element"
				:parentUid="parentUid"
				:can-hide-toolbox.sync="canHideToolbox"
				:is-toolbox-dragging.sync="isToolboxDragging"
			/>

			<!-- ELEMENT VIDEO HERE -->
			<VideoBackground
				v-if="videoConfig"
				:video-config="videoConfig"
			/>

			<ElementStyles
				:styles="customCSS"
			/>
		</template>

		<template #end>
				<div
					class="znpb-hidden-element-container"
					v-if="element.isVisible === false"
				>

					<div class="znpb-hidden-element-placeholder">
						<Icon
							icon="eye"
							@click.stop="element.toggleVisibility()"
						>
						</Icon>
					</div>
				</div>

			<transition
				name="znpb-fade"
			>

			</transition>
		</template>
	</component>
</template>

<script>
// Utils
import { ref, markRaw } from 'vue'
import { debounce } from 'lodash-es'
import { generateElements, getStyles, getOptionValue, camelCase, clearTextSelection } from '@zb/utils'
import importCSS from '@zionbuilder/importcss'
import ElementToolbox from './ElementToolbox/ElementToolbox.vue'
import ElementStyles from './ElementStyles.vue'
import ElementLoading from './ElementLoading.vue'
import VideoBackground from './VideoBackground.vue'
import { applyFilters } from '@zb/hooks'
import Options from '../Options'
import { useElementTypes, usePreviewMode, useElementMenu, usePanels, useElementFocus, useEditElement } from '@zb/editor'
import { useElementComponent } from '@data'

// Components
import ServerComponent from './ServerComponent.vue'
import { trigger } from '@zb/hooks'

export default {
	name: 'Element',
	components: {
		ElementToolbox,
		VideoBackground,
		ElementLoading,
		ElementStyles
	},
	provide () {
		const elementInfo = {}

		Object.defineProperty(elementInfo, 'data', {
			enumerable: true,
			get: () => this.element
		})

		Object.defineProperty(elementInfo, 'parentUid', {
			enumerable: true,
			get: () => this.parentUid
		})

		Object.defineProperty(elementInfo, 'elementModel', {
			enumerable: true,
			get: () => this.element.elementTypeModel
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
	props: {
		element: {
			type: Object,
			required: true
		},
		parentUid: {
			type: String,
			required: true
		}
	},
	setup (props) {
		const { openPanel } = usePanels()
		const { isPreviewMode } = usePreviewMode()
		const { elementComponent, fetchElementComponent } = useElementComponent(props.element)
		const { focusElement } = useElementFocus()

		// TODO: implement this
		// this.setupModel(this.element.options)

		// Get the element component
		fetchElementComponent()

		/**
		 * On context menu open
		 */
		const showElementMenu = function (event) {
			const { showElementMenuFromEvent } = useElementMenu()
			showElementMenuFromEvent(props.element, event)
		}

		const onElementClick = (event) => {
			// TODO: implement this
			// don't process if this was already handled
			// if (window.ZionBuilderApi.editor.ElementFocusMarshall.isHandled || this.element.elementTypeModel.is_child) {
			// 	return
			// }

			// Reset handled click
			// setTimeout(() => {
			// 	window.ZionBuilderApi.editor.ElementFocusMarshall.reset()
			// }, 0)

			// window.ZionBuilderApi.editor.ElementFocusMarshall.handle()

			focusElement(props.element)
		}

		return {
			elementComponent,
			element: props.element,
			isPreviewMode,
			showElementMenu,
			openPanel,
			focusElement,
			onElementClick
		}
	},
	data () {
		return {
			loading: false,
			options: {},
			optionsWithDefaults: {},
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
		'element.scrollTo' (newValue) {
			if (newValue) {
				this.$el.scrollIntoView({
					behavior: 'smooth'
				})

				setTimeout(() => {
					this.element.scrollTo = false
				}, 1000)
			}
		}
	},
	computed: {
		stylesConfig () {
			return this.options._styles || {}
		},
		getClasses () {
			const elementClass = camelCase(this.element.element_type)
			const classes = {
				[`zb-el-${elementClass}`]: true,
				[`znpb-element__wrapper--toolbox-dragging`]: this.isToolboxDragging,
				'znpb-element__wrapper--cutted': this.element.isCutted,
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
		canShowToolbox () {
			return this.element.isVisible && this.showToolbox && !this.isPreviewMode.value && !this.element.elementTypeModel.is_child
		},
		canShowElement () {
			if (this.isPreviewMode.value) {
				return !(this.element.options._isVisible === false)
			}
			return true
		},
		videoConfig () {
			return getOptionValue(this.options, '_styles.wrapper.styles.default.default.background-video', {})
		}
	},
	methods: {
		debounceUpdate: debounce(function () {
			this.$nextTick(() => {
				this.trigger('updated')
			})
		}),
		setupModel (model) {
			const schema = this.element.elementTypeModel.options || {}
			const cssSelector = `#${this.element.elementCssId}`
			const optionsInstance = new Options(schema, model, cssSelector, {
				onLoadingStart: this.onLoadingStart,
				onLoadingEnd: this.onLoadingEnd
			})

			let { options, renderAttributes, customCSS } = optionsInstance.parseData()

			if (this.element.elementTypeModel.style_elements) {
				Object.keys(this.element.elementTypeModel.style_elements).forEach(styleId => {
					if (options._styles && options._styles[styleId] && options._styles[styleId].styles) {
						const styleConfig = this.element.elementTypeModel.style_elements[styleId]
						const formattedSelector = styleConfig.selector.replace('{{ELEMENT}}', `#${this.element.elementCssId}`)
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
			const stylesConfig = this.element.elementTypeModel.style_elements

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
			if (!this.element.elementTypeModel.options) {
				return false
			}

			const pathArray = optionPath.split('.')
			let activeSchema = this.element.elementTypeModel.options
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
		 * Edit element
		 *
		 * Triggered when double click on the element
		 */
		editElement (event) {
			const { editElement } = useEditElement()
			event.stopPropagation()

			if (!this.isPreviewMode.value) {
				editElement(this.element)

				// Clear text selection that may appear
				clearTextSelection(window)
				clearTextSelection(window.parent)
			}
		},

		restoreHiddenElement () {
			this.element.toggleVisibility()
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
				elementType: this.element.element_type,
				element: this.$el,
				options: this.options || {},
				elementUid: this.element.uid,

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
