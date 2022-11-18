<template>
	<SortableContent :element="element">
		<template #start>
			<slot name="start" />
		</template>

		<template #end>
			<slot name="end" />
		</template>
	</SortableContent>
</template>

<script>
import { computed, provide } from 'vue';
export default {
	name: 'Accordions',
	props: ['options', 'element', 'api'],
	setup(props) {
		// Check to see if we need to add some accordions
		if (props.element.content.length === 0 && props.options.items) {
			props.element.addChildren(props.options.items);
		}

		const computedOptions = computed(() => props.options);

		provide('accordionsApi', {
			...props.api,
			options: computedOptions,
		});
	},
};
</script>
