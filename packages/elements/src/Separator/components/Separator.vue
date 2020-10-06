<template>
	<div>
		<slot name="start" />

		<div class="zb-el-zionSeparator-item zb-el-zionSeparator-item--size"
			v-if="!options.use_icon"
		/>

		<div class="zb-el-zionSeparator-item-icon zb-el-zionSeparator-item--size"
			v-else
		>
			<span
				class="zb-el-zionSeparator-item zb-el-zionSeparator-icon-line zb-el-zionSeparator-icon-line-one"
			></span>
			<ElementIcon
				class="zb-el-zionSeparator-icon"
				:iconConfig="iconConfig"
			/>
			<span
				class="zb-el-zionSeparator-item zb-el-zionSeparator-icon-line zb-el-zionSeparator-icon-line-two"
			></span>
		</div>

		<slot name="end" />
	</div>
</template>

<script>
export default {
	name: 'zion_separator',
	props: ['options', 'data', 'api'],
	computed: {
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
