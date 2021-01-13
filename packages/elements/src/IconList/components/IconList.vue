<template>
	<div>
		<slot name="start" />

		<component
			v-for="(item, index) in iconListConfig"
			:key="index"
			:is="item.link && item.link.link ? 'a' : 'span'"
			class="zb-el-iconList__item"
			:class="[`zb-el-iconList__item--${index} `,api.getStyleClasses('item_styles')]"
			v-bind="api.getAttributesForTag('item_styles',{}, index)"
		>
			<ElementIcon
				class="zb-el-iconList__itemIcon"
				:class="api.getStyleClasses('icon_styles')"
				v-bind="api.getAttributesForTag('icon_styles')"
				:iconConfig="item.icon"
			/>

			<span
				v-if="item.text"
				class="zb-el-iconList__itemText"
				:class="api.getStyleClasses('text_styles')"
				v-bind="api.getAttributesForTag('text_styles')"
				v-html="item.text"
			/>
		</component>

		<slot name="end" />
	</div>
</template>

<script>
export default {
	name: 'icon_list',
	props: ['options', 'element', 'api'],
	computed: {
		iconListConfig () {
			return this.options.icons ? this.options.icons : []
		}
	}
}
</script>
