<template>
	<div class="znpb-option-cssChildSelector">
		<CSSSelector
			class="znpb-option-cssChildSelectorStyles"
			v-model="modelValue"
			:is-child="true"
		/>
	</div>
</template>

<script>
import { defineAsyncComponent } from 'vue'

export default {
	name: 'ChildSelector',
	props: {
		modelValue: {
			type: Object,
			required: false
		}
	},
	components: {
		CSSSelector: defineAsyncComponent(() => import('./CSSSelector.vue'))
	},
	setup (props, { emit }) {
		return {

		}
	}
}
</script>

<style lang="scss">
.znpb-option-cssChildSelector {
	display: flex;
	align-items: flex-start;
}

.znpb-option-cssChildSelectorStyles {
	flex-grow: 1;

	& .znpb-option-cssSelectorAccordion > .znpb-horizontal-accordion__header {
		padding: 8px 10px;
	}
}

.znpb-option-cssSelectorTitle {
	font-size: 11px;
}
</style>