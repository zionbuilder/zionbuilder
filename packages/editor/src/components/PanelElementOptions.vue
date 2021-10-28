<template>
	<BasePanel
		class="znpb-element-options__panel-wrapper"
		:class="{
			'znpb-element-options__panel-wrapper--hidden': isPanelHidden
		}"
		@close-panel="closeOptionsPanel"
		:panel-name="`${element.name} ${$translate('options')}`"
		panel-id="PanelElementOptions"
		:show-expand="false"
		:allow-horizontal-resize="!isPanelHidden"
		:allow-vertical-resize="!isPanelHidden"
		:panel="panel"
		:style="panelStyles"
	>

		<template #before-header>
			<div
				class="znpb-element-options__hide"
				@click.stop="isPanelHidden = !isPanelHidden"
			>
				<Icon
					icon="select"
					class="znpb-element-options__hideIcon"
					:class="{
						'znpb-element-options__hide--hidden': isPanelHidden
					}"
				/>

			</div>
		</template>

		<template #header>
			<div class="znpb-element-options__header">
				<!-- Show back button for child elements -->
				<div
					class="znpb-element-options__header-back"
					v-if="element.elementTypeModel.is_child"
					@click="onBackButtonClick"
				>
					<Icon
						class="znpb-element-options__header-back-icon"
						icon="select"
					/>
				</div>

				<h4
					class="znpb-panel__header-name"
					@click="onBackButtonClick"
					@mouseenter="showBreadcrumbs=true"
					@mouseleave="showBreadcrumbs=false"
				>
					{{`${element.name} ${$translate('options')}`}}
					<Icon icon="select" />
					<BreadcrumbsWrapper
						v-show="showBreadcrumbs"
						:element="element"
					/>
				</h4>

			</div>
		</template>

		<div class="znpb-element-options-content-wrapper">
			<Tabs
				:has-scroll="['general','advanced']"
				v-model:activeTab="activeKeyTab"
				class="znpb-element-options__tabs-wrapper"
			>
				<Tab name="General">
					<OptionsForm
						class="znpb-element-options-content-form  znpb-fancy-scrollbar"
						:schema="element.elementTypeModel.options"
						v-model="elementOptions"
						v-if="element.elementTypeModel.hasOwnProperty('options') && Object.keys(element.elementTypeModel.options).length > 0"
					/>

					<p
						class="znpb-element-options-no-option-message"
						v-else
					>{{$translate('element_has_no_specific_options')}}</p>

				</Tab>
				<Tab name="Styling">
					<OptionsForm
						class="znpb-fancy-scrollbar"
						:schema="computedStyleOptionsSchema"
						v-model="computedStyleOptions"
					/>
				</Tab>
				<Tab name="Advanced">
					<OptionsForm
						class="znpb-element-options-content-form znpb-fancy-scrollbar"
						:schema="getSchema('element_advanced')"
						v-model="advancedOptionsModel"
					/>
				</Tab>
				<Tab name="Search">
					<template #title>
						<div
							@click.stop="toggleSearchIcon"
							class="znpb-element-options__search-tab-title"
						>
							<Icon :icon="searchIcon" />
						</div>

						<BaseInput
							v-if="searchActive"
							v-model="optionsFilterKeyword"
							:filterable="true"
							ref="searchInput"
							placeholder="Search option"
							class="znpb-tabs__header-item-search-options"
						>
						</BaseInput>
					</template>

					<p
						class="znpb-element-options-default-message"
						v-if="optionsFilterKeyword.length > 2 && Object.keys(filteredOptions).length === 0"
					>
						{{$translate('no_options_found')}}
					</p>
					<p
						v-if="optionsFilterKeyword.length < 3"
						class="znpb-element-options-no-option-message"
					>
						{{defaultMessage}}
					</p>

					<OptionsForm
						class="znpb-element-options-content-form  znpb-fancy-scrollbar"
						:schema="filteredOptions"
						v-model="elementOptions"
					/>
				</Tab>
			</Tabs>
		</div>
		<div class="znpb-element-options-action">
			<div
				class="znpb-element-options-action__undo"
				:class="{'znpb-element-options-action__undo--disabled': ! canUndo}"
				@click="undo"
			>
				<Icon icon="undo" />
			</div>
			<div
				class="znpb-element-options-action__redo"
				:class="{'znpb-element-options-action__redo--disabled': ! canRedo}"
				@click="redo"
			>
				<Icon icon="redo" />
			</div>

		</div>
	</BasePanel>
</template>

<script>
import { ref, watch, provide, computed } from 'vue'
import { on, off } from '@zb/hooks'
import { debounce, isEditable, Environment } from '@zb/utils'
import { useEditElement, useElementProvide, useEditorData, useWindows, useHistory } from '@composables'
import { usePseudoSelectors, useOptionsSchemas } from '@zb/components'

// Components
import BreadcrumbsWrapper from './elementOptions/BreadcrumbsWrapper.vue'
import BasePanel from './BasePanel.vue'


export default {
	name: 'PanelElementOptions',
	components: {
		BreadcrumbsWrapper,
		BasePanel
	},
	props: ['panel'],
	setup (props) {
		const isPanelHidden = ref(false)
		let ignoreLocalHistory = false
		const { setActivePseudoSelector } = usePseudoSelectors()
		const { element, editElement, unEditElement } = useEditElement()
		const { provideElement } = useElementProvide()
		const { getSchema } = useOptionsSchemas()
		const activeKeyTab = ref(null)
		const searchActive = ref(false)
		const history = ref([])
		const historyIndex = ref(0)

		const optionsFilterKeyword = ref('')

		const panelStyles = computed(() => {
			return {
				'--optionsPanelWidth': `-${props.panel.width}px`
			}
		})

		const elementOptions = computed({
			get () {
				return element.value ? element.value.options : {}
			},
			set (newValues) {
				element.value.updateOptions(newValues)

			}
		})

		watch(element.value.options, (newValue) => {
			// Add to history
			if (!ignoreLocalHistory) {
				addToLocalHistory()
				ignoreLocalHistory = false
			}
		})

		const advancedOptionsModel = computed({
			get () {
				return elementOptions.value._advanced_options || {}
			},
			set (newValues) {
				elementOptions.value = {
					...elementOptions.value,
					_advanced_options: newValues
				}
			}
		})

		const canUndo = computed(() => {
			return historyIndex.value > 0
		})

		const searchIcon = computed(() => {
			return searchActive.value ? 'close' : 'search'
		})

		const canRedo = computed(() => {
			return historyIndex.value + 1 < history.value.length
		})

		const hasChanges = computed(() => history.value.length > 1 && historyIndex.value > 0)

		const addToLocalHistory = debounce(function () {
			// Clone store
			const clonedValues = JSON.parse(JSON.stringify(elementOptions.value))

			if (historyIndex.value + 1 < history.value.length) {
				history.value.splice(historyIndex.value + 1)
			}

			history.value.push(clonedValues)
			historyIndex.value++
		}, 500)

		function undo () {
			const prevState = history.value[historyIndex.value - 1]

			if (prevState) {
				ignoreLocalHistory = true
				elementOptions.value = prevState
				historyIndex.value--
			}
		}

		function redo () {
			const nextState = history.value[historyIndex.value + 1]

			if (nextState) {
				ignoreLocalHistory = true
				elementOptions.value = nextState
				historyIndex.value++
			}
		}

		// Set initial history
		history.value.push(JSON.parse(JSON.stringify(elementOptions.value)))

		// Change the tab when a new element is selected
		watch(element, (newValue) => {
			activeKeyTab.value = 'general'
			searchActive.value = false
			optionsFilterKeyword.value = ''

			// Clear selected pseudo selector
			setActivePseudoSelector(null)
		})

		provideElement(element)
		provide('elementInfo', element)
		provide('OptionsFormTopModelValue', elementOptions)
		provide('serverRequester', element.value.serverRequester)

		return {
			isPanelHidden,
			element,
			// Computed
			panelStyles,
			elementOptions,
			advancedOptionsModel,
			getSchema,
			editElement,
			unEditElement,
			activeKeyTab,
			searchActive,
			searchIcon,
			canUndo,
			canRedo,
			hasChanges,
			// Methods
			undo,
			redo,
			optionsFilterKeyword
		}
	},
	data () {
		return {
			showBreadcrumbs: false,
			elementClasses: [],
			lastTab: null,
			noOptionMessage: '',
			defaultMessage: this.$translate('element_options_default_message'),

		}
	},
	computed: {
		computedStyleOptionsSchema () {
			const schema = {}
			let styledElements = this.element.elementTypeModel.style_elements

			Object.keys(styledElements).forEach(styleId => {
				const config = styledElements[styleId]

				schema[styleId] = {
					type: 'css_selector',
					name: config.title,
					icon: 'brush',
					allow_class_assignments: typeof config.allow_class_assignments !== 'undefined' ? config.allow_class_assignments : true,
					selector: config.selector.replace('{{ELEMENT}}', `#${this.element.elementCssId}`),
					allow_delete: false,
					show_breadcrumbs: true,
					allow_custom_attributes: typeof config.allow_custom_attributes === 'undefined' || config.allow_custom_attributes === true
				}
			})

			return {
				'_styles': {
					id: 'styles',
					child_options: schema,
					optionsLayout: 'full',
					type: 'group'
				}
			}
		},

		allOptionsSchema () {
			const elementOptionsSchema = this.element.elementTypeModel.options ? this.element.elementTypeModel.options : {}
			const optionsSchema = {
				...elementOptionsSchema,
				...this.computedStyleOptionsSchema,
				...this.getSchema('element_advanced')
			}

			return optionsSchema
		},
		computedStyleOptions: {
			get () {
				return this.elementOptions || {}
			},
			set (newValue) {
				if (newValue === null) {
					const oldValues = { ...this.elementOptions }
					delete oldValues._styles

					this.elementOptions = oldValues
				} else {
					this.elementOptions = newValue
				}
			}
		},
		getElementInfo () {
			return {
				uid: this.element.uid,
				elementTypeConfig: this.element.elementTypeModel,
				data: this.element
			}
		},

		filteredOptions () {
			const keyword = this.optionsFilterKeyword
			if (keyword.length > 2) {
				return this.filterOptions(keyword, this.allOptionsSchema)
			}

			return {}
		}
	},
	created () {
		on('change-tab-styling', this.changeTabByEvent())
	},
	methods: {
		onBackButtonClick () {
			if (this.element.parent && this.element.parent.elementTypeModel.element_type !== 'contentRoot') {
				this.editElement(this.element.parent)
			}
		},
		changeTabByEvent (event) {
			if (event !== undefined) {
				if (tabId !== 'search') {
					this.lastTab = this.activeKeyTab
					this.optionsFilterKeyword = ''
				}
				this.activeKeyTab.value = event.detail
			}
		},
		filterOptions (keyword, optionsSchema, currentId, currentName) {
			let lowercaseKeyword = keyword.toLowerCase()
			let foundOptions = {}

			Object.keys(optionsSchema).forEach((optionId) => {
				const optionConfig = optionsSchema[optionId]

				let syncValue = []
				let syncValueName = []

				if (!optionConfig.sync) {
					if (currentId) {
						syncValue.push(...currentId)
					}

					if (currentName) {
						let name = this.getInnerStyleName(currentName[currentName.length - 1])
						currentName[currentName.length - 1] = name
						syncValueName.push(...currentName)
					}

					if (optionId === 'animation-group' || optionId === 'custom-css-group' || optionId === 'general-group') {
						syncValueName.push(this.$translate('advanced'))
					}

					if (!optionConfig.is_layout) {
						syncValue.push(optionId)
					}

					if (optionConfig.type === 'element_styles' || optionConfig.type === 'css_selector') {
						syncValue.push('styles')
						syncValueName.push(this.$translate('styles'), optionConfig.name)
					}

					if (optionConfig.type === 'responsive_group') {
						syncValue.push('%%RESPONSIVE_DEVICE%%')
					}

					if (optionConfig.type === 'pseudo_group') {
						syncValue.push('%%PSEUDO_SELECTOR%%')
					}

					syncValueName.push(optionId)
				}

				// Search in areas
				let searchOptions = optionConfig.search_tags ? [...optionConfig.search_tags] : []
				if (optionConfig.title) {
					searchOptions.push(optionConfig.title)
				}
				if (optionConfig.id) {
					searchOptions.push(optionConfig.id)
				}
				if (optionConfig.description) {
					searchOptions.push(optionConfig.description)
				}
				if (optionConfig.label) {
					searchOptions.push(optionConfig.label)
				}

				if (optionConfig.type !== 'accordion_menu' && optionConfig.type !== 'element_styles') {
					if (searchOptions.join(' ').toLowerCase().indexOf(lowercaseKeyword) !== -1) {
						let filteredBreadcrumbs = []
						if (currentName) {
							filteredBreadcrumbs = currentName.filter(function (value) {
								return value !== undefined
							})
						}
						foundOptions[syncValue.join('.')] = {
							...optionConfig,
							id: syncValue.join('.'),
							sync: optionConfig.sync || syncValue.join('.'),
							breadcrumbs: filteredBreadcrumbs
						}
					}
				}

				if (optionConfig.type === 'repeater') {
					return
				}

				if (optionConfig.type === 'element_styles' || optionConfig.type === 'css_selector') {
					const childOptions = this.filterOptions(keyword, this.getSchema('element_styles'), syncValue, syncValueName)

					foundOptions = {
						...foundOptions,
						...childOptions
					}

				}

				if (optionConfig.child_options && Object.keys(optionConfig.child_options).length > 0) {
					const childOptions = this.filterOptions(keyword, optionConfig.child_options, syncValue, syncValueName)

					foundOptions = {
						...foundOptions,
						...childOptions
					}


				}
			})

			return foundOptions
		},
		getInnerStyleName (id) {
			if (id === 'pseudo_selectors') {
				return undefined
			}

			return this.computedStyleOptionsSchema._styles.child_options[id] !== undefined ? this.computedStyleOptionsSchema._styles.child_options[id].title : this.allOptionsSchema[id] !== undefined ? this.allOptionsSchema[id].title : undefined
		},
		toggleSearchIcon () {
			this.searchActive = !this.searchActive
			if (!this.searchActive) {
				this.changeTab('general')
			} else {
				this.changeTab('search')
			}
			this.optionsFilterKeyword = ''
		},

		changeTab (tabId) {
			this.activeKeyTab = tabId

			if (tabId !== 'search') {
				this.lastTab = this.activeKeyTab
				this.optionsFilterKeyword = ''
			}
		},

		addToGlobalHistory (element) {
			if (this.hasChanges) {
				const activeEl = element ? element : this.element
				const { addToHistory } = useHistory()
				const elementSavedName = activeEl.name
				addToHistory(`Edited ${elementSavedName}`)
			}
		},
		closeOptionsPanel () {
			this.addToGlobalHistory()
			this.panel.close()
			this.unEditElement()
		},
		onKeyPress (e) {
			const controllKey = Environment.isMac ? 'metaKey' : 'ctrlKey'

			if (isEditable()) {
				return
			}

			// Undo CTRL+Z
			if (e.which === 90 && e[controllKey] && !e.shiftKey) {
				this.undo()
				e.preventDefault()
				e.stopPropagation()
			}
			// Redo CTRL+SHIFT+Z CTRL + Y
			if ((e.which === 90 && e[controllKey] && e.shiftKey) || (e[controllKey] && e.which === 89)) {
				this.redo()
				e.preventDefault()
				e.stopPropagation()
			}
		}
	},
	watch: {
		element (newValue, oldValue) {
			if (newValue && newValue !== oldValue) {
				this.addToGlobalHistory(oldValue)
			}
		},
		searchActive (newValue) {
			if (newValue) {
				this.$nextTick(() => {
					if (this.$refs.searchInput) {
						this.$refs.searchInput.focus()
					}
				})
			}
		}
	},
	mounted () {
		const { addEventListener } = useWindows()
		addEventListener('keydown', this.onKeyPress, true)
	},
	unmounted () {
		const { removeEventListener } = useWindows()
		removeEventListener('keydown', this.onKeyPress, true)

		// remove events
		off('change-tab-styling', this.changeTab())
	}
}
</script>

<style lang='scss'>
@keyframes searchFade {
	0% {
		opacity: 0;
	}
	100% {
		opacity: 1;
	}
}

.znpb-tabs__header-item--search {
	flex-grow: 1;
}
.znpb-element-options__search-tab {
	padding: 0;
}
.znpb-element-options {
	&__panel-wrapper .znpb-panel__content_wrapper {
		padding-top: 0;
	}

	&__panel-wrapper--hidden .znpb-panel__content_wrapper {
		z-index: 0;
	}

	&__header {
		position: relative;
		display: flex;
		align-items: center;

		&-back {
			display: flex;
			align-items: center;
			padding-left: 20px;
			cursor: pointer;

			& ~ .znpb-panel__header-name {
				padding-left: 0;
			}

			&-icon {
				width: 22px;
				height: 22px;
				margin-right: 10px;
				color: var(--zb-surface-icon-color);
				font-size: 14px;
				background-color: var(--zb-surface-lighter-color);
				border-radius: 3px;
				transition: .15s all;
				transition: all .3s;
				cursor: pointer;

				&:hover {
					color: var(--zb-surface-text-hover-color);
					background-color: var(--zb-surface-lightest-color);
				}

				svg {
					transform: rotate(90deg);
				}
			}
		}

		.znpb-panel__header-name {
			position: relative;
			display: flex;
			align-items: center;
			cursor: pointer;
		}

		.znpb-element-options__breadcrumbs {
			position: absolute;
			top: 100%;
			left: 0;
			z-index: 20000;
			min-width: 200px;
			max-height: 360px;
			padding: 16px 16px 4px;
			background-color: var(--zb-dropdown-bg-color);
			box-shadow: var(--zb-dropdown-shadow);
			border: 1px solid var(--zb-dropdown-border-color);
			border-radius: 3px;
			transform: translate(10px, -10px);

			& > span {
				display: block;
				padding-bottom: 12px;
			}
		}
	}

	&-content-wrapper {
		position: relative;
		display: flex;
		flex-grow: 1;
		overflow: hidden;

		.znpb-element-options__tabs-wrapper {
			flex-grow: 1;
			padding-top: 20px;
			background-color: var(--zb-surface-darker-color);
		}
		.znpb-tabs {
			display: flex;
			flex-direction: column;

			.znpb-tabs__content, .znpb-tabs__wrapper {
				height: calc(100% - 38px);
			}

			.znpb-tab__wrapper {
				position: relative;
				height: 100%;
			}
			.znpb-tabs__content {
				display: flex;
				flex-direction: column;
			}
		}
	}

	&-content-form {
		flex-grow: 1;
		max-height: 100%;
	}

	&-action {
		display: flex;
		justify-content: space-between;
		flex-grow: 0;
		flex-shrink: 0;
		padding: 20px;

		&__undo {
			margin-right: 10px;
		}

		&__undo, &__redo {
			display: flex;
			justify-content: center;
			flex: 1;
			padding: 15px 37px;
			line-height: 1;
			background-color: var(--zb-surface-lighter-color);
			border-radius: 3px;
			cursor: pointer;

			&--active {
				&:hover {
					opacity: .9;
				}
			}
			&--disabled {
				opacity: .5;
				pointer-events: none;
			}
		}
	}
}

// Options filter
.znpb-tabs__header-item {
	.znpb-tabs__header-item-search-options {
		position: absolute;
		top: 20px;
		left: 0;
		width: calc(100% - 78px);
		height: 38px;
		margin-left: 20px;
		background: var(--zb-surface-lighter-color);
		border: none;
		border-radius: 0;

		input {
			height: 100%;
			padding-left: 0;
			border-radius: 0;
		}
	}
}
.znpb-element-options__tabs-wrapper {
	p.znpb-element-options-default-message, p.znpb-element-options-no-option-message {
		padding: 20px;
	}

	.znpb-element-options-no-option-message {
		position: absolute;
		top: 0;
		z-index: 9;
	}
}

//search tab
.znpb-tabs--card > .znpb-tabs__header > .znpb-tabs__header-item.znpb-tabs__header-item--search {
	flex: 0 1 auto;
	padding: 0;
	margin-left: auto;
	background: var(--zb-surface-color);
	border-top-right-radius: 3px;
	border-top-left-radius: 3px;

	.znpb-editor-icon-wrapper {
		display: block;
	}
}

.znpb-element-options__search-tab-title {
	padding: 10px 12px;
}

// Some style from wp common.css is adding borders and shadow
// A different solution would be to add the admin bar css from common in the builder
.znpb-input-content .widget-inside {
	box-shadow: none;
	border: none;
}

// Hide options panel
.znpb-element-options__hide {
	position: absolute;
	top: 16px;
	left: 100%;
	z-index: 0;
	display: flex;
	justify-content: center;
	align-items: center;
	width: 20px;
	height: 24px;
	color: var(--zb-surface-icon-color);
	background: var(--zb-surface-color);
	border: 1px solid var(--zb-surface-border-color);
	border-radius: 0 50% 50% 0;
	transform: translateX(-100%);
	transition: transform .15s 0s;
	cursor: pointer;

	&Icon {
		position: relative;
		left: -2px;
		font-size: 12px;
		transform: rotate(90deg);
		transition: color .15s;
	}

	&:hover &Icon {
		color: var(--zb-surface-text-hover-color);
	}

	.znpb-editor-panel--detached & {
		display: none;
	}

	.znpb-editor-panel--left & {
		border-left: 0;
	}

	.znpb-editor-panel--right & {
		right: 100%;
		left: auto;
		border-right: 0;
		border-radius: 50% 0 0 50%;
		transform: translateX(100%);
	}

	.znpb-editor-panel--right &Icon {
		left: 2px;
		transform: rotate(-90deg);
	}

	.znpb-element-options__panel-wrapper--hidden & {
		left: calc(100% + 2px);
		transition: opacity .15s;
		opacity: .6;

		&:hover {
			opacity: 1;
		}
	}

	.znpb-element-options__panel-wrapper--hidden.znpb-editor-panel--right & {
		right: calc(100% + 2px);
		left: auto;
	}

	body.znpb-theme-dark
	.znpb-element-options__panel-wrapper--hidden.znpb-editor-panel--right
	& {
		right: calc(100% + 1px);
		left: auto;
	}

	body.znpb-theme-dark .znpb-element-options__panel-wrapper--hidden & {
		left: calc(100% + 1px);
	}

	.znpb-element-options__panel-wrapper:hover &, .znpb-element-options__panel-wrapper--hidden & {
		z-index: 2;
		transform: translateX(0);
		transition: transform .15s 0s, z-index .15s .15s;
	}
}

// Hide icon
.znpb-element-options__panel-wrapper--hidden .znpb-element-options__hideIcon {
	left: -1px;
	transform: rotate(270deg);
}

.znpb-editor-panel--right.znpb-element-options__panel-wrapper--hidden
	.znpb-element-options__hideIcon {
	left: 1px;
	transform: rotate(-270deg);
}

.znpb-element-options__panel-wrapper {
	transition: margin-left .15s;
}

.znpb-element-options__panel-wrapper--hidden {
	margin-left: var(--optionsPanelWidth, -360px);
}

.znpb-editor-panel--right.znpb-element-options__panel-wrapper {
	transition: margin-right .15s;
}

.znpb-editor-panel--right.znpb-element-options__panel-wrapper--hidden {
	margin-right: var(--optionsPanelWidth, -360px);
	margin-left: 0;
}
</style>