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

<script lang="ts" setup>
import { computed, provide } from 'vue';

const props = defineProps<{
	options: {
		items?: ZionElementConfig[];
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

// Check to see if we need to add some accordions
if (props.element.content.length === 0 && props.options.items) {
	props.element.addChildren(props.options.items as ZionElementConfig[]);
}

const computedOptions = computed(() => props.options);

provide('accordionsApi', {
	...props.api,
	options: computedOptions,
});
</script>
