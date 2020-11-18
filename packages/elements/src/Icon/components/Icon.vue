<template>
	<div>
		<slot name="start" />

		<a
			v-if="hasLink"
			:href="options.link.link ? options.link.link : null"
			:target="options.link.target ? options.link.target : null"
			:title="options.link.title ? options.link.title : null"
			class="zb-el-icon-link zb-el-icon-icon"
		>
			<ElementIcon :iconConfig="iconConfig" />
		</a>
		<ElementIcon
			v-else
			class="zb-el-icon-icon"
			:iconConfig="iconConfig"
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
		}
	}
}
</script>
