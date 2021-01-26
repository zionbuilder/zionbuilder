<template>
	<component
		:is="tag"
		:class="{'znpb-utility__text--elipse': !active}"
		:contenteditable="isActive"
		@dblclick.stop="activate"
		@blur="deactivate"
		ref="root"
		v-html="computedModelValue"
	>

	</component>
</template>

<script>
import { ref, watch, nextTick, computed } from 'vue'
import { translate } from '@zb/i18n'
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
			default: false
		},
		tag: {
			type: String,
			required: false,
			default: 'div'
		}
	},
	setup (props, { emit }) {
		const isActive = ref(props.active || false)
		const root = ref(null)

		watch(() => props.active, (newValue) => {
			if (newValue !== isActive.value) {
				isActive.value = newValue
				activate()
			}
		})

		let computedModelValue = computed(() => {
			return props.modelValue && props.modelValue.length ? props.modelValue : translate('editable_name')
		})

		/**
		 * Activates the name change input
		 */
		function activate (event) {
			isActive.value = true
			emit('update:active', true)
			nextTick(() => root.value.focus())
		}

		function deactivate (event) {
			// only rename if content is editable
			if (!isActive.value) {
				return
			}

			let el = event.target
			let range = document.createRange()
			let sel = window.getSelection()
			range.setStart(el, 0)
			range.collapse(true)
			sel.removeAllRanges()
			sel.addRange(range)

			isActive.value = false
			emit('update:active', false)
			emit('update:modelValue', event.target.innerText)
		}

		return {
			isActive,
			activate,
			deactivate,
			root,
			computedModelValue
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
