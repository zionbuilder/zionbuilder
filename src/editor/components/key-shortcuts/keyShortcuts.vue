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
import * as i18n from '@wordpress/i18n';
import keyShortcutsItem from './keyShortcutsItem.vue';

const { Environment } = window.zb.utils;

const controlKey = Environment.isMac ? '⌘' : '⌃';
const schemaDescriptionFirst = [
	{
		shortcutKey: [controlKey, 'S'],
		description: i18n.__('Save changes', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'C'],
		description: i18n.__('Copy', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'V'],
		description: i18n.__('Paste', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'X'],
		description: i18n.__('Cut', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'D'],
		description: i18n.__('Duplicate', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, '⇧', 'C'],
		description: i18n.__('Copy styles', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, '⇧', 'V'],
		description: i18n.__('Paste styles', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'Z'],
		description: i18n.__('Undo', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'Y'],
		description: i18n.__('Redo', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, '⇧', 'Y'],
		description: i18n.__('Redo', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'H'],
		description: i18n.__('Hide element', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'P'],
		description: i18n.__('Toggle preview mode', 'zionbuilder'),
	},
	{
		shortcutKey: ['⇧', 'T'],
		description: i18n.__('Toggle Tree View Panel', 'zionbuilder'),
	},
	{
		shortcutKey: ['⇧', 'L'],
		description: i18n.__('Toggle Library Panel', 'zionbuilder'),
	},
	{
		shortcutKey: ['⇧', 'O'],
		description: i18n.__('Toggle page options', 'zionbuilder'),
	},
	{
		shortcutKey: ['DRAG', controlKey],
		description: i18n.__('Duplicate element in place', 'zionbuilder'),
		details: i18n.__('When dragging element', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, 'DRAG'],
		description: i18n.__('Set even values', 'zionbuilder'),
		details: i18n.__('When dragging toolbox', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, '⇧', 'DRAG'],
		description: i18n.__('Set even incremental value', 'zionbuilder'),
	},
	{
		shortcutKey: [controlKey, '⇧', 'D'],
		description: i18n.__('Back to WP dashboard', 'zionbuilder'),
	},
	{
		shortcutKey: ['⇧', 'DRAG'],
		description: i18n.__('Set incremental value', 'zionbuilder'),
		details: i18n.__('On input of type number', 'zionbuilder'),
	},
	{
		shortcutKey: ['⇧', 'ARROWS'],
		description: i18n.__('Set incremental value', 'zionbuilder'),
	},
	{
		shortcutKey: ['ALT'],
		description: 'Toggle Link',
		details: i18n.__('On input of type number with link option available', 'zionbuilder'),
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
