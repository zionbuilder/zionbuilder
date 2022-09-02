<template>
	<div class="zb-el-imageBox">
		<slot name="
		start" />

		<div
			class="zb-el-imageBox-imageWrapper"
			v-if="imageSrc"
		>
			<img
				class="zb-el-imageBox-image"
				:src="imageSrc"
				v-bind="api.getAttributesForTag('image_styles')"
				:class="api.getStyleClasses('image_styles')"
			/>
		</div>
		<span class="zb-el-imageBox-spacer"></span>
		<div
			class="zb-el-imageBox-text"
			:style="{
				'text-align': options.align
			}"
		>
			<component
				v-if="options.title"
				:is="titleTag"
				class="zb-el-imageBox-title"
				:class="api.getStyleClasses('title_styles')"
				v-bind="api.getAttributesForTag('title_styles')"
				v-html="options.title"
			>
			</component>
			<div
				v-if="options.description"
				class="zb-el-imageBox-description"
				:class="api.getStyleClasses('description_styles')"
				v-bind="api.getAttributesForTag('description_styles')"
			>
				<RenderValue option="description" />
			</div>
		</div>

		<slot name="start" />
	</div>
</template>

<script>
export default {
	name: 'image_box',
	props: ['element', 'options', 'api'],
	computed: {
		imageSrc () {
			return (this.options.image || {}).image
		},
		titleTag () {
			return this.options.title_tag || 'h3'
		}
	}
}
</script>
