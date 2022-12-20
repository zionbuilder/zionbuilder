<template>
	<div class="znpb-admin-gradient-preset-box__empty">
		<Icon icon="plus" />
		<div>{{ __('Add Gradient', 'zionbuilder') }}</div>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
</script>

<style lang="scss">
.znpb-admin-gradient-preset-box__empty {
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	padding: 28px 0;
	color: var(--zb-surface-text-color);
	font-weight: 500;
	border: 2px dashed var(--zb-surface-border-color);
	border-radius: 3px;
	cursor: pointer;
	.znpb-editor-icon-wrapper {
		width: 16px;
		height: 16px;
		margin-bottom: 5px;
	}
}
</style>
