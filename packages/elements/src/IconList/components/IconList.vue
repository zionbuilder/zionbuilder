<template>
	<div>
		<slot name="start" />

		<component
			v-for="(item, index) in iconListConfig"
			:key="index"
			:is="item.link && item.link.link ? 'a' : 'div'"
			class="zb-el-iconList__item"
		>
			<ElementIcon
				class="zb-el-iconList__itemIcon"
				:class="api.getStyleClasses('icon_styles')"
				:iconConfig="item.icon"
			/>

			<span
				v-if="item.text"
				class="zb-el-iconList__itemText"
				:class="api.getStyleClasses('text_styles')"
				v-html="item.text"
			/>
		</component>

		<slot name="end" />
	</div>
</template>

<script>
export default {
	name: 'icon_list',
	props: ['options', 'data', 'api'],
	computed: {
		getTag (item) {
			// Check if has link else span
			return item.link && item.link.link ? 'a' : 'div'
		},
		iconListConfig () {
			return this.options.icons ? this.options.icons : []
		}
	}
}
</script>
