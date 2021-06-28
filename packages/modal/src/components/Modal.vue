<template>
	<transition name="modal-fade">
		<div
			v-if="show"
			class="znpb-modal__backdrop"
			:class="{'znpb-modal__backdrop--nobg' : !bg }"
			@click="closeOnBackdropClick"
			:style="modalStyle"
		>
			<div
				:style="modalContentStyle"
				class="znpb-modal__wrapper "
				:class="{'znpb-modal__wrapper--full-size': fullSize}"
				ref="modalContent"
			>
				<header
					class="znpb-modal__header"
					v-if="( title || showClose || showMaximize ) && ! hasHeaderSlot"
				>
					<div
						class="znpb-modal__header-title"
						@mousedown="activateDrag"
						:style="enableDrag ? {
							'cursor': 'pointer',
							'user-select': 'none'
						} : null"
					>
						{{title}}
						<slot name="title"></slot>
					</div>

					<Icon
						:icon="fullSize ? 'shrink' : 'maximize'"
						class="znpb-modal__header-button"
						v-if="showMaximize"
						@click.stop="fullSize = ! fullSize, $emit('update:fullscreen', fullSize)"
					/>

					<span
						v-if="showClose"
						@click.stop="closeModal"
						class="znpb-modal__header-button"
					>
						<slot name="close"></slot>
						<Icon icon="close" />
					</span>

				</header>
				<slot name="header" />
				<div class="znpb-modal__content">
					<slot></slot>
				</div>
				<slot name="footer" />
			</div>
		</div>
	</transition>
</template>

<script>
import { addOverflow, removeOverflow } from '@zionbuilder/utils'
import { getZindex, removeZindex } from '@zionbuilder/z-index-manager'
import { Icon } from '../../../components'

export default {
	name: 'Modal',
	components: {
		Icon
	},
	props: {
		show: {
			type: Boolean,
			required: false,
			default: false
		},
		title: {
			type: String,
			required: false,
			default: ''
		},
		width: {
			type: Number,
			required: false
		},
		fullscreen: {
			type: Boolean,
			required: false,
			default: false
		},
		appendTo: {
			type: String,
			required: false
		},
		closeOnClick: {
			type: Boolean,
			required: false,
			default: true
		},
		closeOnEscape: {
			type: Boolean,
			required: false,
			default: true
		},
		showClose: {
			type: Boolean,
			required: false,
			default: true
		},
		showMaximize: {
			type: Boolean,
			required: false,
			default: true
		},
		showBackdrop: {
			type: Boolean,
			required: false,
			default: true
		},
		position: {
			type: Object,
			required: false,
			default: null
		},
		enableDrag: {
			type: Boolean,
			required: false,
			default: true
		}
	},
	watch: {
		show (newValue) {
			if (newValue) {
				this.zIndex = getZindex()

				// Wait for the HTML to be added
				this.$nextTick(() => {
					// add overflow to body
					if (this.$el.ownerDocument.getElementById('znpb-editor-iframe') !== undefined && this.$el.ownerDocument.getElementById('znpb-editor-iframe') !== null) {
						addOverflow(document.getElementById('znpb-editor-iframe').contentWindow.document.body)
					} else {
						addOverflow(this.$el.ownerDocument.body)
					}
				})

			} else {
				this.$nextTick(() => {
					removeZindex()
					// remove overflow from body
					if (document.getElementById('znpb-editor-iframe') !== undefined && document.getElementById('znpb-editor-iframe') !== null) {
						removeOverflow(document.getElementById('znpb-editor-iframe').contentWindow.document.body)
					} else {
						removeOverflow(this.$el.ownerDocument.body)
					}
				})


			}
		},
		fullscreen (newValue) {
			if (newValue) {
				this.fullSize = newValue
			} else this.fullSize = this.fullscreen
		},
		showBackdrop (newValue) {
			this.bg = newValue
		}

	},
	data: function () {
		return {
			fullSize: this.fullscreen,
			bg: this.showBackdrop,
			hasHeader: false,
			zIndex: 9999,
			initialPosition: {}
		}
	},
	computed: {
		modalStyle () {
			return {
				zIndex: this.zIndex,
				left: (this.position === null) || (this.position.left + 60 > window.innerWidth) || (this.topPos === null) ? null : '30px',
				top: (this.position === null) || (this.leftPos === null) || (this.topPos === null) ? null : '0',
				transform: (this.position === null) || (this.leftPos === null) || (this.topPos === null) ? null : `translate(${Math.round(this.leftPos)}px,${Math.round(this.topPos)}px)`
			}
		},
		leftPos () {
			return (this.position === null) || (this.position.left + 60 > window.innerWidth) ? null : this.position.left
		},
		topPos () {
			let top = 0

			if (this.position === null) {
				top = null
			} else if (this.position.top - 30 < 0) {
				top = 0
			} else if (this.position.top > window.innerHeight / 2) {
				top = this.position.top - 90
			} else top = this.position.top

			return top
		},
		hasHeaderSlot () {
			return !!this.$slots['header']
		},
		maximizeIcon () {
			return this.fullSize ? 'minimize' : 'maximize'
		},
		modalContentStyle () {
			let modalStyle = {}

			if (this.width) {
				modalStyle['max-width'] = this.width + 'px'
			}
			if (this.enableDrag) {
				modalStyle['position'] = 'absolute'
			}
			if (this.fullSize) {
				modalStyle['max-height'] = '100%'
			}

			return modalStyle
		},
		appendToElement () {
			return document.querySelector(this.appendTo)
		}
	},
	methods: {
		activateDrag () {
			if (this.enableDrag) {
				this.$refs.modalContent.style.transition = 'none'
				const { left, top } = this.$refs.modalContent.getBoundingClientRect()
				this.initialPosition = {
					clientX: event.clientX,
					clientY: event.clientY,
					left,
					top
				}
				window.addEventListener('mousemove', this.drag)
				window.addEventListener('mouseup', this.unDrag)
			}
		},
		drag (event) {
			const left = event.clientX - this.initialPosition.clientX + this.initialPosition.left
			const top = event.clientY - this.initialPosition.clientY + this.initialPosition.top
			const procentualLeft = left * 100 / window.innerWidth + '%'
			const procentualTop = top * 100 / window.innerHeight + '%'
			this.$refs.modalContent.style.left = procentualLeft
			this.$refs.modalContent.style.top = procentualTop
		},
		unDrag () {
			if (this.$refs.modalContent) {
				this.$refs.modalContent.style.transition = 'all .2s'
			}
			window.removeEventListener('mousemove', this.drag)
		},
		closeOnBackdropClick (event) {
			if (this.closeOnClick) {
				if (this.$refs.modalContent && !this.$refs.modalContent.contains(event.target)) {

					this.closeModal()
				}
			}
		},
		closeModal () {
			this.$emit('update:show', false)
			this.$emit('close-modal', true)
		},
		appendModal () {
			if (!this.appendToElement) {
				// eslint-disable-next-line
				console.warn(`${this.$translate('no_html_matching')} ${this.appendTo}`)
				return
			}
			this.appendToElement.appendChild(this.$el)
		},
		onEscapeKeyPress (event) {
			if (event.key === 'Escape') {
				this.closeModal()
			}
		}
	},
	mounted () {
		// Check if we need to move modal to body
		if (this.appendTo) {
			this.appendModal()
		}

		// Check if we need to close modal on esc key
		if (this.closeOnEscape) {
			document.addEventListener('keydown', this.onEscapeKeyPress)
		}
	},

	beforeUnmount () {
		window.removeEventListener('mousemove', this.drag)
		window.removeEventListener('mouseup', this.unDrag)
		window.removeEventListener('keydown', this.onEscapeKeyPress)
		if (this.$el.parentNode === this.appendToElement) {
			this.appendToElement.removeChild(this.$el)
		}

		removeZindex()
	}
}
</script>

<style lang="scss">
.znpb-modal {
	color: #000;

	&__backdrop {
		position: fixed;
		top: 0;
		left: 0;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 100%;
		height: 100%;
		background: rgba(172, 172, 172, 0.2);
		* {
			box-sizing: border-box;
		}
	}

	&__wrapper {
		display: flex;
		flex-direction: column;
		width: calc(100% - 40px);
		max-width: 100%;
		max-height: 80%;
		background: var(--zb-surface-color);
		box-shadow: 0 0 25px -10px rgba(0, 0, 0, 0.1);
		border-radius: 3px;
		transition: all 0.2s;

		&--full-size {
			width: 100%;
			height: 100%;
		}
	}

	&__header {
		display: flex;
		justify-content: space-between;
		flex-shrink: 0;
		box-shadow: 0 1px 0 0 var(--zb-surface-border-color);

		&-title {
			flex-grow: 2;
			padding: 21px 20px;
			color: var(--zb-surface-text-active-color);
			font-family: var(--zb-font-stack);
			font-size: 16px;
			font-weight: 500;
			line-height: 1;
		}

		&-button {
			align-self: center;
			margin-right: 15px;
			color: var(--zb-surface-icon-color);
			font-size: 14px;
			transition: color 0.15s;
			cursor: pointer;

			&:hover {
				color: var(--zb-surface-text-hover-color);
			}

			&:last-child {
				margin-right: 20px;
			}
		}
	}

	&__content {
		position: relative;
		display: flex;
		flex-direction: column;
		flex-grow: 2;
		overflow: hidden;
	}
}
.modal-fade-leave-from,
.modal-fade-enter-to {
	transition: all 0.2s;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
	transform: scale(0.99);
	opacity: 0;
}
</style>
