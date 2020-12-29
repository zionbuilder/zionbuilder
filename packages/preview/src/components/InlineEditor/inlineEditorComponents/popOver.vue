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
			:class='buttonClasses'
		/>

	</Tooltip>
</template>

<script>
import { computed } from 'vue'

export default {
	name: 'PopOver',
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
	setup (props) {
		const buttonClasses = computed(() => {
			let classes = []

			// Check if the button has an icon
			if (typeof props.icon !== 'undefined') {
				classes.push('zn_pb_icon')
				classes.push(props.icon)
			}

			if (props.isActive) {
				classes.push('zion-inline-editor-button--active')
			}

			return classes.join(' ')
		})

		return {
			buttonClasses
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
