<template>
	<Tooltip
		class="zion-inline-editor-popover-wrapper"
		tooltip-class="zion-inline-editor-dropdown hg-popper--no-padding"
		trigger="click"
		placement="top"
		append-to="element"
		:close-on-outside-click="true"
		:popper-ref="popperRef"
		:modifiers="modifiers"
		:show-arrows="false"
	>
		<template #content>
			<slot></slot>
		</template>

		<Icon ref="iconElementRef" :icon="icon" :class="buttonClasses" />
	</Tooltip>
</template>

<script>
import { computed, ref, onMounted } from 'vue';

export default {
	name: 'PopOver',
	props: {
		icon: {
			type: String,
			required: false,
		},
		isActive: {
			type: Boolean,
			required: false,
		},
		fullSize: {
			required: false,
		},
	},
	setup(props) {
		const iconElementRef = ref(null);
		const popperRef = ref({
			x: 0,
			y: 0,
			left: 0,
		});

		const buttonClasses = computed(() => {
			const classes = [];

			// Check if the button has an icon
			if (typeof props.icon !== 'undefined') {
				classes.push('zn_pb_icon');
				classes.push(props.icon);
			}

			if (props.isActive) {
				classes.push('zion-inline-editor-button--active');
			}

			return classes.join(' ');
		});

		const modifiers = [
			{
				name: 'flip',
				options: {
					fallbackPlacements: ['top', 'bottom'],
				},
			},
		];
		if (props.fullSize) {
			modifiers.push({
				name: 'test',
				enabled: true,
				phase: 'beforeWrite',
				requires: ['computeStyles'],
				fn({ state, instance }) {
					const popperSize = state.rects.popper.width;
					const referenceSize = state.rects.reference.width;

					if (popperSize >= referenceSize) return;
					state.styles.popper.width = `${referenceSize}px`;

					instance.update();
				},
			});
		}

		onMounted(() => {
			const InlineEditor = window.document.getElementsByClassName('zion-inline-editor-container')[0];
			popperRef.value = props.fullSize ? InlineEditor : iconElementRef.value.$el;
		});

		return {
			buttonClasses,
			modifiers,
			popperRef,
			iconElementRef,
		};
	},
};
</script>

<style lang="scss">
.zion-inline-editor-popover-wrapper,
.zion-inline-editor-dropdown {
	display: flex;

	& > .znpb-editor-icon-wrapper {
		padding: 10px 11px;
	}

	.zion-inline-editor-link-wrapper {
		width: 100%;
	}
}
</style>
