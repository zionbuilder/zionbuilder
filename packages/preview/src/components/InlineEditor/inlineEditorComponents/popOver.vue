<template>
	<Tooltip
		class="zion-inline-editor-popover-wrapper"
		tooltip-class="zion-inline-editor-dropdown hg-popper--no-padding"
		trigger="click"
		placement="top"
		append-to="element"
		:close-on-outside-click="true"
		@show="isPopOverVisible = true"
		@hide="isPopOverVisible = false"
	>

		<template #content>
			<slot></slot>
		</template>

		<Icon
			:icon="icon"
			:class='buttonClasses'
			@click="isPopOverVisible = !isPopOverVisible"
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
		isActive: {
			type: Boolean,
			required: false
		}
	},
	data: function () {
		return {
			isPopOverVisible: false
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

			if (this.isActive) {
				classes.push('zion-inline-editor-button--active')
			}

			return classes.join(' ')
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
