<template>
	<div
		class="zb-el-iconBox"
	>
		<slot name="start" />

		<div
			class="zb-el-iconBox-iconWrapper"
			v-if="options.icon"
		>
			<ElementIcon
				class="zb-el-iconBox-icon"
				:iconConfig="options.icon"
				:class="api.getStyleClasses('icon_styles')"
			/>
		</div>
		<div class="zb-el-iconBox-text">
			<component
				v-if="options.title"
				:is="titleTag"
				class="zb-el-iconBox-title"
				:class="api.getStyleClasses('title_styles')"
			>
				{{options.title ? options.title: null}}
			</component>
			<div
				v-if="options.description"
				class="zb-el-iconBox-description"
				:class="api.getStyleClasses('description_styles')"
			>
				<RenderValue option="description" />
			</div>
		</div>

		<slot name="end" />
	</div>
</template>

<script>
export default {
	name: 'icon_box',
	props: ['options', 'data', 'api'],
	computed: {
		titleTag () {
			return this.options.title_tag || 'h3'
		}
	}
}
</script>
