<template>
	<RepeaterItem
		v-for="childElement in repeaterElements"
		:key="childElement.uid"
		:element="childElement"
		:repeater-consumer-instance="repeaterConsumerInstance"
		v-bind="$attrs"
	/>
</template>

<script>
import { useRepeaterConsumer } from '@zb/editor'
import RepeaterItem from './RepeaterItem.vue'

export default {
	name: "RepeaterContainer",
	inheritAttrs: false,
	components: {
		RepeaterItem
	},
	props: ['element'],
	setup (props) {
		const repeaterConsumerInstance = useRepeaterConsumer(props.element)
		const { repeaterElements } = repeaterConsumerInstance

		return {
			repeaterElements,
			repeaterConsumerInstance
		}
	}
}
</script>

<style>
</style>