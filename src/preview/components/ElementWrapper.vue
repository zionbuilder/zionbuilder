<template>
	<ElementLoading v-if="loading" />

	<component
		:is="elementComponent"
		v-else-if="elementComponent && !(element.isVisible === false && UIStore.isPreviewMode)"
		:id="`${element.elementCssId}`"
		ref="root"
		class="znpb-element__wrapper zb-element"
		:element="element"
		:options="options"
		v-bind="getExtraAttributes"
		@mouseenter="onMouseEnter"
		@mouseleave="onMouseLeave"
		@click="onElementClick"
		@contextmenu="showElementMenu"
	>
		<template #start>
			<ElementToolbox v-if="canShowToolbox" :element="element" />

			<VideoBackground v-if="videoConfig" :video-config="videoConfig" />

			<ElementStyles :styles="customCSS" />
		</template>
		<template #end>
			<div v-if="!isVisible" class="znpb-hidden-element-container">
				<div class="znpb-hidden-element-placeholder">
					<Icon icon="eye" @click.stop="element.toggleVisibility()"> </Icon>
				</div>
			</div>
		</template>
	</component>
</template>

<script>
// Utils
import { ref, watch, computed, readonly, provide } from 'vue';
import { get, debounce, each, escape, mergeWith, isArray, camelCase } from 'lodash-es';
import { applyFilters } from '/@/common/modules/hooks';
import { useOptionsSchemas, usePseudoSelectors } from '/@/common/composables';

// Components
import ElementToolbox from './ElementToolbox/ElementToolbox.vue';
import ElementStyles from './ElementStyles.vue';
import ElementLoading from './ElementLoading.vue';
import VideoBackground from './VideoBackground.vue';

// Composables
import { useElementComponent } from '../composables/useElementComponent';
import Options from '../modules/Options';
import { useUIStore } from '/@/editor/store';

let clickHandled = false;

export default {
	name: 'ElementWrapper',
	components: {
		ElementToolbox,
		VideoBackground,
		ElementLoading,
		ElementStyles,
	},
	props: ['element'],
	setup(props) {
		const root = ref(null);
		const UIStore = useUIStore();
		const { elementComponent, fetchElementComponent } = useElementComponent(props.element);
		const { getSchema } = useOptionsSchemas();
		const { activePseudoSelector } = usePseudoSelectors();
		const isVisible = computed(() => get(props.element.options, '_isVisible', true));

		let toolboxWatcher = null;
		let optionsInstance = null;

		// Data
		const localLoading = ref(false);
		const loading = computed(() => props.element.loading || localLoading.value);
		const showToolbox = ref(false);
		const canHideToolbox = ref(true);
		const isToolboxDragging = ref(false);
		const registeredEvents = ref({});

		// Needed to generate the hover css
		const isElementEdited = ref(false);
		const isHoverState = ref(false);

		// Options schema
		const advancedSchema = {
			_advanced_options: {
				type: 'group',
				child_options: getSchema('element_advanced'),
			},
		};

		const elementOptionsSchema = Object.assign({}, props.element.elementDefinition.options || {}, advancedSchema);

		// computed
		const parsedData = computed(() => {
			const cssSelector = `#${props.element.elementCssId}`;

			optionsInstance = new Options(
				elementOptionsSchema,
				props.element.options,
				cssSelector,
				{
					onLoadingStart: () => (localLoading.value = true),
					onLoadingEnd: () => (localLoading.value = false),
				},
				props.element,
			);

			return optionsInstance.parseData();
		});

		const options = computed(() => readonly(parsedData.value.options || {}));

		// Check to see if the current element is being edited
		watch(
			() => UIStore.editedElement,
			(newValue, oldValue) => {
				if (newValue === props.element) {
					isElementEdited.value = true;
				} else if (oldValue === props.element) {
					isElementEdited.value = false;
				}
			},
		);

		// check to see if the hover state is selected
		watch(activePseudoSelector, newValue => {
			if (newValue.id === ':hover') {
				isHoverState.value = true;
			} else {
				isHoverState.value = false;
			}
		});

		const shouldGenerateHoverStyles = computed(() => {
			return isElementEdited.value && isHoverState.value;
		});

		const customCSS = computed(() => {
			let customCSS = '';

			const elementStyleConfig = props.element.elementDefinition.style_elements || {};

			Object.keys(elementStyleConfig).forEach(styleId => {
				if (options.value._styles && options.value._styles[styleId]) {
					const styleConfig = elementStyleConfig[styleId];
					const cssSelector = applyFilters(
						'zionbuilder/element/css_selector',
						`#${props.element.elementCssId}`,
						optionsInstance,
						props.element,
					);
					const formattedSelector = styleConfig.selector.replace('{{ELEMENT}}', cssSelector);
					const stylesSavedValues = applyFilters(
						'zionbuilder/element/styles_model',
						options.value._styles[styleId],
						optionsInstance,
						props.element,
					);

					customCSS += window.zb.editor.getCssFromSelector([formattedSelector], stylesSavedValues);

					// Generate the styles on hover
					if (shouldGenerateHoverStyles.value) {
						customCSS += window.zb.editor.getCssFromSelector([formattedSelector], stylesSavedValues, {
							forcehoverState: true,
						});
					}
				}
			});

			customCSS += parsedData.value.customCSS;
			customCSS = applyFilters('zionbuilder/element/custom_css', customCSS, optionsInstance, props.element);

			return customCSS;
		});

		const stylesConfig = computed(() => options.value._styles || {});
		const canShowToolbox = computed(() => {
			return (
				(UIStore.editedElement === props.element || props.element.isHighlighted) &&
				props.element.isVisible &&
				!props.element.elementDefinition.is_child &&
				!UIStore.isPreviewMode
			);
		});
		const canShowElement = computed(() => (UIStore.isPreviewMode ? !(options.value._isVisible === false) : true));
		const videoConfig = computed(() =>
			get(options.value, '_styles.wrapper.styles.default.default.background-video', {}),
		);

		const renderAttributes = computed(() => {
			const optionsAttributes = parsedData.value.renderAttributes;
			const additionalAttributes = {};

			if (stylesConfig.value) {
				each(stylesConfig.value, (styleData, styleID) => {
					if (styleData.attributes) {
						each(styleData.attributes, attributeValue => {
							if (attributeValue.attribute_name) {
								additionalAttributes[styleID] = additionalAttributes[styleID] || {};

								let cleanAttrName = attributeValue.attribute_name;
								let cleanAttrValue = escape(attributeValue.attribute_value);
								additionalAttributes[styleID][cleanAttrName] = cleanAttrValue;
							}
						});
					}
				});
			}

			// Check for custom css classes
			const elementStyleConfig = props.element.elementDefinition.style_elements;
			if (elementStyleConfig) {
				Object.keys(elementStyleConfig).forEach(styleId => {
					if (options.value._styles && options.value._styles[styleId] && options.value._styles[styleId].classes) {
						const styleConfig = elementStyleConfig[styleId];
						const renderTag = styleConfig.render_tag;

						if (renderTag) {
							options.value._styles[styleId].classes.forEach(cssClass => {
								if (!additionalAttributes[renderTag]) {
									additionalAttributes[renderTag] = {};
								}

								additionalAttributes[renderTag]['class'] = [
									...(additionalAttributes[renderTag]['class'] || []),
									cssClass,
								];
							});
						}
					}
				});
			}

			return mergeWith({}, optionsAttributes, additionalAttributes, (a, b) => {
				if (isArray(a)) {
					return b.concat(a);
				}
			});
		});
		const getExtraAttributes = computed(() => {
			const wrapperAttributes = renderAttributes.value.wrapper || {};

			const elementClass = camelCase(props.element.element_type);
			const classes = applyFilters(
				'zionbuilder/element/css_classes',
				{
					[`zb-el-${elementClass}`]: true,
					[`znpb-element__wrapper--toolbox-dragging`]: isToolboxDragging.value,
					'znpb-element__wrapper--cutted': props.element.isCut,
					'znpb-element--loading': loading.value,
				},
				optionsInstance,
				props.element,
			);

			if (stylesConfig.value.wrapper) {
				const wrapperConfig = stylesConfig.value.wrapper;
				if (wrapperConfig.classes) {
					wrapperConfig.classes.forEach(classSelector => {
						classes[classSelector] = true;
					});
				}
			}

			// Add classes added by render attributes
			const wrapperClasses = typeof wrapperAttributes.class !== 'undefined' ? wrapperAttributes.class : [];

			wrapperClasses.forEach(cssClass => {
				classes[cssClass] = true;
			});

			// Add render attributes classes
			return {
				...wrapperAttributes,
				class: classes,

				api: {
					getStyleClasses,
					getAttributesForTag,
				},
			};
		});

		// Get the element component
		fetchElementComponent();

		function getAttributesForTag(tagID, extraArgs = {}, index = null) {
			tagID = index !== null ? `${tagID}${index}` : tagID;
			return Object.assign(renderAttributes.value[tagID] || {}, extraArgs);
		}

		/**
		 * On context menu open
		 */
		const showElementMenu = function (event) {
			event.preventDefault();
			event.stopPropagation();

			UIStore.showElementMenuFromEvent(props.element, event);
		};

		// Prevents us using stop propagation that can affect other elements
		const onElementClick = () => {
			if (clickHandled) {
				return;
			}

			if (!UIStore.isPreviewMode) {
				UIStore.editElement(props.element);
			}

			clickHandled = true;
			setTimeout(() => {
				clickHandled = false;
			}, 50);
		};

		const getStyleClasses = (styleId, extraClasses = {}) => {
			const classes = {};

			if (stylesConfig.value[styleId]) {
				const elementStylesClasses = stylesConfig.value[styleId];
				if (elementStylesClasses.classes) {
					elementStylesClasses.classes.forEach(classSelector => {
						classes[classSelector] = true;
					});
				}
			}

			return classes;
		};

		// Mainly used for RenderValue component
		provide('elementInfo', props.element);
		provide('elementOptions', options);

		function onMouseEnter(e) {
			props.element.highlight();

			if (props.element.parent) {
				let parent = props.element.parent;
				while (parent) {
					parent.unHighlight();
					parent = parent.parent;
				}
			}
		}

		function onMouseLeave(e) {
			props.element.unHighlight();
			if (props.element.parent) {
				props.element.parent.highlight();
				parent = parent.parent;
			}
		}

		return {
			root,
			// Computed
			stylesConfig,
			canShowToolbox,
			canShowElement,
			videoConfig,
			getExtraAttributes,
			// Data
			elementComponent,
			showElementMenu,
			onElementClick,
			options,
			customCSS,
			loading,
			canHideToolbox,
			isToolboxDragging,
			toolboxWatcher,
			registeredEvents,
			showToolbox,
			// Stores
			UIStore,
			// Methods
			onMouseEnter,
			onMouseLeave,
			isVisible,
		};
	},
	watch: {
		'element.scrollTo'(newValue) {
			if (newValue) {
				if (typeof this.$el.scrollIntoView === 'function') {
					this.$el.scrollIntoView({
						behavior: 'smooth',
						inline: 'center',
						block: 'center',
					});
				}

				setTimeout(() => {
					this.element.scrollTo = false;
				}, 1000);
			}
		},
	},
	methods: {
		debounceUpdate: debounce(function () {
			this.$nextTick(() => {
				this.trigger('updated');
			});
		}),
		/**
		 * Register an event for an action
		 */
		on(eventType, callback) {
			if (typeof this.registeredEvents[eventType] === 'undefined') {
				this.registeredEvents[eventType] = [];
			}

			this.registeredEvents[eventType].push(callback);
		},
		/**
		 * Remove an event listener
		 */
		off(eventType, callback) {
			if (
				typeof this.registeredEvents[eventType] === 'undefined' &&
				this.registeredEvents[eventType].includes(callback)
			) {
				const callbackIndex = this.registeredEvents[eventType].indexOf(callback);
				if (callbackIndex !== -1) {
					this.registeredEvents[eventType].splice(callbackIndex, 1);
				}
			}
		},
		/**
		 * Remove all events
		 */
		offAll() {
			this.registeredEvents = {};
		},
		getDefaultEventResponse() {
			return {
				elementType: this.element.element_type,
				element: this.$el,
				options: this.options || {},
				elementUid: this.element.uid,

				// Actions that the user can subscribe to
				on: this.on,
				off: this.off,
				offAll: this.offAll,
			};
		},
		trigger(eventType, data) {
			const defaultData = this.getDefaultEventResponse();
			if (typeof this.registeredEvents[eventType] !== 'undefined') {
				this.registeredEvents[eventType].forEach(callbackFunction => {
					callbackFunction({
						...defaultData,
						...data,
					});
				});
			}
		},
	},
};
</script>

<style lang="scss">
.znpb-element {
	&--loading {
		opacity: 0.2;
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
	opacity: 0.2;
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
	box-shadow: 0 0 0 2px rgba(var(--zb-secondary-rgb-color), 0.3);
}

.znpb-element__wrapper {
	position: relative;
	transition: opacity 0.2s;

	.znpb-hidden-element-container {
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		z-index: 1001;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 100%;
		height: 100%;
		background: rgba(255, 255, 255, 0.7);
	}

	&:hover,
	&--toolbox-dragging {
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
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: var(--zb-red);
			box-shadow: 0 11px 20px 0 rgba(0, 0, 0, 0.1);
			border-radius: 50%;
			transition: all 0.2s;
		}
		.znpb-editor-icon-wrapper {
			position: relative;
			color: var(--zb-surface-color);
			cursor: pointer;
		}
		&:hover {
			&:before {
				transform: scale(1.1);
			}
		}
	}
}
.znpb-element-utilities__margin-top-helper,
.znpb-element-utilities__margin-right-helper,
.znpb-element-utilities__margin-bottom-helper,
.znpb-element-utilities__margin-left-helper {
	position: absolute;
}

.znpb-element-utilities__margin-top-helper,
.znpb-element-utilities__margin-bottom-helper {
	left: 0;
	width: 100%;
	min-height: 2px;
	cursor: n-resize;
}

.znpb-element-utilities__margin-left-helper,
.znpb-element-utilities__margin-right-helper {
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

.znpb-el-notice {
	color: #fff;
	font-size: 13px;
	position: relative;
	background-color: rgba(40, 40, 44, 0.6);
	border-radius: 4px;
	padding: 20px 20px 20px 56px;
	width: 100%;
	margin: 20px;
}

.znpb-el-notice h3 {
	font-size: 15px !important;
	margin: 0 0 5px !important;
}

.znpb-el-notice a {
	font-weight: 700;
}

.znpb-el-notice .znpb-editor-icon-wrapper {
	color: rgba(255, 255, 255, 0.4);
	position: absolute;
	font-size: 26px;
	margin-left: -36px;
}

.znpb-el-notice .znpb-editor-icon-wrapper svg {
	fill: currentColor;
	width: 1em;
	height: 1em;
	display: block;
}
</style>
