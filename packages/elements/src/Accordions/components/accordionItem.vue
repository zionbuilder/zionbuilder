<template>
	<div
		class="zb-el-accordions-accordionWrapper"
		:class="{'zb-el-accordions--active': activeByDefault}"
	>
		<slot name="start" />

		<div class="zb-el-accordions-accordionTitle">
			{{options.title}}
			<span class="zb-el-accordions-accordionIcon"></span>
		</div>
		<div class="zb-el-accordions-accordionContent">
			<div
				v-html="renderedContent"
				class="zb-el-accordions-accordionContent__inner"
			></div>
		</div>
		<slot name="end" />
	</div>
</template>

<script>
import { computed } from 'vue'
export default {
	name: 'accordion_item',
	props: ['options', 'element', 'api'],
	setup (props) {
		let renderedContent = computed(() => {
			return props.options.content ? props.options.content : 'accordion content'
		})
		let activeByDefault = computed(() => {
			return props.options.active_by_default ? props.options.active_by_default : false
		})

		return {
			renderedContent,
			activeByDefault
		}
	}
}
</script>
