<template>
	<div class="znpb-admin-gradient-preset-box" @mouseover="showLink = true" @mouseleave="showLink = false">
		<Icon
			v-znpb-tooltip="i18n.__('Delete this gradient from your preset', 'zionbuilder')"
			icon="close"
			class="znpb-admin-gradient-preset-box__delete"
			@click.stop="$emit('delete-gradient')"
		/>

		<div class="znpb-admin-gradient-preset-box__gradient">
			<GradientPreview :config="config" :round="true" />
		</div>
	</div>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { ref } from 'vue';

withDefaults(
	defineProps<{
		config: {
			type: string;
			colors: {
				color: string;
				position: number;
			}[];
		};
	}>(),
	{
		config: () => {
			return {
				type: 'linear',
				colors: [
					{
						color: '#000000',
						position: 0,
					},
					{
						color: '#ffffff',
						position: 100,
					},
				],
			};
		},
	},
);

defineEmits(['delete-gradient']);

const showLink = ref(false);
</script>

<style lang="scss">
.znpb-admin-gradient-preset-box {
	position: relative;
	display: flex;
	justify-content: center;
	align-items: center;
	box-shadow: 0 5px 10px 0 var(--zb-surface-shadow);
	border: 1px solid var(--zb-surface-border-color);
	border-radius: 3px;
	cursor: pointer;

	&::before {
		content: '';
		display: block;
		padding-top: 100%;
	}

	&__gradient {
		position: relative;
	}

	&__delete {
		position: absolute;
		top: 10px;
		right: 10px;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 20px;
		height: 20px;
		font-size: 8px;
		border: 1px solid var(--zb-surface-border-color);
		border-radius: 50%;
		transition: all 0.15s;
		cursor: pointer;
		opacity: 0;
		visibility: hidden;
	}

	&:hover &__delete {
		opacity: 1;
		visibility: visible;
	}

	.znpb-gradient-preview {
		box-shadow: 0 0 0 2px inset rgba(0, 0, 0, 0.1);
	}
}
</style>
