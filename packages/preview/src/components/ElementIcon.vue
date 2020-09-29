<template>
	<span
		v-if="iconConfig"
		:data-znpbiconfam="iconConfig.family"
		:data-znpbicon="iconUnicode"
	/>
</template>

<script>
export default {
	name: 'ElementIcon',
	props: {
		iconConfig: {
			type: Object,
			required: false,
			default () {
				return {
					family: 'Font Awesome 5 Brands Regular',
					name: 'wordpress-simple',
					unicode: 'uf411'
				}
			}
		}
	},
	computed: {
		iconUnicode () {
			const unicode = JSON.parse(`"\\${this.iconConfig.unicode}"`)
			return unicode.trim()
		}
	}
}
</script>

<style>

</style>
