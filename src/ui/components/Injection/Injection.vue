<template>
	<component :is="customComponent" v-for="(customComponent, i) in computedComponents" :key="i" />
</template>

<script lang="ts">
export default {
	name: 'Injection',
	inheritAttrs: false,
};
</script>

<script lang="ts" setup>
import { computed } from 'vue';
import { useInjections } from '../../composables';

const props = withDefaults(
	defineProps<{
		location: string;
		htmlTag?: string;
	}>(),
	{
		htmlTag: 'div',
	},
);

const { getComponentsForLocation } = useInjections();
const computedComponents = computed(() => getComponentsForLocation(props.location));
</script>
