<template>
	<transition
		appear
		name="bounce-add-icon"
	>
		<div
			class="znpb-element-toolbox__add-element-button"
			:class="{
				[`znpb-element-toolbox__add-element-button--${position}`]: position,
				[`znpb-element-toolbox__add-element-button--${placement}`]: position,
			}"
			@click.stop="toggleAddElementsPopup"
			ref="addElementsPopupButton"
		>
			<Icon
				v-znpb-tooltip="positionString + ' ' + element.name"
				icon="plus"
				:rounded="true"
			/>
		</div>

	</transition>
</template>

<script>
import { ref } from 'vue'
import { useAddElementsPopup } from '@composables'
import { translate } from '@zb/i18n'

export default {
	name: "AddElementIcon",
	props: {
		element: {
			type: Object
		},
		placement: {
			type: String,
			default: 'next'
		},
		position: {
			type: String
		}
	},
	setup (props) {
		const addElementsPopupButton = ref(false)

		const positionString = props.placement === 'inside' ? translate('insert_inside') : translate('insert_after')

		function toggleAddElementsPopup () {
			const { showAddElementsPopup } = useAddElementsPopup()
			showAddElementsPopup(props.element, addElementsPopupButton, {
				placement: props.placement
			})
		}

		return {
			// Refs
			addElementsPopupButton,

			// Vars
			positionString,

			// Methods
			toggleAddElementsPopup
		}
	}
}
</script>

<style lang="scss">
.znpb-element-toolbox__add-element-button {
	text-align: center;
	cursor: pointer;

	.znpb-editor-icon-wrapper {
		width: 28px;
		height: 28px;
		color: #fff;
		background: var(--zb-element-color);
	}
}

.znpb-element-toolbox__add-element-button--centered-bottom {
	position: absolute;
	top: 100%;
	left: 50%;
	margin: 0 auto;
	transform: translate(-50%, -50%);
}

.bounce-add-icon-enter-from {
	transform: translate(-50%, -50%) scale(.9);
}
.bounce-add-icon-enter-to {
	transform: translate(-50%, -50%) scale(1);
}
.bounce-add-icon-leave-from {
	transform: translate(-50%, -50%) scale(.5);
}
.bounce-add-icon-leave-to {
	transform: scale(0);
}
.bounce-add-icon-enter-to, .bounce-add-icon-leave-from {
	transition: scale .2s;
}
</style>