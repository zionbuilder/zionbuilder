<template>
	<component
		:is="elementWrapperComponent"
		:element="element"
		v-bind="attrs"
		ref="elementRef"
	/>
</template>

<script>
import { computed, ref } from 'vue'
import { applyFilters, trigger } from '@zb/hooks'

// Components
import ElementWrapper from './ElementWrapper.vue'

export default {
	name: 'Element',
	props: ['element', 'onElementSetup'],
	setup (props) {
		const attrs = ref({})
		const elementRef = ref({})

		// Trigger an action here so we can use provide/inject and other component lifecycle events
		trigger('zionbuilder/preview/element/setup', props.element)

		// Get the element component
		const elementWrapperComponent = computed(() => {
			return applyFilters('zionbuilder/preview/element/wrapper_component', ElementWrapper, props.element)
		})

		if (typeof props.onElementSetup === 'function') {
			props.onElementSetup.apply(null, [elementRef, attrs])
		}

		return {
			elementWrapperComponent,
			attrs,
			elementRef
		}
	}
}
</script>

<style>
</style>