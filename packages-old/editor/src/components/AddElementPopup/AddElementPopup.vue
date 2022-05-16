<template>
	<Tooltip
		v-if="activePopup"
		:key="activePopup.key"
		tooltip-class="hg-popper--big-arrows"
		placement="auto"
		:show="true"
		append-to="body"
		:trigger="null"
		:close-on-outside-click="true"
		:close-on-escape="true"
		:popper-ref="activePopup.selector"
		@hide="hideAddElementsPopup"
	>
		<template #content>
			<ColumnTemplates :element="activePopup.element" @close="hideAddElementsPopup" />
		</template>
	</Tooltip>
</template>

<script>
import { watch } from 'vue';
import { useAddElementsPopup } from '@composables';

// Components
import ColumnTemplates from './ColumnTemplates.vue';

export default {
	name: 'AddElementPopup',
	components: {
		ColumnTemplates,
	},
	setup() {
		const { activePopup, hideAddElementsPopup } = useAddElementsPopup();

		watch(activePopup, newValue => {
			if (newValue) {
				window.addEventListener('scroll', hideAddElementsPopup);
			} else {
				window.removeEventListener('scroll', hideAddElementsPopup);
			}
		});

		return {
			activePopup,
			hideAddElementsPopup,
		};
	},
};
</script>
