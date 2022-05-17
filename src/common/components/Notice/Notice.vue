<template>
	<transition appear name="move">
		<div class="znpb-notices-wrapper">
			<div class="znpb-notice" :class="`znpb-notice--${error.type || 'success'}`">
				<Icon class="znpb-notice__close" icon="close" :size="12" @click="$emit('close-notice')" />
				<div v-if="error.title" class="znpb-notice__title">{{ error.title }}</div>
				<div class="znpb-notice__message">{{ error.message }}</div>
			</div>
		</div>
	</transition>
</template>

<script lang="ts">
export default {
	name: 'Notice',
};
</script>

<script lang="ts" setup>
import { Icon } from '../Icon';
import { onMounted, onBeforeUnmount } from 'vue';

const props = defineProps<{
	error: {
		title: string;
		message: string;
		type?: 'success' | 'error' | 'warning' | 'info';
		delayClose?: number;
	};
}>();

const emit = defineEmits(['close-notice']);

function hideOnEscape(event: KeyboardEvent) {
	if (event.key === 'Escape') {
		emit('close-notice');
		event.preventDefault();
		document.removeEventListener('keydown', hideOnEscape);
	}
}

onMounted(() => {
	const delay = props.error.delayClose ?? 5000;

	if (delay !== 0) {
		setTimeout(() => {
			emit('close-notice');
		}, delay);
	}

	document.addEventListener('keydown', hideOnEscape);
});

onBeforeUnmount(() => {
	document.removeEventListener('keydown', hideOnEscape);
});
</script>

<style lang="scss">
.znpb-notices-wrapper {
	position: absolute;
	right: 30px;
	bottom: 20px;
	z-index: 1000;
	width: 100%;
	max-width: 320px;
	transform: translateX(0);
}
.znpb-notice {
	padding: 16px 35px 16px 20px;
	margin-bottom: 10px;
	color: #fff;
	line-height: 1.8;
	background: #1bb934;
	border-radius: 3px;

	&__title {
		display: inline-block;
		margin-bottom: 10px;
		color: #fff;
		font-weight: 500;
		border-bottom: 1px solid rgba(255, 255, 255, 0.4);
	}

	&__close {
		position: absolute;
		top: 10px;
		right: 10px;
		color: #fff;
		font-size: 12px;
		cursor: pointer;
	}

	&--warning {
		background: #eec643;
	}

	&--info {
		background: #2ea1f8;
	}

	&--error {
		background: #e84655;
	}
}

.move-enter-to {
	transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}
.move-leave-from {
	transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}
.move-enter-from {
	transform: translateX(20px);
	opacity: 0;
}
.move-leave-to {
	transform: translateX(20px);
	opacity: 0;
}
</style>
