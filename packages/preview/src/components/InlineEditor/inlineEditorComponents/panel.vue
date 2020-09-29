<template>
	<div class="zion-inline-editor-panel-wrapper"
		:class="{'zion-inline-editor-popover-wrapper--open' : visible && isPanelvisibile}"
	>
		<BaseIcon
			:icon="icon"
			@click.native="togglePanel"
			:class="buttonClasses"
		/>
		<transition name="panel-show">
			<div v-if="isPanelvisibile" :class="classes" class="zion-inline-editor-dropdown zion-inline-editor-dropdown--panel">
				<slot></slot>
			</div>
		</transition>
	</div>
</template>

<script>

export default {
	props: {
		icon: {
			type: String
		},
		buttontext: {
			type: String
		},
		direction: {
			type: String
		},
		visible: {
			type: Boolean
		}
	},

	data: function () {
		return {
			isPanelvisibile: this.visible
		}
	},
	watch: {
		visible (newVal) {
			this.isPanelvisibile = newVal
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

			if (this.isPanelvisibile) {
				classes.push('zion-inline-editor-button--active')
			}

			return classes.join(' ')
		},
		classes () {
			let classes = []

			if (typeof this.direction !== 'undefined') {
				classes.push('zion-inline-editor-dropdown--panel--direction-row')
			}

			return classes.join(' ')
		}
	},
	methods: {
		togglePanel () {
			if (this.isPanelvisibile) {
				this.hide_panel()
			} else {
				this.show_panel()
			}
		},
		show_panel () {
			this.isPanelvisibile = true
			this.$emit('open-panel', this)
		},
		hide_panel () {
			this.isPanelvisibile = false
			this.$emit('close-panel', this)
		}
		// getPanelVisibility () {
		// 	return this.isPanelvisibile
		// }
	}
}
</script>

<style lang="scss">
.zion-inline-editor-panel-wrapper {
	display: flex;
	align-items: center;
	color: $font-color;
	font-size: 14px;
	& > .znpb-editor-icon-wrapper {
		padding: 10px 11px;
	}

	.znpb-editor-icon-wrapper .zion-icon {
		width: auto;
	}
}
.zion-inline-editor-dropdown--panel {
	flex-direction: column;
	width: 100%;

	&--direction-row {
		flex-direction: row;
	}
}

/* popover animations */
.panel-show-enter-active, .panel-show-leave-active {
	transition: all .2s;
}
.panel-show-enter, .panel-show-leave-to {
	opacity: 0;
}

</style>
