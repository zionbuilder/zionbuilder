<template>
	<transition
		appear
		name="bounce-icon"
	>
		<div
			class="znpb-editor-toolbox__top-bar-wrapper"
			:class="{['znpb-editor-toolbox__top-bar-wrapper--open']: topBarOpen}"
		>
			<div
				class="znpb-editor-toolbox__top-bar"
				:class="{['znpb-editor-toolbox__top-bar--open']: topBarOpen, ['znpb-editor-toolbox__top-bar--reverse']: reverseAnimation}"
				v-if="topBarOpen"
			>
				<Tooltip
					v-for="action in actions"
					:key="action.title"
				>
					<template #content>
						<div class="znpb-popper--tooltip">
							{{action.title}}
						</div>
					</template>

					<Icon
						:icon="action.icon"
						@click.stop="action.action"
					/>
				</Tooltip>
			</div>

			<Icon
				:icon="closeIcon"
				@click="toggleOpen"
				class="znpb-editor-toolbox__element-options-button"
				v-znpb-tooltip="toolboxOpenText"
			/>
		</div>
	</transition>

</template>

<script>
import { ref, computed } from 'vue'
// Utils
import { translate } from '@zb/i18n'
// Composables
import { useEditElement, useSaveTemplate } from '@zb/editor'

export default {
	name: 'TopBarToolbox',
	props: {
		element: Object
	},
	setup (props, { emit }) {
		const { showSaveElement } = useSaveTemplate()
		const topBarOpen = ref(false)
		const reverseAnimation = ref(false)
		const closeIcon = computed(() => topBarOpen.value ? 'close' : 'edit')
		const { editElement } = useEditElement()

		const toolboxOpenText = computed(() => {
			if (topBarOpen.value) {
				return `${translate('close')} ${props.element.elementTypeModel.name} ${translate('toolbox')}`
			} else {
				return `${translate('open')} ${props.element.elementTypeModel.name} ${translate('toolbox')}`
			}
		})

		function toggleOpen () {
			topBarOpen.value = !topBarOpen.value

			emit('set-top-bar-display', topBarOpen.value)

			if (!topBarOpen.value) {
				reverseAnimation.value = true
				setTimeout(() => {
					reverseAnimation.value = false
				}, 200)
			}
		}

		const actions = [
			{
				title: translate('edit_element'),
				action: () => editElement(props.element),
				icon: 'edit'
			},
			{
				title: translate('save_element'),
				action: () => showSaveElement(props.element, 'block'),
				icon: 'export'
			},
			{
				title: translate('visible_element'),
				action: () => { props.element.isVisible = false },
				icon: 'eye'
			},
			{
				title: translate('duplicate_element'),
				action: () => props.element.duplicate(),
				icon: 'copy'
			},
			{
				title: translate('delete_element'),
				action: () => props.element.delete(),
				icon: 'delete'
			}
		]

		return {
			toggleOpen,
			actions,
			topBarOpen,
			reverseAnimation,
			closeIcon,
			toolboxOpenText
		}
	}
}
</script>

<style lang="scss">
.znpb-editor-toolbox__element-options-button {
	border-radius: 16px;
	&:before {
		content: "";
		position: absolute;
		top: 0;
		left: 0;
		z-index: -1;
		width: 100%;
		height: 100%;
		box-shadow: 0 11px 20px 0 rgba(0, 0, 0, .1);
		border-radius: 50%;
		transition: all .2s;
	}
	&:hover:before {
		transform: scale(1.1);
	}
}
.znpb-editor-toolbox__top-bar-wrapper {
	position: absolute;
	top: 0;
	right: 0;
	z-index: 9999;
	display: flex;
	flex-wrap: nowrap;
	justify-content: space-around;
	align-items: center;
	color: #fff;
	font-size: 14px;
	line-height: 1 !important;
	border-radius: 16px;
	transform: translateY(-50%) translateX(17px);
	transition: all .2s ease;
	pointer-events: auto;
	.znpb-element__wrapper > .znpb-element-toolbox & {
		background-color: var(--zb-element-color);
		.znpb-editor-toolbox__element-options-button {
			&:hover:before {
				background-color: var(--zb-element-color);
			}
		}
	}
	.zb-column > .znpb-element-toolbox & {
		background-color: var(--zb-column-color);
		.znpb-editor-toolbox__element-options-button {
			&:hover:before {
				background-color: var(--zb-column-color);
			}
		}
	}

	.zb-section > .znpb-element-toolbox & {
		background-color: var(--zb-section-color);

		.znpb-editor-toolbox__element-options-button {
			&:hover:before {
				background-color: var(--zb-section-color);
			}
		}
	}

	&--open {
		padding: 0 6px;
		box-shadow: 0 11px 20px 0 rgba(0, 0, 0, .1);
		.znpb-editor-toolbox__element-options-button {
			&:before {
				box-shadow: none;
			}
		}

		.znpb-editor-toolbox__element-options-button {
			&:before {
				transform: scale(1);
			}
		}
		.znpb-editor-toolbox__top-bar {
			display: flex;
			animation: topBarAnimation .2s ease-in-out;
			animation-fill-mode: forwards;
		}
	}

	.znpb-editor-icon-wrapper {
		position: relative;
		width: 32px;
		height: 32px;
		color: #fff;
		cursor: pointer;
	}
}

.znpb-editor-toolbox__top-bar {
	display: none;
	overflow: hidden;
	width: 160px;
	border-bottom-left-radius: 16px;
	border-top-left-radius: 16px;
	animation: none;
	transition: all 2s ease-in-out;
	//TO DO check this code
	&--reverse {
		display: flex !important;
		animation: topBarAnimationReverse .2s ease-in-out;
		animation-fill-mode: forwards;
		.znpb-editor-icon-wrapper {
			transform: scale(0);
			transition: all .2s ease-in-out;
			opacity: 0;
		}
	}
}
@keyframes topBarAnimation {
	0% {
		width: 0;
	}
	100% {
		width: 160px;
	}
}
@keyframes topBarAnimationReverse {
	0% {
		width: 160px;
	}
	100% {
		width: 0;
	}
}
.bounce-icon-enter-from {
	transform: translate(17px, -50%) scale(.9);
}
.bounce-icon-enter-to {
	transform: translate(17px, -50%) scale(1);
}
.bounce-icon-leave-from {
	transform: translate(17px, -50%) scale(.2);
}
.bounce-icon-leave-to {
	transform: scale(0);
}
.bounce-icon-enter-to, .bounce-icon-leave-from {
	transition: all .2s;
}

.znpb-preview-page-wrapper > .znpb-element__wrapper > .znpb-element-toolbox > .znpb-editor-toolbox__top-bar-wrapper {
	top: 30px;
	right: 34px;
}
</style>
