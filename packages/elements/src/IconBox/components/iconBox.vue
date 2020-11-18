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
				v-bind="api.getAttributesForTag('icon_styles')"
			/>
		</div>
		<div class="zb-el-iconBox-text">
			<component
				v-if="options.title"
				:is="titleTag"
				class="zb-el-iconBox-title"
				v-bind="api.getAttributesForTag('title_styles')"
			>
				{{options.title ? options.title: null}}
			</component>
			<div
				v-if="options.description"
				class="zb-el-iconBox-description"
				v-bind="api.getAttributesForTag('description_styles')"
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
	props: ['options', 'element', 'api'],
	computed: {
		titleTag () {
			return this.options.title_tag || 'h3'
		}
	}
}
</script>
