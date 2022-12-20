<template>
	<div
		ref="root"
		class="znpb-element-options__media-class-pseudo-holder"
		@click="(selectorIsOpen = !selectorIsOpen), (contentOpen = false), (newPseudoName = false)"
	>
		<span class="znpb-element-options__media-class-pseudo-name">
			{{ activePseudoSelector.name }}
		</span>

		<Tooltip
			v-if="hasContent"
			:trigger="null"
			:show="showContentTooltip"
			:content="__('Click to add content for pseudo selector.', 'zionbuilder')"
			placement="top"
		>
			<Icon
				icon="edit"
				:size="12"
				class="znpb-pseudo-selector__edit"
				@click.stop="(contentOpen = !contentOpen), (selectorIsOpen = false)"
			/>
		</Tooltip>
		<Tooltip
			:show-arrows="false"
			:show="selectorIsOpen"
			:trigger="null"
			append-to="element"
			placement="bottom-end"
			tooltip-class="hg-popper--no-padding znpb-element-options__media-class-pseudo-selector-dropdown"
		>
			<template #content>
				<div class="znpb-element-options__media-class-pseudo-selector-list hg-popper-list">
					<PseudoDropdownItem
						v-for="(selectorConfig, index) in computedPseudoSelectors"
						:key="index"
						:selector="selectorConfig"
						:selectors-model="activePseudoSelectors"
						:clearable="selectorConfig.canBeDeleted"
						class="hg-popper-list__item"
						@remove-styles="deleteConfigForPseudoSelector"
						@selector-selected="onPseudoSelectorSelected"
						@delete-selector="deletePseudoSelectorAndStyles"
					/>
				</div>
			</template>

			<div
				class="znpb-element-options__media-class-pseudo-title"
				:class="{
					'znpb-element-options__media-class-pseudo-title--has-edit':
						activePseudoSelector.id === ':before' || activePseudoSelector.id === ':after',
				}"
			>
				<Icon icon="select" :rotate="selectorIsOpen ? 180 : null" />
			</div>
		</Tooltip>

		<div v-if="contentOpen" class="znpb-element-options__media-class-pseudo-title__before-after-content" @click.stop="">
			<BaseInput
				ref="pseudoContentInput"
				v-model="pseudoContentModel"
				:clearable="true"
				:placeholder="`Insert text ${activePseudoSelector.id} content`"
				@keypress.enter="contentOpen = false"
			/>
		</div>
		<div
			v-if="newPseudoName"
			class="znpb-element-options__media-class-pseudo-title__before-after-content"
			@click.stop=""
		>
			<BaseInput
				ref="newpseudoInput"
				v-model="newPseudoModel"
				:clearable="true"
				:placeholder="__('Add new pseudo-selector ex: :hover::before ', 'zionbuilder')"
				@keypress.enter="createNewPseudoSelector"
			/>
		</div>
	</div>
</template>

<script>
import { __ } from '@wordpress/i18n';
import { computed, ref, onBeforeUnmount } from 'vue';
import { cloneDeep, set, find } from 'lodash-es';
import { useEditorData } from '/@/editor/composables';

// Components
import PseudoDropdownItem from './PseudoDropdownItem.vue';

const { useResponsiveDevices, usePseudoSelectors } = window.zb.composables;

export default {
	name: 'PseudoSelectors',
	components: {
		PseudoDropdownItem,
	},
	props: {
		modelValue: {
			type: [Object, Array],
			required: false,
			default: {},
		},
	},
	setup(props, { emit }) {
		const { activeResponsiveDeviceInfo } = useResponsiveDevices();
		const { pseudoSelectors, activePseudoSelector, setActivePseudoSelector, deleteCustomSelector, addCustomSelector } =
			usePseudoSelectors();
		const { editorData } = useEditorData();

		// Refs / Data
		const root = ref(null);
		const contentOpen = ref(false);
		const selectorIsOpen = ref(false);
		const showContentTooltip = ref(false);
		const newPseudoName = ref(false);
		const customPseudoName = ref('');

		const hasContent = computed(
			() => activePseudoSelector.value.id === ':before' || activePseudoSelector.value.id === ':after',
		);
		const activePseudoSelectors = computed(
			() => (props.modelValue || {} || {})[activeResponsiveDeviceInfo.value.id] || {},
		);
		const pseudoStyles = computed(() => (activePseudoSelectors.value || {})[activePseudoSelector.value.id] || {});
		const pseudoContentModel = computed({
			get() {
				return pseudoStyles.value.content || '';
			},
			set(newValue) {
				const cloneModelValue = cloneDeep(props.modelValue);
				const newValues = set(
					cloneModelValue,
					`${activeResponsiveDeviceInfo.value.id}.${activePseudoSelector.value.id}.content`,
					newValue,
				);

				emit('update:modelValue', newValues);
			},
		});

		const computedPseudoSelectors = computed(() => {
			const savedSelectors = Object.keys(activePseudoSelectors.value);
			const customSelectors = savedSelectors.filter(selector => {
				return !find(pseudoSelectors.value, ['id', selector]);
			});

			// Combine selectors with custom selectors
			return [
				...pseudoSelectors.value,
				...customSelectors.map(selector => {
					return {
						name: selector,
						id: selector,
						canBeDeleted: true,
					};
				}),
			];
		});

		/**
		 * emit the change of the pseudoselector
		 */
		function onPseudoSelectorSelected(pseudoConfig) {
			selectorIsOpen.value = false;

			setActivePseudoSelector(pseudoConfig || pseudoSelectors.value[0]);
			if (activePseudoSelector.value.id === 'custom') {
				newPseudoName.value = true;
			}
			if (
				pseudoContentModel.value === '' &&
				(activePseudoSelector.value.id === 'before' || activePseudoSelector.value.id === 'after')
			) {
				showContentTooltip.value = false;
				contentOpen.value = true;
			}
		}

		function createNewPseudoSelector() {
			newPseudoName.value = false;

			let newSel = {
				id: customPseudoName.value,
				name: customPseudoName.value,
				canBeDeleted: true,
			};

			addCustomSelector(newSel);
			setActivePseudoSelector(newSel);
		}

		/**
		 * Close input if clicked outside of selector
		 */
		function closePanel(event) {
			if (!root.value.contains(event.target)) {
				contentOpen.value = false;
				selectorIsOpen.value = false;
				newPseudoName.value = false;
			}
		}

		function deleteConfigForPseudoSelector(pseudoSelectorId) {
			const newValues = {
				...props.modelValue,
				[activeResponsiveDeviceInfo.value.id]: {
					...props.modelValue[activeResponsiveDeviceInfo.value.id],
				},
			};

			delete newValues[activeResponsiveDeviceInfo.value.id][pseudoSelectorId];

			// Check if there are any remaining styles for this responsive device
			if (Object.keys(newValues[activeResponsiveDeviceInfo.value.id] || {}).length === 0) {
				delete newValues[activeResponsiveDeviceInfo.value.id];
			}

			emit('update:modelValue', newValues);
		}

		function deletePseudoSelectorAndStyles(selector) {
			deleteConfigForPseudoSelector(selector.id);
			deleteCustomSelector(selector);
		}

		// Lifecycle
		onBeforeUnmount(() => {
			// Clear active pseudo selector
			setActivePseudoSelector(null);
		});

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
			deletePseudoSelectorAndStyles,
		};
	},
	computed: {
		isPro() {
			return this.plugin_info.is_pro_active;
		},

		pseudoSelectors() {
			return this.pseudoSelectors.map(selectorConfig => {
				const returnedSelector = {
					...selectorConfig,
					active: true,
				};

				if (!['default', ':hover'].includes(selectorConfig.id) && !this.isPro) {
					returnedSelector.active = false;
					returnedSelector.label = {
						text: __('pro', 'zionbuilder'),
						type: 'warning',
					};
				}

				return returnedSelector;
			});
		},

		newPseudoModel: {
			get() {
				return this.customPseudoName;
			},
			set(newVal) {
				this.customPseudoName = newVal.split(' ').join('').toLowerCase();
			},
		},
	},
	watch: {
		hasContent: function (newValue) {
			if (newValue) {
				this.showContentTooltip = true;
				setTimeout(() => {
					this.showContentTooltip = false;
				}, 2000);
			}
		},
		selectorIsOpen: function (newValue, oldValue) {
			if (newValue) {
				document.addEventListener('click', this.closePanel);
			} else {
				document.removeEventListener('click', this.closePanel);
			}
		},
		contentOpen: function (newValue, oldValue) {
			if (newValue) {
				this.$nextTick(() => this.$refs.pseudoContentInput.focus());
				document.addEventListener('click', this.closePanel);
			} else {
				this.$refs.pseudoContentInput.blur();
				document.removeEventListener('click', this.closePanel);
			}
		},
		newPseudoName: function (newValue, oldValue) {
			if (newValue) {
				this.$nextTick(() => this.$refs.newpseudoInput.focus());
				document.addEventListener('click', this.closePanel);
			} else {
				this.$refs.newpseudoInput.blur();
				document.removeEventListener('click', this.closePanel);
			}
		},
	},
	beforeUnmount() {
		document.removeEventListener('click', this.closePanel);
	},
};
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
		color: var(--zb-input-text-color);
		background: var(--zb-input-bg-color);
		border: 2px solid var(--zb-input-border-color);
		border-radius: 3px;
		cursor: pointer;
	}

	&-title {
		display: flex;
		color: var(--zb-surface-text-color);
		border-left: var(--zb-input-separator-width) solid var(--zb-input-separator-color);
		cursor: pointer;

		.znpb-editor-icon-wrapper {
			padding: 11px;
		}

		// &--has-edit {
		// 	margin-right: 5px;
		// }

		&__before-after-content {
			position: absolute;
			top: 110%;
			left: 0;
			z-index: 20000;
			width: 100%;
			padding: 15px;
			color: var(--zb-dropdown-text-color);
			background-color: var(--zb-dropdown-bg-color);
			box-shadow: 0 2px 15px 0 var(--zb-dropdown-shadow);
			border: 1px solid var(--zb-dropdown-border-color);
			border-radius: 4px;
			input::placeholder {
				color: var(--zb-input-placeholder-color);
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
