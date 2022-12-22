<template>
	<div class="znpb-key-shortcuts-modal znpb-fancy-scrollbar">
		<div class="znpb-key-shortcuts-modal__content">
			<keyShortcutsItem
				v-for="(schema, i) in schemaDescriptionFirst"
				:key="i + schema.description"
				:shortcut-key="schema.shortcutKey"
				:description="schema.description"
			>
				<pre v-if="schema.details" class="znpb-key-shortcuts-modal__item-details">{{ schema.details }}</pre>
			</keyShortcutsItem>
		</div>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import keyShortcutsItem from './keyShortcutsItem.vue';

const { Environment } = window.zb.utils;

const controlKey = Environment.isMac ? '⌘' : '⌃';
const schemaDescriptionFirst = [
	{
		shortcutKey: [controlKey, 'S'],
		description: __('Save changes', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'C'],
		description: __('Copy', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'V'],
		description: __('Paste', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'X'],
		description: __('Cut', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'D'],
		description: __('Duplicate', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, '⇧', 'C'],
		description: __('Copy styles', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, '⇧', 'V'],
		description: __('Paste styles', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'Z'],
		description: __('Undo', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'Y'],
		description: __('Redo', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, '⇧', 'Y'],
		description: __('Redo', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'H'],
		description: __('Hide element', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'P'],
		description: __('Toggle preview mode', 'zionbuilder'),
	},
	{
		shortcutKey: ['⇧', 'T'],
		description: __('Toggle Tree View Panel', 'zionbuilder'),
	},
	{
		shortcutKey: ['⇧', 'L'],
		description: __('Toggle Library Panel', 'zionbuilder'),
	},
	{
		shortcutKey: ['⇧', 'O'],
		description: __('Toggle page options', 'zionbuilder'),
	},
	{
		shortcutKey: ['DRAG', controlKey],
		description: __('Duplicate element in place', 'zionbuilder'),
		details: __('When dragging element', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'DRAG'],
		description: __('Set even values', 'zionbuilder'),
		details: __('When dragging toolbox', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, '⇧', 'DRAG'],
		description: __('Set even incremental value', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, '⇧', 'D'],
		description: __('Back to WP dashboard', 'zionbuilder'),
	},
	{
		shortcutKey: ['⇧', 'DRAG'],
		description: __('Set incremental value', 'zionbuilder'),
		details: __('On input of type number', 'zionbuilder'),
	},
	{
		shortcutKey: ['⇧', 'ARROWS'],
		description: __('Set incremental value', 'zionbuilder'),
	},
	{
		shortcutKey: ['ALT'],
		description: 'Toggle Link',
		details: __('On input of type number with link option available', 'zionbuilder'),
	},
];
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
