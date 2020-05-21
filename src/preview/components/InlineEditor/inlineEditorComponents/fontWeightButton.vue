<template>
	<span class="zion-inline-editor-button" :class="classses" @mousedown="setTextStyle">{{value}}</span>
</template>

<script>

export default {
	props: ['formatter', 'icon', 'buttontext', 'value'],
	inject: {
		Editor: {
			default () {
				return {}
			}
		}
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
		this.Editor.editor.onNodeChange.add(this.hasFormat)
	},
	methods: {
		// Apply button style
		setTextStyle (event) {
			// Remove Style if this is already active
			this.Editor.editor.formatter.apply('fontweight', { value: this.value })
		},
		// Check if the selection has a specific style applied
		hasFormat () {
			this.isActive = this.Editor.editor.formatter.match('fontweight', { value: this.value })
		}
	}
}
</script>

<style lang="scss" >
.zion-inline-editor-button {
	padding: 6px;
	font-size: 13px;
	font-weight: 500;
	line-height: 1;

	&:hover {
		color: darken($font-color, 10%);
	}

	@at-root {
		.znpb-editor-icon-wrapper.ite-weight ~ .zion-inline-editor-dropdown .zion-inline-editor-button--active {
			color: $surface-active-color;
		}
	}
}
</style>
