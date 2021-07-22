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
import { computed } from 'vue'

export default {
	name: 'icon',
	props: ['options', 'element', 'api'],
	setup (props) {
		const hasLink = computed(() => {
			return props.options.link && props.options.link.link && props.options.link.link !== ''
		})

		const iconStyle = computed(() => {
			return props.options.style && props.options.style !== '' ? props.options.style : 'default'
		})

		const iconConfig = computed(() => {
			return props.options.icon || {
				'family': 'Font Awesome 5 Free Regular',
				'name': 'star',
				'unicode': 'uf005'
			}
		})

		const iconUnicode = computed(() => {
			const json = `"\\${iconConfig.value.unicode}"`
			return JSON.parse(json).trim()
		})

		return {
			iconUnicode,
			hasLink,
			iconStyle,
			iconConfig
		}
	}
}
</script>
