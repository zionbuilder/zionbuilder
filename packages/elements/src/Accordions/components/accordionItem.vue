<template>
	<div
		class="zb-el-accordions-accordionWrapper"
		:class="{'zb-el-accordions--active': options.active_by_default}"
	>
		<slot name="start" />

		<div class="zb-el-accordions-accordionTitle">
			{{options.title}}
			<span class="zb-el-accordions-accordionIcon"></span>
		</div>
		<div class="zb-el-accordions-accordionContent">
			<div
				v-html="options.content"
				class="zb-el-accordions-accordionContent__inner"
			></div>
		</div>

		<slot name="end" />
	</div>
</template>

<script>
export default {
	name: 'accordion_item',
	props: ['options', 'element', 'api']
}
</script>
