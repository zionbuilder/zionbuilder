<template>
	<div class="znpb-loader-wrapper">
		<div
			class="znpb-loader"
			:style="{
				height: `${size}px`,
				width: `${size}px`,
			}"
		></div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'Loader',
};
</script>

<script lang="ts" setup>
withDefaults(
	defineProps<{
		size?: number;
	}>(),
	{
		size: 24,
	},
);
</script>

<style lang="scss">
.znpb-loader-wrapper {
	display: flex;
	justify-content: center;
	align-items: center;
	width: 100%;
}
.znpb-loader {
	position: relative;
	left: -2px;

	&:before,
	&:after {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		box-sizing: border-box;
		width: 100%;
		height: 100%;
		border: 2px solid transparent;
		border-radius: 50%;
	}

	&:after {
		border-right-color: var(--zb-surface-loader-color);
		border-bottom-color: var(--zb-surface-loader-color);
		animation: Rotate 0.6s linear infinite;
	}
}
</style>
