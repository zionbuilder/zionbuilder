<template>
	<div class="znpb-empty-placeholder">
		<AddElementIcon :element="element" placement="inside" position="middle" />
	</div>
</template>

<script lang="ts" setup>
import AddElementIcon from './AddElementIcon.vue';

defineProps<{
	element: ZionElement;
}>();
</script>

<style lang="scss">
.znpb-empty-placeholder {
	position: relative;
	display: flex;
	justify-content: center;
	align-items: center;
	width: 100%;
	height: 100%;
	min-height: 80px;

	&__add-element-button {
		position: relative;
		width: 28px;
		height: 28px;
		color: #fff;
		font-size: 14px;
		line-height: 1 !important;
		border-radius: 50%;

		&::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: var(--zb-column-color);
			box-shadow: 0 11px 20px 0 rgba(0, 0, 0, 0.1);
			border-radius: 50%;
			transition: all 0.2s;
		}

		.znpb-element__wrapper &::before {
			background-color: var(--zb-element-color);
		}
		.zb-column &::before {
			background-color: var(--zb-column-color);
		}

		&:hover {
			&::before {
				transform: scale(1.1);
			}
		}
		.znpb-editor-icon-wrapper {
			position: relative;
			width: 28px;
			height: 28px;
			cursor: pointer;
		}
	}
}
</style>
