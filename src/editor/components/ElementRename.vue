<template>
	<div
		:class="{'znpb-utility__text--elipse': !isContentEditable}"
		:contenteditable="isContentEditable"
		v-on:dblclick.stop="doActivateNameChange"
		@blur="doRenameElement"
	>
		{{content}}
	</div>
</template>

<script>
export default {
	name: 'ElementRename',
	props: {
		content: {
			type: String,
			required: true
		},
		isActive: {
			type: Boolean,
			required: false,
			default: false
		}
	},
	data: function () {
		return {
			isContentEditable: this.isActive
		}
	},
	watch: {
		isActive: function (newValue) {
			if (newValue) {
				this.doActivateNameChange()
			}
		}
	},
	methods: {
		/**
		 * Activates the name change input
		 */
		doActivateNameChange (event) {
			this.isContentEditable = true
			this.$nextTick(() => this.$el.focus())
		},
		doRenameElement (event) {
			// only rename if content is editable
			if (!this.isContentEditable) {
				return
			}

			let el = event.target
			let range = document.createRange()
			let sel = window.getSelection()
			range.setStart(el, 0)
			range.collapse(true)
			sel.removeAllRanges()
			sel.addRange(range)

			this.isContentEditable = false
			this.$emit('name-changed', event.target.innerText)
		}
	}
}
</script>
<style lang="scss">
.znpb-utility__text--elipse::after {
	content: '';
	position: absolute;
	top: 1px;
	right: 1px;
	bottom: 0;
	width: 35px;
	height: calc(100% - 2px);
	background: linear-gradient(-90deg, $surface-variant 0%, rgba($surface-variant, .3) 100%);
}

.znpb-panel-item--hovered .znpb-utility__text--elipse::after {
	background: linear-gradient(-90deg, darken($surface-variant, 3%) 0%, rgba(darken($surface-variant, 3%), .3) 100%);
}

.znpb-panel-item--active .znpb-utility__text--elipse::after {
	background: linear-gradient(-90deg, $secondary 0%, rgba($secondary, .3) 100%);
}
</style>
