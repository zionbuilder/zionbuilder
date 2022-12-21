<template>
	<Tooltip
		v-model:show="showAddModal"
		:trigger="null"
		placement="bottom"
		append-to="element"
		strategy="fixed"
		tooltip-class="znpb-option-cssSelectorChildActionAddTooltip"
		:close-on-outside-click="true"
		@hide="onFormClose"
	>
		<template #content>
			<div @click.stop>
				<OptionsForm v-model="computedFormModel" class="znpb-option-cssSelectorChildActionAddForm" :schema="schema" />
				<Button class="znpb-button--line znpb-option-cssSelectorChildActionAddButton" :type="buttonType" @click="add">
					{{ buttonTitle }}
				</Button>
			</div>
		</template>

		<div class="znpb-option-cssSelectorChildActions">
			<slot
				:actions="{
					openModal,
					closeModal,
					toggleModal,
				}"
			/>
		</div>
	</Tooltip>
</template>

<script>
import { __ } from '@wordpress/i18n';
import { ref, computed } from 'vue';

export default {
	name: 'AddSelector',
	props: {
		type: {
			type: String,
			required: false,
			default: 'selector',
		},
	},
	setup(props, { emit }) {
		const showAddModal = ref(false);
		const hasError = ref(false);

		// Options form schema
		const schema = computed(() => {
			return {
				title: {
					type: 'text',
					title:
						props.type === 'selector'
							? __('Selector nice name', 'zionbuilder')
							: __('CSS class nice name', 'zionbuilder'),
					description:
						props.type === 'selector'
							? __('Enter a name that will help you recognize this CSS class', 'zionbuilder')
							: __('Enter a name that will help you recognize this CSS class', 'zionbuilder'),
				},
				selector: {
					type: 'text',
					title: props.type === 'selector' ? __('CSS selector', 'zionbuilder') : __('CSS class', 'zionbuilder'),
					description:
						props.type === 'selector'
							? __('Enter the css selector you want to style', 'zionbuilder')
							: __('Enter the CSS class name without the leading dot', 'zionbuilder'),
					placeholder:
						props.type === 'selector' ? __('.my-selector', 'zionbuilder') : __('my-class-name', 'zionbuilder'),
					error: props.type === 'class' && hasError.value ? true : false,
				},
			};
		});

		const buttonTitle = computed(() => {
			return props.type == 'selector' ? __('Add child selector', 'zionbuilder') : __('Add CSS class', 'zionbuilder');
		});

		const formModel = ref({});
		const computedFormModel = computed({
			get() {
				return formModel.value;
			},
			set(newValue) {
				if (null === newValue) {
					formModel.value = {};
				} else {
					formModel.value = newValue;
				}
			},
		});

		const canSave = computed(() => {
			return (
				formModel.value.title &&
				formModel.value.title.length > 0 &&
				formModel.value.selector &&
				formModel.value.selector.length > 0
			);
		});

		const buttonType = computed(() => {
			if (!canSave.value) {
				return 'disabled';
			}

			return '';
		});

		function openModal() {
			showAddModal.value = true;
		}

		function closeModal() {
			showAddModal.value = false;
		}

		function toggleModal() {
			showAddModal.value = !showAddModal.value;
		}

		function onFormClose() {
			// close modal
			showAddModal.value = false;

			// Clear form
			formModel.value = {};
		}

		function add() {
			if (!canSave.value) {
				return;
			}

			// Check if we are in class name mode
			if (props.type === 'class') {
				// Check if this is a valid css class
				if (!/^[a-z_-][a-z\d_-]*$/i.test(formModel.value.selector) || formModel.value.selector.split('')[0] === '-') {
					hasError.value = true;
					setTimeout(() => {
						hasError.value = false;
					}, 500);
					return;
				}
			}

			// Send data to parent
			emit('add-selector', formModel.value);

			// close modal
			showAddModal.value = false;

			// Clear form
			formModel.value = {};
		}

		return {
			// computed
			buttonTitle,
			schema,
			showAddModal,
			computedFormModel,
			buttonType,

			// Methods
			openModal,
			closeModal,
			toggleModal,
			onFormClose,
			add,
		};
	},
};
</script>

<style lang="scss">
.znpb-option-cssSelectorChildActionAddTooltip {
	max-width: 320px;
}

.znpb-option-cssSelectorChildActionAddForm {
	padding: 10px 10px 0 10px;
}

.znpb-option-cssSelectorChildActionAddButton {
	display: flex;
	margin: 0 15px 15px 15px !important;
	text-align: center;
}

.znpb-option-cssSelectorChildActions {
	display: flex;
	user-select: none;

	& > .znpb-editor-icon-wrapper {
		display: flex;
		justify-content: center;
		align-items: center;
		width: 30px;
		height: 30px;
		background: var(--zb-surface-lightest-color);
		border-radius: 3px;
		transition: background 0.2s;
	}
}

.znpb-option-cssSelectorChildActionsChildNumber {
	display: flex;
	align-items: center;
	margin-right: 12px;

	.znpb-editor-icon-wrapper {
		margin-right: 3px;
	}
}
</style>
