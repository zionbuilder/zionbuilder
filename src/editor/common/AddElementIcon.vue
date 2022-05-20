<template>
	<transition appear name="bounce-add-icon">
		<div
			ref="addElementsPopupButton"
			v-znpb-tooltip="positionString + ' ' + element.name"
			class="znpb-element-toolbox__add-element-button"
			:class="{
				[`znpb-element-toolbox__add-element-button--${position}`]: position,
				[`znpb-element-toolbox__add-element-button--${placement}`]: placement,
				['znpb-element-toolbox__add-element-button--active']: isPopupActive,
			}"
			@click.stop="onIconClick"
		>
			<Icon icon="plus" :rounded="true" />
		</div>
	</transition>
</template>

<script>
import { ref, computed, watch } from 'vue';
import { useAddElementsPopup } from '../composables';
import { translate } from '@common/modules/i18n';

export default {
	name: 'AddElementIcon',
	props: {
		element: {
			type: Object,
		},
		placement: {
			type: String,
			default: 'next',
		},
		position: {
			type: String,
		},
	},
	setup(props) {
		const addElementsPopupButton = ref(false);
		const isIconClicked = ref(false);

		const { activePopup } = useAddElementsPopup();

		const positionString = props.placement === 'inside' ? translate('insert_inside') : translate('insert_after');

		const isPopupActive = computed(() => {
			return isIconClicked.value && activePopup.value && activePopup.value.element === props.element;
		});

		watch(activePopup, (newValue, oldValue) => {
			if (!newValue || (newValue && newValue.element !== props.element)) {
				isIconClicked.value = false;
			}
		});

		function toggleAddElementsPopup() {
			const { showAddElementsPopup } = useAddElementsPopup();
			showAddElementsPopup(props.element, addElementsPopupButton, {
				placement: props.placement,
			});
		}

		function onIconClick() {
			isIconClicked.value = true;
			toggleAddElementsPopup();
		}

		return {
			// Refs
			addElementsPopupButton,

			// Vars
			positionString,

			// Computed
			isPopupActive,

			// Methods
			onIconClick,
		};
	},
};
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
		color: #fff;
	}

	svg {
		position: relative;
	}
}

.bounce-add-icon-enter-from {
	transform: scale(0.9);
}
.bounce-add-icon-enter-to {
	transform: scale(1);
}
.bounce-add-icon-leave-from {
	transform: scale(0.5);
}
.bounce-add-icon-leave-to {
	transform: scale(0);
}
.bounce-add-icon-enter-to,
.bounce-add-icon-leave-from {
	transition: scale 0.2s;
}
</style>
