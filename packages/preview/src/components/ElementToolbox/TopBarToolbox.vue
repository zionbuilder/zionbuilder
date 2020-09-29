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
				<Tooltip :modifiers="{offset: { offset: '0,5px' }}">
					<div
						slot="content"
						class="znpb-popper--tooltip"
					>
						{{$translate('edit_element')}}
					</div>

					<BaseIcon
						icon="edit"
						@click.stop="openOptionsPanel"
					/>
				</Tooltip>
				<Tooltip :modifiers="{offset: { offset: '0,5px' }}">
					<div
						slot="content"
						class="znpb-popper--tooltip"
					>
						{{$translate('save_element')}}
					</div>
					<BaseIcon
						icon="export"
						@click.stop="emitEventbus"
					/>
				</Tooltip>
				<Tooltip :modifiers="{offset: { offset: '0,5px' }}">
					<div
						slot="content"
						class="znpb-popper--tooltip"
					>
						{{$translate('visible_element')}}
					</div>
					<BaseIcon
						icon="eye"
						@click.stop="setInvisible"
					/>
				</Tooltip>
				<Tooltip :modifiers="{offset: { offset: '0,5px' }}">
					<div
						slot="content"
						class="znpb-popper--tooltip"
					>
						{{$translate('clone_element')}}
					</div>
					<BaseIcon
						icon="copy"
						@click.stop="duplicateElement"
					/>
				</Tooltip>
				<Tooltip :modifiers="{offset: { offset: '0,5px' }}">
					<div
						slot="content"
						class="znpb-popper--tooltip"
					>
						{{$translate('delete_element')}}
					</div>
					<BaseIcon
						icon="delete"
						@click.stop="deleteParentElement"
					/>
				</Tooltip>
			</div>
			<Tooltip :modifiers="{offset: { offset: '0,5px' }}">
				<div
					slot="content"
					class="znpb-popper--tooltip"
				>
					<span v-if="topBarOpen">
						{{$translate('close')}} {{elementModel.name}} {{$translate('toolbox')}}
					</span>
					<span v-else>
						{{$translate('open')}} {{elementModel.name}} {{$translate('toolbox')}}
					</span>
				</div>
				<BaseIcon
					:icon="closeIcon"
					@click.stop="toggleOpen"
					class="znpb-editor-toolbox__element-options-button"
				/>
			</Tooltip>
		</div>
	</transition>

</template>

<script>
// Utils
import { mapActions, mapGetters } from 'vuex'

// Components
import { Tooltip } from '@/common/components/tooltip'

export default {
	name: 'TopBarToolbox',
	components: {
		Tooltip
	},
	props: {
		data: {
			type: Object,
			required: true
		},
		parentUid: {
			type: String,
			required: true
		}
	},
	data () {
		return {
			topBarOpen: false,
			reverseAnimation: false,
			ExportModal: false
		}
	},
	computed: {
		...mapGetters([
			'getElementById',
			'getElementData'
		]),
		closeIcon () {
			return this.topBarOpen ? 'close' : 'edit'
		},
		elementModel () {
			return this.getElementById(this.data.element_type)
		}

	},
	methods: {
		...mapActions([
			'setActiveElement',
			'copyElement',
			'deleteElement',
			'updateElementOptionValue',
			'setElementFocus',
			'setRightClickMenu',
			'openPanel'
		]),
		setInvisible () {
			this.updateElementOptionValue({
				elementUid: this.data.uid,
				path: '_isVisible',
				newValue: false,
				type: 'visibility'
			})
		},
		toggleOpen () {
			this.setElementFocus({
				uid: this.data.uid,
				parentUid: this.parentUid,
				insertParent: this.elementModel.wrapper ? this.data.uid : this.parentUid,
				scrollIntoView: false
			})

			this.topBarOpen = !this.topBarOpen
			this.$emit('set-top-bar-display', this.topBarOpen)
			if (!this.topBarOpen) {
				this.reverseAnimation = true
				setTimeout(() => {
					this.reverseAnimation = false
				}, 200)
			}
			this.setRightClickMenu({
				visibility: false
			})
		},
		openOptionsPanel () {
			this.setActiveElement(this.data.uid)
			this.openPanel('PanelElementOptions')
		},
		duplicateElement () {
			this.copyElement({
				elementUid: this.data.uid,
				insertParent: this.parentUid
			})
		},

		deleteParentElement () {
			this.deleteElement({
				elementUid: this.data.uid,
				parentUid: this.parentUid
			})
		},
		emitEventbus (event) {
			window.ZionBuilderApi.trigger('save-element', {
				elementUid: this.data.uid,
				parentUid: this.parentUid
			})
		}
	}
}
</script>

<style lang="scss">
.znpb-editor-toolbox__element-options-button {
	border-radius: 16px;
	&:before {
		@extend %iconbg;
		z-index: -1;
	}
	&:hover:before {
		transform: scale(1.1);
	}
}
.znpb-editor-toolbox__top-bar-wrapper {
	position: absolute;
	top: 0;
	right: 0;
	z-index: 1001;
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
		background-color: $elements-toolbox-color;
		.znpb-editor-toolbox__element-options-button {
			&:hover:before {
				background-color: $elements-toolbox-color;
			}
		}
	}
	.zb-column > .znpb-element-toolbox & {
		background-color: $column-color;
		.znpb-editor-toolbox__element-options-button {
			&:hover:before {
				background-color: $column-color;
			}
		}
	}

	.zb-section > .znpb-element-toolbox & {
		background-color: $section-color;

		.znpb-editor-toolbox__element-options-button {
			&:hover:before {
				background-color: $section-color;
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
.bounce-icon-enter {
	transform: translate(17px, -50%) scale(.9);
}
.bounce-icon-enter-to {
	transform: translate(17px, -50%) scale(1);
}
.bounce-icon-leave {
	transform: translate(17px, -50%) scale(.2);
}
.bounce-icon-leave-active {
	transform: scale(0);
}
.bounce-icon-leave-to {
	transform: scale(0);
}
.bounce-icon-enter-active, .bounce-icon-leave-active {
	transition: all .2s;
}

.znpb-preview-page-wrapper > .znpb-element__wrapper > .znpb-element-toolbox > .znpb-editor-toolbox__top-bar-wrapper {
	top: 30px;
	right: 34px;
}
</style>
