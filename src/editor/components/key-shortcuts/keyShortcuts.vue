<template>
	<div class="znpb-key-shortcuts-modal znpb-fancy-scrollbar">
		<div class="znpb-key-shortcuts-modal__content">
			<keyShortcutsItem
				v-for="(schema, i) in schemaDescriptionFirst"
				:key="i + schema.description"
				:keyshortcut="schema.keyshortcut"
				:description="schema.description"
			>
				<pre v-if="schema.details" class="znpb-key-shortcuts-modal__item-details">{{ schema.details }}</pre>
			</keyShortcutsItem>
		</div>
	</div>
</template>

<script>
import { __ } from '@wordpress/i18n';
import keyShortcutsItem from './keyShortcutsItem.vue';

const { Environment } = window.zb.utils;

export default {
	name: 'KeyShortcutsModal',
	components: {
		keyShortcutsItem,
	},
	data() {
		const controlKey = Environment.isMac ? '⌘' : '⌃';
		return {
			schemaDescriptionFirst: [
				{
					keyshortcut: [controlKey, 'S'],
					description: __('Save changes', 'zionbuilder'),
				},
				{
					keyshortcut: [controlKey, 'C'],
					description: __('Copy', 'zionbuilder'),
				},
				{
					keyshortcut: [controlKey, 'V'],
					description: __('Paste', 'zionbuilder'),
				},
				{
					keyshortcut: [controlKey, 'X'],
					description: __('Cut', 'zionbuilder'),
				},
				{
					keyshortcut: [controlKey, 'D'],
					description: __('Duplicate', 'zionbuilder'),
				},
				{
					keyshortcut: [controlKey, '⇧', 'C'],
					description: __('Copy styles', 'zionbuilder'),
				},
				{
					keyshortcut: [controlKey, '⇧', 'V'],
					description: __('Paste styles', 'zionbuilder'),
				},
				{
					keyshortcut: [controlKey, 'Z'],
					description: __('Undo', 'zionbuilder'),
				},
				{
					keyshortcut: [controlKey, 'Y'],
					description: __('Redo', 'zionbuilder'),
				},
				{
					keyshortcut: [controlKey, '⇧', 'Y'],
					description: __('Redo', 'zionbuilder'),
				},
				{
					keyshortcut: [controlKey, 'H'],
					description: __('Hide element', 'zionbuilder'),
				},
				{
					keyshortcut: [controlKey, 'P'],
					description: __('Toggle preview mode', 'zionbuilder'),
				},
				{
					keyshortcut: ['⇧', 'T'],
					description: __('Toggle Tree View Panel', 'zionbuilder'),
				},
				{
					keyshortcut: ['⇧', 'L'],
					description: __('Toggle Library Panel', 'zionbuilder'),
				},
				{
					keyshortcut: ['⇧', 'O'],
					description: __('Toggle page options', 'zionbuilder'),
				},
				{
					keyshortcut: ['DRAG', controlKey],
					description: __('Duplicate element in place', 'zionbuilder'),
					details: __('When dragging element', 'zionbuilder'),
				},
				{
					keyshortcut: [controlKey, 'DRAG'],
					description: __('Set even values', 'zionbuilder'),
					details: __('When dragging toolbox', 'zionbuilder'),
				},
				{
					keyshortcut: [controlKey, '⇧', 'DRAG'],
					description: __('Set even incremental value', 'zionbuilder'),
				},
				{
					keyshortcut: [controlKey, '⇧', 'D'],
					description: __('Back to WP dashboard', 'zionbuilder'),
				},
				{
					keyshortcut: ['⇧', 'DRAG'],
					description: __('Set incremental value', 'zionbuilder'),
					details: __('On input of type number', 'zionbuilder'),
				},
				{
					keyshortcut: ['⇧', 'ARROWS'],
					description: __('Set incremental value', 'zionbuilder'),
				},
				{
					keyshortcut: ['ALT'],
					description: 'Toggle Link',
					details: __('On input of type number with link option available', 'zionbuilder'),
				},
			],
		};
	},
};
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
