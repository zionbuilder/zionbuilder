<template>
	<div v-if="TabsManager.activeTab.value === tabId" class="znpb-tab__wrapper">
		<slot />
	</div>
</template>

<script lang="ts">
export default {
	name: 'Tab',
};
</script>

<script lang="ts" setup>
import { inject, useSlots, onBeforeMount } from 'vue';

const props = defineProps<{
	name: string;
	id?: string;
	active?: boolean;
}>();

const TabsManager = inject('TabsManager');
const slots = useSlots();
const tabId = props.id ? props.id : props.name.toLowerCase().replace(/ /g, '-');

onBeforeMount(() => {
	TabsManager.addTab({
		id: tabId,
		slots: slots,
		title: props.name,
	});
});
</script>
