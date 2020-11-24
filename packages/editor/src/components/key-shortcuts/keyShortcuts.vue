<template>
	<div class="znpb-key-shortcuts-modal znpb-fancy-scrollbar">
		<div class="znpb-key-shortcuts-modal__content">
			<keyShortcutsItem
				v-for="(schema,i) in schemaDescriptionFirst"
				:key="i+schema.description"
				:keyshortcut="schema.keyshortcut"
				:description="schema.description"
			>

				<pre
					v-if="schema.details"
					class="znpb-key-shortcuts-modal__item-details"
				>{{schema.details}}</pre>
			</keyShortcutsItem>
		</div>
	</div>
</template>

<script>
import keyShortcutsItem from './keyShortcutsItem.vue'
export default {
	name: 'keyShortcutsModal',
	components: {
		keyShortcutsItem
	},
	data () {
		return {
			schemaDescriptionFirst: [
				{
					'keyshortcut': ['CTRL', 'S'],
					'description': this.$translate('save_changes')
				},
				{
					'keyshortcut': ['CTRL', 'C'],
					'description': this.$translate('copy_element')
				},
				{
					'keyshortcut': ['CTRL', 'V'],
					'description': this.$translate('paste_element')
				},
				{
					'keyshortcut': ['CTRL', 'X'],
					'description': this.$translate('cut_element')
				},
				{
					'keyshortcut': ['CTRL', 'D'],
					'description': this.$translate('duplicate_element')
				},
				{
					'keyshortcut': ['CTRL', 'SHIFT', 'C'],
					'description': this.$translate('copy_element_styles')
				},
				{
					'keyshortcut': ['CTRL', 'SHIFT', 'V'],
					'description': this.$translate('paste_element_styles')
				},
				{
					'keyshortcut': ['CTRL', 'Z'],
					'description': this.$translate('undo')
				},
				{
					'keyshortcut': ['CTRL', 'Y'],
					'description': this.$translate('redo')
				},
				{
					'keyshortcut': ['CTRL', 'SHIFT', 'Y'],
					'description': this.$translate('redo')
				},
				{
					'keyshortcut': ['CTRL', 'H'],
					'description': this.$translate('hide_element')
				},
				{
					'keyshortcut': ['CTRL', 'P'],
					'description': this.$translate('toggle_preview')
				},
				{
					'keyshortcut': ['SHIFT', 'T'],
					'description': this.$translate('toggle_tree_view_panel')
				},
				{
					'keyshortcut': ['SHIFT', 'L'],
					'description': this.$translate('toggle_library')
				},
				{
					'keyshortcut': ['SHIFT', 'O'],
					'description': this.$translate('toggle_page_options')
				},
				{
					'keyshortcut': ['DRAG', 'CTRL'],
					'description': this.$translate('duplicate_element_in_place'),
					'details': this.$translate('when_dragging_element')
				},
				{
					'keyshortcut': ['CTRL', 'DRAG'],
					'description': this.$translate('set_even_values'),
					'details': this.$translate('when_dragging_toolbox')
				},
				{
					'keyshortcut': ['CTRL', 'SHIFT', 'DRAG'],
					'description': this.$translate('set_even_incremental_value')
				},
				{
					'keyshortcut': ['CTRL', 'SHIFT', 'D'],
					'description': this.$translate('back_to_wp_dashboard')
				},
				{
					'keyshortcut': ['SHIFT', 'DRAG'],
					'description': this.$translate('set_incremental_value'),
					'details': this.$translate('when_dragging_input_number')
				},
				{
					'keyshortcut': ['SHIFT', 'ARROWS'],
					'description': this.$translate('set_incremental_value')
				},
				{
					'keyshortcut': ['ALT'],
					'description': 'Toggle Link',
					'details': this.$translate('on_input_number_link')
				}
			]
		}
	}
}
</script>

<style lang="scss">
.znpb-key-shortcuts-modal {
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	padding: 30px;
	&__item-details {
		margin-top: 0;
		margin-bottom: 20px;
		text-align: center;
	}
	&__content {
		display: flex;
		flex-direction: column;
		height: 100%;
		min-height: 0;
	}
}
</style>
