<template>
	<Tooltip
		:trigger="null"
		:show="showAddModal"
		placement="bottom"
		append-to="element"
		strategy="fixed"
		tooltip-class="znpb-option-cssSelectorChildActionAddTooltip"
		:close-on-outside-click="true"
		@hide="onFormClose"
	>
		<template v-slot:content>
			<div @click.stop>
				<OptionsForm
					class="znpb-option-cssSelectorChildActionAddForm"
					:schema="schema"
					v-model="formModel"
				/>
				<Button
					@click="add"
					class="znpb-option-cssSelectorChildActionAddButton"
					:type="buttonType"
				>
					Add child selector
				</Button>
			</div>
		</template>

		<div class="znpb-option-cssSelectorChildActions">
			<template v-if="childSelectors.length === 0">
				<!-- No childs yet -->
				<Tooltip
					content="Add new inner selector"
					placement="top"
					append-to="element"
					strategy="fixed"
					@hide="showAddModal = false"
					@click.stop="showAddModal = !showAddModal"
				>
					<Icon icon="child-add" />
				</Tooltip>

			</template>
			<template v-else>
				<!-- ICON -->
				<span
					@click.stop="showChilds = !showChilds, $emit('toggle-view-childs')"
					class="znpb-option-cssSelectorChildActionsChildNumber"
				>
					<Icon icon="child" />
					{{childSelectors.length}}
				</span>
				<Tooltip
					content="Add new inner selector"
					placement="top"
					append-to="element"
					strategy="fixed"
					@hide="showAddModal = false"
					@click.stop="showAddModal = !showAddModal"
				>
					<Icon icon="plus" />
				</Tooltip>

			</template>
		</div>
	</Tooltip>

</template>

<script>
import { ref, computed } from 'vue'

export default {
	name: 'AddChildActions',
	props: {
		childSelectors: {
			type: Array,
			default: []
		}
	},
	setup (props, { emit }) {
		const showAddModal = ref(false)

		// Options form schema
		const schema = {
			title: {
				type: 'text',
				title: 'Selector nice name',
				description: 'Enter a name for this selector'
			},
			selector: {
				type: 'text',
				title: 'CSS selector',
				description: 'Enter the css selector you want to style'
			}
		}

		const formModel = ref({})

		const canSave = computed(() => {
			return formModel.value.title && formModel.value.title.length > 0 && formModel.value.selector && formModel.value.selector.length > 0
		})

		const buttonType = computed(() => {
			if (!canSave.value) {
				return 'gray'
			}

			return ''
		})

		function add () {
			if (!canSave.value) {
				return
			}

			// Send data to parent
			emit('addChild', formModel.value)

			// close modal
			showAddModal.value = false

			// Clear form
			formModel.value = {}
		}

		function onFormClose () {
			// close modal
			showAddModal.value = false

			// Clear form
			formModel.value = {}
		}

		return {
			showAddModal,
			add,
			schema,
			formModel,
			onFormClose,
			buttonType
		}
	}
}
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

	& > [placement="top"] {
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