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
import { ref } from 'vue'

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

		function add () {
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
			onFormClose
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
	border: 1px solid #a7a4a4;
	border-radius: 3px;
	user-select: none;

	& > * {
		padding: 3px;
	}
}

.znpb-option-cssSelectorChildActionsChildNumber {
	display: flex;
	align-items: center;
	padding-right: 5px;
	border-right: 1px solid;
}
</style>