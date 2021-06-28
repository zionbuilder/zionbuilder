<template>
	<div>
		<slot name="start" />

		<a
			v-if="hasLink"
			:href="options.link.link ? options.link.link : null"
			:target="options.link.target ? options.link.target : null"
			:title="options.link.title ? options.link.title : null"
			class="zb-el-icon-link zb-el-icon-icon"
			:data-znpbiconfam="iconConfig.family"
			:data-znpbicon="iconUnicode"
			v-bind="api.getAttributesForTag('shape')"
		>
		</a>

		<ElementIcon
			v-else
			class="zb-el-icon-icon"
			:iconConfig="iconConfig"
			v-bind="api.getAttributesForTag('shape')"
		/>

		<slot name="end" />
	</div>
</template>

<script>
export default {
	name: 'icon',
	props: ['options', 'element', 'api'],
	computed: {
		hasLink () {
			return this.options.link && this.options.link.link && this.options.link.link !== ''
		},
		iconStyle () {
			return this.options.style && this.options.style !== '' ? this.options.style : 'default'
		},
		iconConfig () {
			return this.options.icon || {
				'family': 'Font Awesome 5 Free Regular',
				'name': 'star',
				'unicode': 'uf005'
			}
		},
		iconUnicode () {
			return JSON.parse(`"\\${this.iconConfig.unicode}"`).trim()
		}
	}
}
</script>
