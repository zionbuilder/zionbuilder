<template>
	<div
		class="znpb-element-options__media-class-pseudo-holder"
		@click="selectorIsOpen = !selectorIsOpen, contentOpen = false, newPseudoName=false"
		ref="root"
	>
		<span class="znpb-element-options__media-class-pseudo-name">
			{{ activePseudoSelector.name  }}
		</span>

		<Tooltip
			v-if="hasContent"
			:trigger="null"
			:show="showContentTooltip"
			:content="$translate('add_pseudo_content')"
			placement="top"
		>
			<Icon
				icon="edit"
				:size="12"
				class="znpb-pseudo-selector__edit"
				@click.stop="contentOpen=!contentOpen, selectorIsOpen=false"
			/>
		</Tooltip>
		<Tooltip
			:show-arrows="false"
			:show="selectorIsOpen"
			:trigger="null"
			append-to="element"
			placement="bottom-end"
			tooltipClass="hg-popper--no-padding znpb-element-options__media-class-pseudo-selector-dropdown"
		>
			<template #content>
				<div class="znpb-element-options__media-class-pseudo-selector-list hg-popper-list">
					<PseudoDropdownItem
						v-for="(selectorConfig, index) in computedPseudoSelectors"
						:selector="selectorConfig"
						:selectors-model="activePseudoSelectors"
						:key="index"
						:clearable="selectorConfig.canBeDeleted"
						@remove-styles="deleteConfigForPseudoSelector"
						@selector-selected="onPseudoSelectorSelected"
						@delete-selector="deletePseudoSelectorAndStyles"
						class="hg-popper-list__item"
					/>
				</div>
			</template>

			<div
				class="znpb-element-options__media-class-pseudo-title"
				:class="{'znpb-element-options__media-class-pseudo-title--has-edit' : activePseudoSelector.id===':before' || activePseudoSelector.id===':after' }"
			>
				<Icon
					icon="select"
					:rotate="selectorIsOpen ? 180 : null"
				/>

			</div>
		</Tooltip>

		<div
			class="znpb-element-options__media-class-pseudo-title__before-after-content"
			v-if="contentOpen"
			@click.stop=""
		>
			<BaseInput
				v-model="pseudoContentModel"
				ref="pseudoContentInput"
				:clearable="true"
				@keypress.enter="contentOpen = false"
				:placeholder="`Insert text ${activePseudoSelector.id} content`"
			/>
		</div>
		<div
			class="znpb-element-options__media-class-pseudo-title__before-after-content"
			v-if="newPseudoName"
			@click.stop=""
		>
			<BaseInput
				v-model="newPseudoModel"
				:clearable="true"
				ref="newpseudoInput"
				@keypress.enter="createNewPseudoSelector"
				:placeholder="$translate('new_pseudo')"
			/>
		</div>
	</div>
</template>

<script>
import { computed, ref } from 'vue'
import { cloneDeep, set, find } from 'lodash-es'
import { updateOptionValue } from '@zb/utils'
import { useResponsiveDevices, usePseudoSelectors } from '@zb/components'
import { useEditorData } from '@composables'

// Components
import PseudoDropdownItem from './PseudoDropdownItem.vue'

export default {
	name: 'PseudoSelectors',
	components: {
		PseudoDropdownItem
	},
	props: {
		modelValue: {
			type: [Object, Array],
			required: false,
			default: {}
		}
	},
	setup (props, { emit }) {
		const { activeResponsiveDeviceInfo } = useResponsiveDevices()
		const { pseudoSelectors, activePseudoSelector, setActivePseudoSelector, deleteCustomSelector, addCustomSelector } = usePseudoSelectors()
		const { editorData } = useEditorData()

		// Refs / Data
		const root = ref(null)
		const contentOpen = ref(false)
		const selectorIsOpen = ref(false)
		const showContentTooltip = ref(false)
		const newPseudoName = ref(false)
		const customPseudoName = ref('')

		const hasContent = computed(() => activePseudoSelector.value.id === ':before' || activePseudoSelector.value.id === ':after')
		const activePseudoSelectors = computed(() => ((props.modelValue || {}) || {})[activeResponsiveDeviceInfo.value.id] || {})
		const pseudoStyles = computed(() => (activePseudoSelectors.value || {})[activePseudoSelector.value.id] || {})
		const pseudoContentModel = computed({
			get () {
				return pseudoStyles.value.content || ''
			},
			set (newValue) {
				const cloneModelValue = cloneDeep(props.modelValue)
				const newValues = set(cloneModelValue, `${activeResponsiveDeviceInfo.value.id}.${activePseudoSelector.value.id}.content`, newValue)

				emit('update:modelValue', newValues)
			}
		})


		const computedPseudoSelectors = computed(() => {
			const savedSelectors = Object.keys(activePseudoSelectors.value)
			const customSelectors = savedSelectors.filter((selector) => {
				return !find(pseudoSelectors.value, ['id', selector])
			})

			// Combine selectors with custom selectors
			return [
				...pseudoSelectors.value,
				...customSelectors.map(selector => {
					return {
						name: selector,
						id: selector,
						canBeDeleted: true
					}
				})
			]
		})


		/**
		 * emit the change of the pseudoselector
		 */
		function onPseudoSelectorSelected (pseudoConfig) {
			selectorIsOpen.value = false

			setActivePseudoSelector(pseudoConfig || pseudoSelectors.value[0])
			if (activePseudoSelector.value.id === 'custom') {
				newPseudoName.value = true
			}
			if (pseudoContentModel.value === '' && (activePseudoSelector.value.id === 'before' || activePseudoSelector.value.id === 'after')) {
				showContentTooltip.value = false
				contentOpen.value = true
			}
		}

		function createNewPseudoSelector () {
			newPseudoName.value = false

			let newSel = {
				id: customPseudoName.value,
				name: customPseudoName.value,
				canBeDeleted: true
			}

			addCustomSelector(newSel)
			setActivePseudoSelector(newSel)
		}

		/**
		 * Close input if clicked outside of selector
		 */
		function closePanel (event) {
			if (!root.value.contains(event.target)) {
				contentOpen.value = false
				selectorIsOpen.value = false
				newPseudoName.value = false
			}
		}

		function deleteConfigForPseudoSelector (pseudoSelectorId) {

			const newValues = {
				...props.modelValue,
				[activeResponsiveDeviceInfo.value.id]: {
					...props.modelValue[activeResponsiveDeviceInfo.value.id]
				}
			}

			delete newValues[activeResponsiveDeviceInfo.value.id][pseudoSelectorId]

			// Check if there are any remaining styles for this responsive device
			if (Object.keys((newValues[activeResponsiveDeviceInfo.value.id]) || {}).length === 0) {
				delete newValues[activeResponsiveDeviceInfo.value.id]
			}

			emit('update:modelValue', newValues)
		}

		function deletePseudoSelectorAndStyles (selector) {
			deleteConfigForPseudoSelector(selector.id)
			deleteCustomSelector(selector)
		}

		return {
			// Data
			root,
			contentOpen,
			selectorIsOpen,
			showContentTooltip,
			newPseudoName,
			customPseudoName,
			activeResponsiveDeviceInfo,
			computedPseudoSelectors,
			activePseudoSelector,
			hasContent,
			activePseudoSelectors,
			plugin_info: editorData.value.plugin_info,
			// Computed
			pseudoContentModel,
			// Methods
			onPseudoSelectorSelected,
			deleteConfigForPseudoSelector,
			createNewPseudoSelector,
			closePanel,
			deletePseudoSelectorAndStyles
		}
	},
	computed: {
		isPro () {
			return this.plugin_info.is_pro_active
		},

		pseudoSelectors () {
			return this.pseudoSelectors.map((selectorConfig) => {
				const returnedSelector = {
					...selectorConfig,
					active: true
				}

				if (!['default', ':hover'].includes(selectorConfig.id) && !this.isPro) {
					returnedSelector.active = false
					returnedSelector.label = {
						text: this.$translate('pro'),
						type: 'warning'
					}
				}

				return returnedSelector
			})
		},

		newPseudoModel: {
			get () {
				return this.customPseudoName
			},
			set (newVal) {
				this.customPseudoName = newVal.split(' ').join('').toLowerCase()
			}
		}
	},
	watch: {
		hasContent: function (newValue) {
			if (newValue) {
				this.showContentTooltip = true
				setTimeout(() => {
					this.showContentTooltip = false
				}, 2000)
			}
		},
		selectorIsOpen: function (newValue, oldValue) {
			if (newValue) {
				document.addEventListener('click', this.closePanel)
			} else {
				document.removeEventListener('click', this.closePanel)
			}
		},
		contentOpen: function (newValue, oldValue) {

			if (newValue) {
				this.$nextTick(() => this.$refs.pseudoContentInput.focus())
				document.addEventListener('click', this.closePanel)
			} else {
				this.$refs.pseudoContentInput.blur()
				document.removeEventListener('click', this.closePanel)
			}
		},
		newPseudoName: function (newValue, oldValue) {
			if (newValue) {
				this.$nextTick(() => this.$refs.newpseudoInput.focus())
				document.addEventListener('click', this.closePanel)
			} else {
				this.$refs.newpseudoInput.blur()
				document.removeEventListener('click', this.closePanel)
			}
		}
	},
	beforeUnmount () {
		document.removeEventListener('click', this.closePanel)
	}
}
</script>

<style lang="scss">
.znpb-pseudo-selector {
	&__edit {
		padding: 11px 8px;
	}
}
.znpb-element-options__media-class-pseudo-name {
	padding-left: 10px;
	font-size: 13px;
	font-weight: 500;
}
.znpb-element-options__media-class-pseudo {
	&-holder {
		display: flex;
		justify-content: space-between;
		align-items: center;
		flex: 4;
		color: darken($font-color, 10%);
		border: 2px solid #e5e5e5;
		border-radius: 3px;
		cursor: pointer;
	}

	&-title {
		display: flex;
		color: $font-color;
		border-left: 2px solid #e5e5e5;
		cursor: pointer;

		.znpb-editor-icon-wrapper {
			padding: 11px;
		}
		&--has-edit {
			// margin-right: 5px;
		}

		&__before-after-content {
			position: absolute;
			top: 110%;
			left: 0;
			z-index: 20000;
			width: 100%;
			padding: 15px;
			color: $surface-headings-color;
			background-color: #fff;
			box-shadow: 0 2px 15px 0 rgba(0, 0, 0, .1);
			border: 1px solid #f1f1f1;
			border-radius: 4px;
			input::placeholder {
				color: $surface-headings-color;
			}
		}
	}

	&-selector-dropdown {
		min-width: 130px;
	}

	&-selector-list {
		display: flex;
		flex-direction: column;
	}
}
</style>
