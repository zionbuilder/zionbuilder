<template>
	<component :is="elementWrapperComponent" :element="element" />
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import { applyFilters, doAction } from '/@/common/modules/hooks';

const props = defineProps<{
	element: ZionElement;
}>();

// Trigger an action here so we can use provide/inject and other component lifecycle events
doAction('zionbuilder/preview/element/setup', props.element);

// Get the element component
const elementWrapperComponent = computed(() => {
	return applyFilters('zionbuilder/preview/element/wrapper_component', 'ElementWrapper', props.element);
});
</script>
