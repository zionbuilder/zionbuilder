<template>
	<div
		:class="{'znpb-utility__text--elipse': !active}"
		:contenteditable="active"
		@dblclick.stop="activate"
		@blur="deactivate"
	>
		{{modelValue}}
	</div>
</template>

<script>
export default {
	name: 'InlineEdit',
	props: {
		modelValue: {
			type: String,
			required: true
		},
		active: {
			type: Boolean,
			required: false,
			default: null
		}
	},
	watch: {
		active (newVal) {
			if (newVal) {
				this.activate()
			}
		}
	},
	methods: {
		/**
		 * Activates the name change input
		 */
		activate (event) {
			this.$emit('update:active', true)
			this.$nextTick(() => this.$el.focus())
		},
		deactivate (event) {
			// only rename if content is editable
			if (!this.active) {
				return
			}

			let el = event.target
			let range = document.createRange()
			let sel = window.getSelection()
			range.setStart(el, 0)
			range.collapse(true)
			sel.removeAllRanges()
			sel.addRange(range)

			this.$emit('update:active', false)
			this.$emit('update:modelValue', event.target.innerText)
		}
	}
}
</script>
<style lang="scss">
.znpb-tree-view__item--hidden {
	.znpb-editor-icon-wrapper--show-element::before {
		content: "";
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		width: 44px;
		height: 100%;
		background: linear-gradient(
		-90deg,
		$surface-variant 0%,
		rgba($surface-variant, .3) 100%
		);
	}
}

.znpb-panel-item--hovered .znpb-editor-icon-wrapper--show-element::before {
	background: linear-gradient(
	-90deg,
	darken($surface-variant, 3%) 0%,
	rgba(darken($surface-variant, 3%), .3) 100%
	);
}

.znpb-panel-item--active .znpb-editor-icon-wrapper--show-element::before {
	background: linear-gradient(
	-90deg,
	$secondary 0%,
	rgba($secondary, .3) 100%
	);
}

.znpb-tree-view__item-enable-visible {
	position: relative;
}
</style>
