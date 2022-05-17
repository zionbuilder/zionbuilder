<template>
	<Tooltip
		v-model:show="expanded"
		tooltip-class="hg-popper--no-padding"
		trigger="null"
		placement="right"
		:close-on-outside-click="true"
		:close-on-escape="true"
		class="znpb-hiddenMenuWrapper"
	>
		<template #content>
			<Menu :actions="actions" @action="expanded = !expanded" />
		</template>

		<Icon icon="more" :bg-size="14" @click.stop="expanded = !expanded" />
	</Tooltip>
</template>

<script lang="ts">
export default {
	name: 'HiddenMenu',
};
</script>

<script lang="ts" setup>
import { ref } from 'vue';
import Menu, { Action } from './Menu.vue';

defineProps<{
	actions: Action[];
}>();

const expanded = ref(false);
</script>
