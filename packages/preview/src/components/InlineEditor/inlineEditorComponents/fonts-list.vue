<template>
	<div class=" ">
		<ul class="zion-inline-editor__font-panel znpb-fancy-scrollbar">
			<li
				v-for="(font, i) in fontsListForOption"
				:key="i"
				@click="changeFont(font.id, $event)"
				class="zion-inline-editor__font-list-item"
				:class="{'zion-inline-editor__font-list-item--active': isActive(font.id)}"
			>
				{{font.name}}

			</li>
		</ul>
	</div>
</template>

<script>
import { useDataSets } from '@zb/components'

export default {
	inject: {
		Editor: {
			default () {
				return {}
			}
		}
	},
	setup() {
		const { fontsListForOption } = useDataSets()

		return {
			fontsListForOption
		}
	},
	data: function () {
		return {
			justChangedNode: null,
			activeFont: null
		}
	},
	beforeMount: function () {
		this.Editor.editor.on('NodeChange', this.onNodeChange)
		this.getFontName(this.Editor.editor.selection.getNode())
	},
	methods: {
		isActive (fontName) {
			return this.activeFont === fontName ? 'zion-inline-editor__font-list-item--active' : ''
		},
		onNodeChange (node) {
			if (node.selectionChange && !this.justChangedNode) {
				this.getFontName(this.Editor.editor.selection.getNode())
			}
			this.justChangedNode = false
		},
		changeFont (font, event) {
			this.activeFont = font

			this.Editor.editor.formatter.toggle('fontname', {
				value: font
			})
			this.justChangedNode = true
			this.$emit('active-font', this.activeFont)
		},

		getFontName (font) {
			this.activeFont = this.Editor.editor.queryCommandValue('fontname')
		}
	}
}
</script>

<style lang="scss">
.zion-inline-editor {
	.zion-inline-editor {
		&__font-panel {
			width: 100%;
			padding: 8px 0;
			background: $surface;
		}
		&__font-list {
			display: block;
			overflow: hidden;
			overflow-y: scroll;
			width: 100%;
			max-height: 150px;
			padding: 0;
			margin: 0;
			list-style: none;

			&-item {
				display: flex;
				align-items: center;
				width: 100%;
				padding: 8px 16px;
				color: $font-color;
				font-family: $font-stack;
				font-size: 13px;
				font-weight: 500;
				text-align: left;
				transition: all .2s;

				&:hover, &--active {
					color: $surface-active-color;
					background-color: lighten($surface-variant, 2%);
				}
			}
		}
	}
}
</style>
