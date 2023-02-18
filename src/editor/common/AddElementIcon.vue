<template>
	<div
		v-if="!userStore.permissions.only_content"
		ref="root"
		v-znpb-tooltip="positionString + ' ' + element.name"
		class="znpb-element-toolbox__add-element-button"
		:class="{
			[`znpb-element-toolbox__add-element-button--${position}`]: position,
			[`znpb-element-toolbox__add-element-button--${placement}`]: placement,
		}"
	>
		<Icon icon="plus" :rounded="true" />
	</div>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { useUIStore, useUserStore } from '../store';
import { ref, onBeforeUnmount, onMounted } from 'vue';

const props = withDefaults(
	defineProps<{
		element: ZionElement;
		placement?: 'inside' | 'next';
		position?: string | null;
		index?: number;
	}>(),
	{
		placement: 'next',
		position: null,
		index: -1,
	},
);

const root = ref(null);
const UIStore = useUIStore();
const userStore = useUserStore();
const positionString =
	props.placement === 'inside' ? i18n.__('Insert inside', 'zionbuilder') : i18n.__('Insert after', 'zionbuilder');

onMounted(() => {
	if (root.value) {
		root.value.addEventListener('click', onIconClick, true);
	}
});

onBeforeUnmount(() => {
	if (root.value) {
		root.value.removeEventListener('click', onIconClick, true);
	}
});

function onIconClick(event: MouseEvent) {
	event.stopPropagation();
	UIStore.showAddElementsPopup(props.element, event, props.placement);
}

// Prevent the element from exiting the browser window
function preventElementExit() {
	const element = root.value;
	if (!element) {
		return;
	}

	const elementRect = element.getBoundingClientRect();
	const windowWidth = window.innerWidth;
	const windowHeight = window.innerHeight;

	if (elementRect.left < 0) {
		element.style.left = '0px';
	}

	if (elementRect.top < 0) {
		element.style.top = '0px';
	}

	if (elementRect.right > windowWidth) {
		element.style.left = `${windowWidth - elementRect.width}px`;
	}

	if (elementRect.bottom > windowHeight) {
		element.style.marginTop = `-30px`;
	}
}

onMounted(() => {
	preventElementExit();
});
</script>

<style lang="scss">
.znpb-element-toolbox__add-element-button {
	--button-size: 28px;
	--font-size: 14px;
	display: flex;
	align-items: center;
	justify-content: center;
	position: absolute;
	top: 100%;
	left: 50%;
	width: var(--button-size);
	height: var(--button-size);
	cursor: pointer;
	z-index: 1001;
	pointer-events: auto;
	padding: 0;
	margin-top: calc((var(--button-size) / 2) * -1);
	margin-left: calc((var(--button-size) / 2) * -1);

	&::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: var(--zb-secondary-color);
		border-radius: 50%;
		box-shadow: 0 5px 20px 0 rgba(0, 0, 0, 0.1);
		transition: all 0.1s;
	}

	&:hover::before {
		transform: scale(1.1);
	}

	&--inside {
		top: 50%;

		&::before {
			background-color: rgb(82 82 82 / 40%);
		}
	}

	.znpb-editor-icon-wrapper {
		font-size: var(--font-size);
		color: #fff !important;
	}

	svg {
		position: relative;
		pointer-events: none;
	}
}
</style>
