<template>
	<div class="znpb-cornerLoaderWrapper">
		<transition name="save">
			<div v-if="isLoading" class="znpb-admin__options-save-loader">
				<Icon icon="check" />
			</div>
		</transition>
	</div>
</template>

<script lang="ts" setup>
defineProps<{
	isLoading: boolean;
}>();
</script>

<style lang="scss">
.znpb-cornerLoaderWrapper {
	position: fixed;
	right: 50px;
	bottom: 50px;
	display: flex;
	flex-direction: column;
	align-items: flex-end;
	min-width: 200px;
	z-index: 999;
}

.znpb-admin__options-save-loader {
	position: relative;
	display: flex;
	justify-content: center;
	align-items: center;
	width: 45px;
	height: 45px;
	border-radius: 50%;

	.znpb-editor-icon-wrapper {
		color: white;
		font-size: 18px;
		opacity: 0;
	}
	&:before,
	&:after {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		border-radius: 50%;
	}

	&:before {
		border: 2px solid;
		border-color: var(--zb-surface-lighter-color);
		border-right-color: var(--zb-success-color);
		transform: translateZ(0);
		transform: scale(1);
		animation: Rotate 0.6s infinite;
	}
	&.save-leave-to {
		width: 45px;
		height: 45px;
		text-align: center;
		background-color: var(--zb-success-color);
		transform: scale();
		animation: Bounce 0.6s;
	}

	&.save-leave-to:before {
		border: none;
	}

	&.save-enter-from,
	&.save-enter-to,
	&.save-leave {
		.znpb-editor-icon-wrapper {
			opacity: 0;
		}
	}

	&.save-leave-to {
		.znpb-editor-icon-wrapper {
			opacity: 1;
		}
	}
	.save-leave-to,
	.save-leave-from {
		opacity: 0;
	}

	.save-enter-to,
	.save-leave-to,
	.save-leave-from {
		transition: all 0.8s;
	}
}

@keyframes Bounce {
	0% {
		transform: scale(1);
	}
	50% {
		transform: scale(1.3);
	}
	100% {
		transform: scale(1);
	}
}
</style>
