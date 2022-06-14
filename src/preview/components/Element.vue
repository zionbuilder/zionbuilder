<template>
	<component :is="elementWrapperComponent" v-bind="attrs" ref="elementRef" :element="element" />
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue';
import { applyFilters, doAction } from '/@/common/modules/hooks';

// Components
import ElementWrapper from './ElementWrapper.vue';

const props = defineProps<{
	element: ZionElement;
	onElementSetup: Function;
}>();

const attrs = ref({});
const elementRef = ref({});

// Trigger an action here so we can use provide/inject and other component lifecycle events
doAction('zionbuilder/preview/element/setup', props.element);

// Get the element component
const elementWrapperComponent = computed(() => {
	return applyFilters('zionbuilder/preview/element/wrapper_component', ElementWrapper, props.element);
});

if (typeof props.onElementSetup === 'function') {
	props.onElementSetup.apply(null, [elementRef, attrs]);
}
</script>

<style></style>
