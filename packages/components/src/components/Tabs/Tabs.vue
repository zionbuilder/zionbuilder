<template>
	<div class="znpb-tabs" :class="{ [`znpb-tabs--${tabStyle}`]: tabStyle }">
		<div class="znpb-tabs__header" :class="{ [`znpb-tabs__header--${titlePosition}`]: titlePosition }">
			<div
				v-for="(tab, index) in tabs"
				:key="index"
				class="znpb-tabs__header-item"
				:class="{
					'znpb-tabs__header-item--active': tab.id === activeTab,
					[`znpb-tabs__header-item--${tab.title.toLowerCase()}`]: tab.title,
				}"
				@click="selectTab(tab)"
			>
				<RenderComponent :render-slot="tab.slots.title ? tab.slots.title : tab.title" />
			</div>
		</div>
		<div class="znpb-tabs__content" :class="{ 'znpb-fancy-scrollbar': hasScroll }">
			<slot />
		</div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'Tabs',
};
</script>

<script lang="ts" setup>
import { onMounted, provide, ref, watch } from 'vue';

const props = withDefaults(
	defineProps<{
		tabStyle?: 'minimal' | 'card' | 'group';
		titlePosition?: 'start' | 'center' | 'end';
		activeTab?: string;
		hasScroll?: string[];
	}>(),
	{
		tabStyle: 'card',
		titlePosition: 'start',
		activeTab: '',
		hasScroll: () => [],
	},
);

const tabs = ref([]);
const activeTab = ref(props.activeTab);

const emit = defineEmits(['update:activeTab', 'changed-tab']);

function addTab(tab) {
	tabs.value.push(tab);
}

function removeTab(tabId: string) {
	const index = tabs.value.find(tab => tab.id === tabId);
	if (index !== undefined) {
		tabs.value.splice(index, 1);
	}
}

watch(
	() => props.activeTab,
	newValue => {
		activeTab.value = newValue;
	},
);

provide('TabsManager', {
	addTab,
	removeTab,
	activeTab,
	tabs,
});

function RenderComponent(props: any) {
	if (typeof props['render-slot'] === 'function') {
		return props['render-slot']();
	} else {
		return props['render-slot'];
	}
}

function selectTab(tab) {
	activeTab.value = tab.id;
	emit('changed-tab', tab.id);
	emit('update:activeTab', tab.id);
}

onMounted(() => {
	if (!props.activeTab && tabs.value[0]) {
		selectTab(tabs.value[0]);
	}
});
</script>

<style lang="scss">
.znpb-tabs {
	max-width: 100%;

	& > &__header {
		display: flex;
		margin: 0 20px;

		& > .znpb-tabs__header-item {
			display: flex;
			justify-content: center;
			align-items: center;
			color: var(--zb-surface-text-color);
			font-size: 13px;
			font-weight: 500;
			cursor: pointer;
		}

		// Title position
		&--start {
			justify-content: start;
		}

		&--center {
			justify-content: center;
		}

		&--end {
			justify-content: flex-end;
		}
	}

	& > &__content {
		background: var(--zb-surface-color);
	}

	// Styles
	&--minimal &__header {
		margin: 0;
		& > .znpb-tabs__header-item {
			padding: 10px 25px;

			&:first-child {
				padding-left: 0;
			}

			&:last-child {
				padding-right: 0;
			}

			&--active,
			&:hover {
				color: var(--zb-surface-text-active-color);
			}
		}
	}

	&--card > &__header {
		& > .znpb-tabs__header-item {
			padding: 12.5px 20px;

			&--active,
			&:hover {
				color: var(--zb-surface-text-active-color);
				background: var(--zb-surface-color);
				border-radius: 3px 3px 0 0;
			}
		}
	}

	&--group > &__header {
		padding: 3px;
		margin: 0 0 20px 0;
		background: var(--zb-surface-lighter-color);
		border-radius: 3px;

		& > .znpb-tabs__header-item {
			display: flex;
			justify-content: center;
			align-items: center;
			flex-grow: 1;
			padding: 10px 15px;
			color: var(--zb-surface-text-color);
			font-size: 14px;
			line-height: 1;
			border-radius: 2px;

			&:hover {
				color: var(--zb-surface-text-hover-color);
				background-color: var(--zb-surface-lightest-color);
			}

			&--active {
				color: var(--zb-secondary-text-color);
				background: var(--zb-secondary-color);

				&:hover {
					color: var(--zb-secondary-text-color);
					background: var(--zb-secondary-color);
				}
			}
		}
	}
}
</style>
