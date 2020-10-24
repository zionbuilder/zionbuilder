<template>
	<Icon
		v-if="icon"
		:icon="icon"
		@mousedown="setTextStyle"
		:class="classses"
	/>

	<span
		v-else
		class="zion-inline-editor-button"
		:class="classses"
		@mousedown="setTextStyle"
	>{{buttontext}}</span>
</template>

<script>
import { Icon } from '@zb/components'

export default {
	props: ['formatter', 'icon', 'buttontext', 'value'],
	inject: {
		Editor: {
			default () {
				return {}
			}
		}
	},
	components: {
		Icon
	},
	data: function () {
		return {
			isActive: null
		}
	},
	computed: {
		classses () {
			let classes = []
			// Check if the button is active
			if (this.isActive) {
				classes.push('zion-inline-editor-button--active')
			}

			return classes.join(' ')
		}
	},
	beforeMount: function () {
		var self = this

		this.Editor.editor.formatter.formatChanged(this.formatter, function (state, args) {
			self.isActive = state
		})

		this.isActive = this.hasFormat(this.formatter)
	},

	methods: {
		// Apply button style
		setTextStyle (event) {
			event.preventDefault()
			// Remove Style if this is already active
			this.Editor.editor.formatter.toggle(this.formatter)
		},
		// Check if the selection has a specific style applied
		hasFormat (styleType) {
			return this.Editor.editor.formatter.match(styleType)
		}
	}
}
</script>

<style lang="scss" scoped>
</style>
