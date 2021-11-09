<template>
	<div class="znpb-menu">
		<div
			v-for="action in actions"
			:key="action.title"
			class="znpb-menu-item"
			:class="{
				'znpb-menu-item--disabled': action.show === false,
				[action.cssClasses]: action.cssClasses
			}"
			@click.stop="performAction(action)"
		>
			<Icon
				class="znpb-menu-itemIcon"
				:icon="action.icon"
				v-if="action.icon"
			/>

			<span class="znpb-menu-itemTitle">{{action.title}}</span>

			<span
				class="znpb-menu-itemAppend"
				v-if="action.append"
			>
				{{action.append}}
			</span>
		</div>
	</div>
</template>

<script>
export default {
	name: "Menu",
	props: {
		actions: {
			type: Array,
			required: true
		}
	},
	setup (props, { emit }) {
		function performAction (action) {
			action.action()
			emit('action', true)
		}

		return {
			performAction
		}
	}
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
		transition: background-color .2s;

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
		opacity: .3;
		pointer-events: none;
	}
}

.znpb-menu-itemTitle {
	flex-grow: 1;
}

.znpb-menu-itemAppend {
	opacity: .5;
}

.znpb-menu-item--separator-bottom {
	border-bottom: 1px solid var(--zb-surface-lighter-color);
}
</style>