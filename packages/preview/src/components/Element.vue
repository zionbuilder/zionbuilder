<template>
	<ElementLoading v-if="loading" />

	<component
		v-else-if="elementComponent && !(element.isVisible === false && isPreviewMode)"
		:is="elementComponent"
		class="znpb-element__wrapper zb-element"
		:class="{'znpb-element__wrapper--panel-hovered': element.isHighlighted}"
		:id="`${element.elementCssId}`"
		:element="element"
		:data="element"
		:options="options"
		@mouseenter="showToolbox = true"
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
import { ref, watch, computed } from 'vue'
import { debounce } from 'lodash-es'
import { generateElements, getStyles, getOptionValue, camelCase, clearTextSelection } from '@zb/utils'
import { applyFilters, trigger } from '@zb/hooks'

// Components
import ServerComponent from './ServerComponent.vue'
import ElementToolbox from './ElementToolbox/ElementToolbox.vue'
import ElementStyles from './ElementStyles.vue'
import ElementLoading from './ElementLoading.vue'
import VideoBackground from './VideoBackground.vue'

// Composables
import { useElementTypes, usePreviewMode, useElementMenu, useElementFocus, useEditElement } from '@zb/editor'
import { useElementComponent } from '@data'
import Options from '../Options'

export default {
	name: 'Element',
	components: {
		ElementToolbox,
		VideoBackground,
		ElementLoading,
		ElementStyles
	},
	props: ['element'],
	setup (props) {
		const { isPreviewMode } = usePreviewMode()
		const { elementComponent, fetchElementComponent } = useElementComponent(props.element)
		const { focusElement } = useElementFocus()

		let toolboxWatcher = null

		// Data
		const loading = ref(false)
		const showToolbox = ref(false)
		const canHideToolbox = ref(true)
		const isToolboxDragging = ref(false)
		const registeredEvents = ref({})

		// computed
		const parsedData = computed(() => {
			const schema = props.element.elementTypeModel.options || {}
			const cssSelector = `#${props.element.elementCssId}`
			const optionsInstance = new Options(schema, props.element.options, cssSelector, {
				onLoadingStart: () => loading.value = true,
				onLoadingEnd: () => loading.value = false,
			})

			return optionsInstance.parseData()
		})

		const options = computed(() => parsedData.value.options)
		const renderAttributes = computed(() => parsedData.value.renderAttributes)
		const customCSS = computed(() => {
			let customCSS = parsedData.value.customCSS
			const elementStyleConfig = props.element.elementTypeModel.style_elements

			if (elementStyleConfig) {
				Object.keys(elementStyleConfig).forEach(styleId => {
					if (options.value._styles && options.value._styles[styleId] && options.value._styles[styleId].styles) {
						const styleConfig = elementStyleConfig[styleId]
						const formattedSelector = styleConfig.selector.replace('{{ELEMENT}}', `#${props.element.elementCssId}`)
						customCSS += getStyles(formattedSelector, options.value._styles[styleId].styles)
					}
				})
			}

			return customCSS
		})
		const stylesConfig = computed(() => options._styles || {})
		const canShowToolbox = computed(() => props.element.isVisible && showToolbox.value && !isPreviewMode.value && !props.element.elementTypeModel.is_child)
		const canShowElement = computed(() => isPreviewMode.value ? !(options.value._isVisible === false) : true)
		const videoConfig = computed(() => getOptionValue(options.value, '_styles.wrapper.styles.default.default.background-video', {}))
		const getExtraAttributes = computed(() => {
			const wrapperAttributes = renderAttributes.wrapper || {}
			const elementClass = camelCase(props.element.element_type)
			const classes = {
				[`zb-el-${elementClass}`]: true,
				[`znpb-element__wrapper--toolbox-dragging`]: isToolboxDragging.value,
				'znpb-element__wrapper--cutted': props.element.isCutted,
				'znpb-element--loading': loading.value
			}

			// Add animation classes
			const appearAnimation = (options._advanced_options || {})._appear_animation
			if (appearAnimation) {
				classes['animated'] = true
				classes[appearAnimation] = true
			}

			if (stylesConfig.wrapper) {
				const wrapperConfig = stylesConfig.wrapper
				if (wrapperConfig.classes) {
					wrapperConfig.classes.forEach(classSelector => {
						classes[classSelector] = true
					})
				}
			}

			// Add classes added by render attributes
			const wrapperClasses = typeof wrapperAttributes.class !== 'undefined' ? wrapperAttributes.class : []

			wrapperClasses.forEach(cssClass => {
				classes[cssClass] = true
			})

			// Add render attributes classes
			return {
				...wrapperAttributes,
				class: classes,
				api: {
					getStyleClasses
				}
			}
		})

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
			focusElement(props.element)
		}

		/**
		 * On element mouseleave
		 */
		const onMouseLeave = () => {
			if (canHideToolbox.value) {
				showToolbox.value = false
			} else {
				if (!toolboxWatcher) {
					// Set a watcher so we can hide the toolbox
					toolboxWatcher = watch(canHideToolbox, (newValue) => {
						if (newValue) {
							showToolbox.value = false
							toolboxWatcher()
							toolboxWatcher = null
						}
					})
				}
			}
		}

		const getStyleClasses = (styleId, extraClasses = {}) => {
			const classes = {}

			if (stylesConfig[styleId]) {
				const elementStylesClasses = stylesConfig[styleId]
				if (elementStylesClasses.classes) {
					elementStylesClasses.classes.forEach(classSelector => {
						classes[classSelector] = true
					})
				}
			}

			return classes
		}

		return {
			// Computed
			stylesConfig,
			canShowToolbox,
			canShowElement,
			videoConfig,
			getExtraAttributes,
			// Data
			elementComponent,
			element: props.element,
			isPreviewMode,
			showElementMenu,
			focusElement,
			onElementClick,
			options,
			customCSS,
			loading,
			canHideToolbox,
			isToolboxDragging,
			toolboxWatcher,
			registeredEvents,
			showToolbox,
			// Methods
			onMouseLeave
		}
	},
	watch: {
		// 'element.options': {
		// 	handler (newValue, oldValue) {
		// 		this.debounceUpdate()
		// 	},
		// 	deep: true
		// },
		// 'element.content' (newValue, oldValue) {
		// 	this.debounceUpdate()
		// },
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
	methods: {
		debounceUpdate: debounce(function () {
			this.$nextTick(() => {
				this.trigger('updated')
			})
		}),

		applyCustomClassesToRenderTags () {
			const elementSavedStyles = getOptionValue(this.options, '_styles', {})
			const stylesConfig = this.element.elementTypeModel.style_elements

			Object.keys(elementSavedStyles).forEach(styleConfigId => {
				const { classes } = elementSavedStyles[styleConfigId]

				if (typeof stylesConfig[styleConfigId] !== 'undefined') {
					const { render_tag: renderTag } = stylesConfig[styleConfigId]
					if (renderTag && classes && classes.length > 0) {
						classes.forEach(cssClass => {
							this.element.renderAttributes.addRenderAttribute(renderTag, 'class', cssClass)
						})
					}
				}
			})
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
