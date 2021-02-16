<template>
	<Element
		:key="element.uid"
		:element="element"
		:is-repeater-item="true"
		:class="{
			'znpb-element__wrapper--is-disabled': isDisabled
		}"
	/>
</template>

<script>
import { computed } from 'vue'

export default {
	name: "RepeaterElement",
	props: ['element', 'repeaterConsumerInstance'],
	setup (props) {
		const { iterate, provideRepeaterItem, mainElement } = props.repeaterConsumerInstance

		provideRepeaterItem()
		iterate()

		const isDisabled = computed(() => {
			return props.element !== mainElement
		})

		return {
			isDisabled
		}
	}
}
</script>

<style lang="scss">
.znpb-element__wrapper--is-disabled {
	opacity: .6 !important;
	pointer-events: none !important;
}
</style>