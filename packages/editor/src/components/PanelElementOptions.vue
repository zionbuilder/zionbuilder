<template>
	<BasePanel
		class="znpb-element-options__panel-wrapper"
		@close-panel="closeOptionsPanel"
		:panel-name="`${element.name} ${$translate('options')}`"
		panel-id="PanelElementOptions"
		:show-move="true"
		:show-expand="false"
		:allow-horizontal-resize="false"
		:allow-vertical-resize="false"
		:panel="panel"
	>
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
						v-if="showBreadcrumbs"
						:element="element"
					/>
				</h4>

			</div>
		</template>
		<div class="znpb-element-options-content-wrapper">
			<Tabs
				:has-scroll="['general','advanced']"
				:activeTab="activeKeyTab"
				@changed-tab="changeTab"
				class="znpb-element-options__tabs-wrapper"
			>
				<Tab
					name="General"
					v-if="element.elementTypeModel.hasOwnProperty('options')"
				>
					<OptionsForm
						class="znpb-element-options-content-form"
						:schema="element.elementTypeModel.options"
						v-model="elementOptions"
					/>
				</Tab>
				<Tab name="Styling">
					<OptionsForm
						:schema="computedStyleOptionsSchema"
						v-model="computedStyleOptions"
					/>
				</Tab>
				<Tab name="Advanced">
					<OptionsForm
						class="znpb-element-options-content-form"
						:schema="getSchema('element_advanced')"
						v-model="advancedOptionsModel"
					/>
				</Tab>
				<Tab name="Search">
					<template #title>
						<div
							@click="toggleSearchIcon"
							class="znpb-element-options__search-tab-title"
						>
							<Icon :icon="searchIcon" />
						</div>

						<BaseInput
							v-if="searchActive"
							v-model="optionsFilterKeyword"
							:filterable="true"
							ref="search"
							placeholder="Search option"
							class="znpb-tabs__header-item-search-options"
						>
						</BaseInput>
					</template>
					<p
						class="znpb-element-options-default-message"
						v-if="this.optionsFilterKeyword.length > 2 && this.filteredOptions.length === 0"
					>
						{{noOptionFoundMessage}}
					</p>
					<p
						v-if="this.optionsFilterKeyword.length < 3"
						class="znpb-element-options-no-option-message"
					>
						{{defaultMessage}}
					</p>
					<OptionsForm
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
import { cloneDeep } from 'lodash-es'
import { on, off } from '@zb/hooks'
import { debounce } from '@zb/utils'
import { useEditElement, useElementProvide, useEditorData, useWindows, useHistory } from '@composables'
import { useOptionsSchemas } from '@zb/components'

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
		let ignoreLocalHistory = false
		const { element, editElement, unEditElement } = useEditElement()
		const { provideElement } = useElementProvide()
		const { getSchema } = useOptionsSchemas()
		const activeKeyTab = ref(null)
		const searchActive = ref(false)
		const history = ref([])
		const historyIndex = ref(0)

		const elementOptions = computed({
			get () {
				return element.value.options
			},
			set (newValues) {
				element.value.updateOptions(newValues)
				// Add to history
				if (!ignoreLocalHistory) {
					addToLocalHistory()
					ignoreLocalHistory = false
				}
			}
		})

		const canUndo = computed(() => {
			return historyIndex.value > 0
		})

		const canRedo = computed(() => {
			return historyIndex.value + 1 < history.value.length
		})

		const hasChanges = computed(() => {
			history.value.length > 1 && historyIndex.value > 0
		})

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
		watch(element, () => {
			activeKeyTab.value = 'general'
			searchActive.value = false
		})

		provideElement(element)
		provide('elementInfo', element)
		provide('OptionsFormTopModelValue', elementOptions)

		return {
			element,
			// Computed
			elementOptions,
			getSchema,
			editElement,
			unEditElement,
			activeKeyTab,
			searchActive,
			canUndo,
			canRedo,
			// Methods
			undo,
			redo
		}
	},
	data () {
		return {
			showBreadcrumbs: false,
			elementClasses: [],
			lastTab: null,
			optionsFilterKeyword: '',
			noOptionMessage: '',
			defaultMessage: this.$translate('element_options_default_message'),
			noOptionFoundMessage: 'No options found with this keyword'
		}
	},
	computed: {
		computedStyleOptionsSchema () {
			const schema = {}
			let styledElements = this.element.elementTypeModel.style_elements

			Object.keys(styledElements).forEach(styleId => {
				const config = styledElements[styleId]
				schema[styleId] = {
					type: 'accordion_menu',
					title: config.title,
					id: styleId,
					icon: 'brush',
					child_options: {
						'styles': {
							type: 'element_styles',
							id: 'styles',
							is_layout: true,
							selector: config.selector.replace('{{ELEMENT}}', this.element.uid),
							title: config.title
						}
					}
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
		advancedOptionsModel: {
			get () {
				return this.elementOptions._advanced_options || {}
			},
			set (newValues) {
				this.elementOptions = {
					...this.elementOptions,
					_advanced_options: newValues
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
		searchIcon () {
			return this.searchActive ? 'close' : 'search'
		},
		filteredOptions () {
			const keyword = this.optionsFilterKeyword
			if (keyword.length > 2) {
				return this.filterOtions(keyword, this.allOptionsSchema)
			}
			return {}
		}
	},
	created () {
		on('change-tab-styling', this.changeTabByEvent)
	},
	methods: {
		onBackButtonClick () {
			if (this.element.parent && this.element.parent.elementTypeModel.element_type !== 'contentRoot') {
				this.editElement(this.element.parent)
			}
		},
		changeTabByEvent (event) {
			if (event !== undefined) {
				this.activeKeyTab = event.detail
			}
		},
		filterOtions (keyword, optionsSchema, currentId) {
			let lowercaseKeyword = keyword.toLowerCase()
			let foundOptions = {}

			Object.keys(optionsSchema).forEach((optionId) => {
				const optionConfig = optionsSchema[optionId]

				let syncValue = []
				if (!optionConfig.sync) {
					if (currentId) {
						syncValue.push(...currentId)
					}

					if (!optionConfig.is_layout) {
						syncValue.push(optionId)
					}

					if (optionConfig.type === 'element_styles') {
						syncValue.push('styles')
					}

					if (optionConfig.type === 'responsive_group') {
						syncValue.push('%%RESPONSIVE_DEVICE%%')
					}

					if (optionConfig.type === 'pseudo_group') {
						syncValue.push('%%PSEUDO_SELECTOR%%')
					}
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

				if (optionConfig.type !== 'accordion_menu') {
					if (searchOptions.join(' ').toLowerCase().indexOf(lowercaseKeyword) !== -1) {
						foundOptions[syncValue.join('.')] = {
							...optionConfig,
							id: syncValue.join('.'),
							sync: optionConfig.sync || syncValue.join('.')
						}
					}
				}

				if (optionConfig.type === 'repeater') {
					return
				}

				if (optionConfig.type === 'element_styles') {
					const childOptions = this.filterOtions(keyword, this.getSchema('element_styles'), syncValue)

					foundOptions = {
						...foundOptions,
						...childOptions
					}
				}

				if (optionConfig.child_options && Object.keys(optionConfig.child_options).length > 0) {
					const childOptions = this.filterOtions(keyword, optionConfig.child_options, syncValue)

					foundOptions = {
						...foundOptions,
						...childOptions
					}
				}
			})

			return foundOptions
		},
		toggleSearchIcon () {
			this.searchActive = !this.searchActive
			if (!this.searchActive) {
				this.activeKeyTab = this.lastTab
			}
		},

		changeTab (tabId) {
			this.activeKeyTab = tabId

			if (tabId !== 'search') {
				this.lastTab = this.activeKeyTab
				this.optionsFilterKeyword = ''
			}

			if (!this.searchActive) {
				this.activeKeyTab = this.lastTab
			}

			if (tabId === 'search' && this.searchActive) {
				if (this.$refs.search) {
					this.$refs.search.focus()
				}
			}
		},
		closeOptionsPanel () {
			if (this.hasChanges) {
				const { addToHistory } = useHistory()
				const elementSavedName = this.element.name
				addToHistory(`Edited ${elementSavedName}`)
			}

			this.panel.close()
			this.unEditElement()
		},
		onKeyPress (e) {
			// Undo CTRL+Z
			if (e.which === 90 && e.ctrlKey && !e.shiftKey) {
				this.undo()
				e.preventDefault()
				e.stopPropagation()
			}
			// Redo CTRL+SHIFT+Z CTRL + Y
			if ((e.which === 90 && e.ctrlKey && e.shiftKey) || (e.ctrlKey && e.which === 89)) {
				this.redo()
				e.preventDefault()
				e.stopPropagation()
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
		off('change-tab-styling', this.changeTab)
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
				color: $icons-color;
				font-size: 14px;
				background-color: $surface-variant;
				border-radius: 3px;
				transition: .15s all;
				transition: all .3s;
				cursor: pointer;

				&:hover {
					color: darken($icons-color, 5%);
					background-color: darken($surface-variant, 2%);
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
			background-color: #fff;
			box-shadow: 0 2px 15px 0 rgba(0, 0, 0, .1);
			border: 1px solid #f1f1f1;
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
		flex-grow: 1;
		overflow: hidden;

		.znpb-element-options__tabs-wrapper {
			padding-top: 20px;
			background-color: $surface-variant;
		}
		.znpb-tabs {
			display: flex;
			flex-direction: column;
			height: calc(100% - 20px);
			.znpb-tabs__content, .znpb-tabs__wrapper {
				height: 100%;
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
			background-color: $surface-variant;
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
		background: $surface-variant;
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
	background: $surface;
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
</style>
