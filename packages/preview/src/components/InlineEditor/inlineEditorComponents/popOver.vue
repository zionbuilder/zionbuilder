<template>
	<Tooltip
		class="zion-inline-editor-popover-wrapper"
		tooltip-class="zion-inline-editor-dropdown hg-popper--no-padding"
		trigger="click"
		placement="top"
		append-to="element"
		:close-on-outside-click="true"
	>

		<template #content>
			<slot></slot>
		</template>

		<Icon
			:icon="icon"
			@mousedown.prevent="togglePopper"
			:class='buttonClasses'
		/>

	</Tooltip>
</template>

<script>
export default {
	props: {
		icon: {
			type: String,
			required: false
		},
		fullWidth: {
			type: Boolean,
			required: false
		},
		direction: {
			type: String,
			required: false
		},
		visible: {
			type: Boolean
		}
	},
	data: function () {
		return {
			isPopOverVisible: this.visible
		}
	},
	watch: {
		visible (newVal) {
			this.isPopOverVisible = newVal
		}
	},
	computed: {
		buttonClasses () {
			let classes = []

			// Check if the button has an icon
			if (typeof this.icon !== 'undefined') {
				classes.push('zn_pb_icon')
				classes.push(this.icon)
			}

			if (this.isPopOverVisible) {
				classes.push('zion-inline-editor-button--active')
			}

			return classes.join(' ')
		}
	},
	methods: {
		togglePopper () {
			if (this.isPopOverVisible) {
				this.isPopOverVisible = false
				this.$emit('close-panel', this)
			} else {
				this.isPopOverVisible = true
				this.$emit('open-panel', this)
			}
		}
	}
}
</script>

<style lang="scss">
.zion-inline-editor-popover-wrapper, .zion-inline-editor-dropdown {
	display: flex;

	& > .znpb-editor-icon-wrapper {
		padding: 10px 11px;
	}
}
</style>
