<template>
	<span class="znpb-label" :class="{ [`znpb-label--${type}`]: type }">
		{{ text }}
	</span>
</template>

<script lang="ts" setup>
defineProps<{
	text: string;
	type?: 'error' | 'warning' | 'pro';
}>();
</script>

<style lang="scss">
.znpb-label {
	display: inline-block;
	padding: 5px 8px 5px;
	margin: 0 20px;
	color: #fff;
	font-size: 9px;
	font-weight: 700;
	letter-spacing: 0.5px;
	background: var(--zb-primary-color);
	border-radius: 2px;

	&--error {
		background: var(--zb-error-color);
	}

	&--warning {
		color: var(--zb-surface-color);
		background: var(--zb-warning-color);
	}
	&--pro {
		margin-right: 0;
		margin-left: auto;
		color: var(--zb-surface-color);
		text-transform: uppercase;
		background: var(--zb-warning-color);
	}
}
</style>
