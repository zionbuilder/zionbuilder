<template>
	<div v-show="isActive" class="znpb-tab__wrapper">
		<slot name="title" />

		<slot />
	</div>
</template>

<script lang="ts">
export default {
	name: 'Tab',
};
</script>

<script lang="ts" setup>
import { inject, ref, watchEffect } from 'vue';

const props = defineProps<{
	name: string;
	icon?: string;
	id?: string;
	active?: boolean;
}>();

const isActive = ref(false);

const activeTab: any = inject('TabsProvider');

watchEffect(() => {
	isActive.value = activeTab.value === (props.id ?? props.name.toLowerCase().replace(/ /g, '-'));
});
</script>
