<template>
	<div class="zion-inline-editor-panel-color">
		<div class="zion-inline-editor-button">
			<ColorPicker
				:modelValue="color"
				@input="onColorChange"
				:show-library="false"
				@open="onOpen"
				@close="onClose"
				type="simple"
			></ColorPicker>
		</div>
	</div>
</template>

<script>

export default {
	inject: {
		Editor: {
			default () {
				return {}
			}
		}
	},
	data () {
		return {
			justChangedNode: false,
			color: null
		}
	},
	beforeMount: function () {
		this.Editor.editor.on('NodeChange', this.onNodeChange)
		this.getActiveColor()
	},
	beforeUnmount () {
		this.Editor.editor.off('NodeChange', this.onNodeChange)
	},
	methods: {
		onOpen () {
			this.$emit('open-color-picker', true)
			this.Editor.preventClose()
		},
		onClose () {
			this.$emit('close-color-picker', true)
			this.Editor.allowClose()
		},
		onColorChange (newValue) {
			this.color = newValue
			this.Editor.editor.formatter.apply('forecolor', { value: newValue })
			this.justChangedNode = true
		},
		onNodeChange (node) {
			if (node.selectionChange && !this.justChangedNode) {
				this.getActiveColor()
			}

			this.justChangedNode = false
		},
		getActiveColor () {
			// set a flag so we don't recursively update the color
			this.color = this.Editor.editor.queryCommandValue('forecolor')
		}
	}

}
</script>

<style lang="scss">
.vc-chrome {
	width: 100% !important;
}
.vc-chrome * {
	user-select: none !important;
}

.zion-inline-editor-button {
	position: relative;
}
.zion-inline-editor-panel-color {
	& > .zion-inline-editor-button {
		padding: 14px 11px;
	}

	.znpb-form-colorpicker {
		padding: 0;
		span:first-child {
			width: 15px;
			height: 15px;
			background-position: 0 0, 3px 3px;
			background-size: 6px 6px;
		}
		.znpb-colorpicker-circle {
			width: 15px;
			height: 15px;
			box-shadow: 0 0 0 2px rgb(56, 56, 56);
			&.znpb-colorpicker-circle--no-color:before {
				width: 15px;
			}
			&--trigger {
				box-shadow: none;
			}
		}
	}
}
.zion-inline-editor-color-picker-button {
	content: " ";
	position: absolute;
	top: 50%;
	left: 50%;
	width: 20px;
	height: 20px;
	background: #fff;
	border-radius: 50%;
	transform: translate(-50%, -50%);
}
