<template>
	<div class="znpb-accordion" :class="{ 'znpb-accordion--collapsed': localCollapsed }">
		<div class="znpb-accordion__header" @click="localCollapsed = !localCollapsed">
			<!-- @slot Content that will be placed inside the accordion header -->
			<slot name="header">{{ header }}</slot>
			<Icon icon="select" class="znpb-accordion-title-icon" />
		</div>
		<div v-if="localCollapsed" class="znpb-accordion__content">
			<!-- @slot Content that will be placed inside the accordion content -->
			<slot></slot>
		</div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'Accordion',
};
</script>

<script lang="ts" setup>
import { ref } from 'vue';
import Icon from '../Icon/Icon.vue';

interface IProps {
	/**
	 * If the accordion should be open or closed by default
	 */
	collapsed?: boolean;

	/**
	 * Text that will be displayed inside the accordion header
	 */
	header?: string;
}

const props = withDefaults(defineProps<IProps>(), {
	collapsed: false,
});

const localCollapsed = ref(props.collapsed);
</script>

<style lang="scss">
.znpb-accordion {
	width: 100%;
	border-bottom: 1px solid var(--zb-surface-lighter-color);

	&__header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 30px;
		font-size: 12px;
		font-weight: bold;
		line-height: 1;
	}

	&__content {
		padding: 0 30px;
	}

	&--collapsed {
		.znpb-accordion__header {
			h2.znpb-accordion-title {
				color: var(--zb-primary-color);
			}
			.znpb-accordion-title-icon {
				transform: rotate(180deg);
			}
		}
	}
}
</style>
