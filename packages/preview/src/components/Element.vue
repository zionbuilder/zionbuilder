<template>
	<component
		:is="elementWrapperComponent"
		:element="element"
	/>
</template>

<script>
import { computed } from 'vue'
import { applyFilters, trigger } from '@zb/hooks'
import ElementWrapper from './ElementWrapper.vue'

export default {
	name: 'Element',
	props: ['element'],
	setup (props) {
		trigger('zionbuilder/preview/element/setup', props.element)

		const elementWrapperComponent = computed(() => {
			return applyFilters('zionbuilder/preview/element/wrapper_component', ElementWrapper, props.element)
		})

		return {
			elementWrapperComponent
		}
	}
}
</script>

<style>
</style>