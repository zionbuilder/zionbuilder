<template>
	<div class="znpb-menu">
		<div
			v-for="action in actions"
			:key="action.title"
			class="znpb-menu-item"
			:class="[{ 'znpb-menu-item--disabled': action.show === false }, action.cssClasses]"
			@click.stop="performAction(action)"
		>
			<Icon v-if="action.icon" class="znpb-menu-itemIcon" :icon="action.icon" />

			<span class="znpb-menu-itemTitle">{{ action.title }}</span>

			<span v-if="action.append" class="znpb-menu-itemAppend">
				{{ action.append }}
			</span>
		</div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'Menu',
};
</script>

<script lang="ts" setup>
export interface Action {
	title: string;
	icon?: string;
	cssClasses?: string;
	append?: string;
	show?: boolean;
	// eslint-disable-next-line @typescript-eslint/ban-types
	action: Function;
}

defineProps<{
	actions: Action[];
}>();

const emit = defineEmits(['action']);

function performAction(action: Action) {
	action.action();
	emit('action');
}
</script>

<style lang="scss">
.znpb-menu-item {
	display: flex;
	min-width: 200px;
	padding: 0 12px;
	cursor: pointer;

	&:first-child {
		border-top-right-radius: 4px;
		border-top-left-radius: 4px;
	}

	&:last-child {
		border-bottom-right-radius: 4px;
		border-bottom-left-radius: 4px;
	}

	span {
		padding: 8px 0;
		margin-right: 12px;
		font-weight: 500;
		transition: background-color 0.2s;

		&:last-child {
			margin-right: 0;
		}
	}

	&:hover {
		color: var(--zb-surface-text-active-color);
		background-color: var(--zb-surface-lighter-color);
	}

	&--disabled {
		cursor: not-allowed;
		opacity: 0.3;
		pointer-events: none;
	}
}

.znpb-menu-itemTitle {
	flex-grow: 1;
}

.znpb-menu-itemAppend {
	opacity: 0.5;
}

.znpb-menu-item--separator-bottom {
	border-bottom: 1px solid var(--zb-surface-lighter-color);
}
</style>
