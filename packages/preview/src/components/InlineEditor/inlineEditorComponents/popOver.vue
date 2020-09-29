<template>
	<div class="zion-inline-editor-popover-wrapper"
	:class="{
		'zion-inline-editor-popover-wrapper--full-width' : fullWidth,
		'zion-inline-editor-popover-wrapper--vertical': direction,
		'zion-inline-editor-popover-wrapper--open': visible && isPopOverVisible,
	}">
		<BaseIcon
			:icon="icon"
			@mousedown.native.prevent="togglePopper"
			:class='buttonClasses'
		/>
		<transition name="bar-show">
			<div v-if="isPopOverVisible" class="zion-inline-editor-dropdown zion-inline-editor-dropdown--popover">
				<slot></slot>
			</div>
		</transition>
	</div>
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
.zion-inline-editor {
	.zion-inline-editor-dropdown {
		position: absolute;
		right: 0;
		bottom: 100%;
		display: flex;
		background: $surface;
		box-shadow: 0 2px 15px 0 rgba(0, 0, 0, .1);
		border: 1px solid #f1f1f1;
		border-radius: 3px;

		& > .znpb-editor-icon-wrapper {
			padding: 6px 10px;
		}
	}
	.zion-inline-editor-popover-wrapper {
		display: flex;

		&--full-width {
			.zion-inline-editor-dropdown {
				width: 100%;
			}
		}
		&--vertical {
			.zion-inline-editor-dropdown {
				flex-direction: column;
			}
		}
		&--big-pd {
			.zion-inline-editor-dropdown--popover {
				padding: 20px;
			}
		}
		& > .znpb-editor-icon-wrapper {
			padding: 10px 11px;
		}
		& > .align--center {
			padding: 15px 12px;
			font-size: 13px;
		}

		& .znpb-input-wrapper {
			padding-right: 0;
			padding-left: 0;
		}

		& .zion-inline-editor-popover__link-title .znpb-input-wrapper {
			padding-bottom: 0;
		}
	}
	.zion-inline-editor-dropdown {
		margin-bottom: 7px;
	}
	.zion-inline-editor-dropdown--popover {
		padding: 10px;
		font-size: 14px;

		.zion-inline-editor-button--active {
			&.zion-inline-editor-button, &.znpb-editor-icon-wrapper {
				color: $surface;
			}
		}
	}
}

.zion-inline-editor-dropdown--popover {
	& > .zion-input {
		margin-bottom: 15px;
	}
}
/* popover animations */
.bar-show-enter-active, .bar-show-leave-active {
	transition: all .2s;
}
.bar-show-enter, .bar-show-leave-to {
	opacity: 0;
}
</style>
