<template>
	<Tooltip
		v-if="UIStore.activeAddElementPopup"
		:key="UIStore.activeAddElementPopup.key"
		tooltip-class="hg-popper--big-arrows"
		placement="auto"
		:show="true"
		append-to="body"
		:trigger="null"
		:close-on-outside-click="true"
		:close-on-escape="true"
		:popper-ref="UIStore.activeAddElementPopup.selector"
		@hide="UIStore.hideAddElementsPopup"
	>
		<template #content>
			<ColumnTemplates :element="UIStore.activeAddElementPopup.element" @close="UIStore.hideAddElementsPopup" />
		</template>
	</Tooltip>
</template>

<script lang="ts" setup>
import { watch } from 'vue';
import { useUIStore } from '../../store';

// Components
import ColumnTemplates from './ColumnTemplates.vue';

const UIStore = useUIStore();

watch(
	() => UIStore.activeAddElementPopup,
	newValue => {
		if (newValue) {
			window.addEventListener('scroll', UIStore.hideAddElementsPopup);
		} else {
			window.removeEventListener('scroll', UIStore.hideAddElementsPopup);
		}
	},
);
</script>
