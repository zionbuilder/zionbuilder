<template>
	<div class="znpb-menu">
		<div
			v-for="action in actions"
			:key="action.title"
			class="znpb-menu-item"
			:class="{'znpb-menu-item--disabled' : action.show === false}"
			@click.stop="performAction(action)"
		>
			<span>{{action.title}}</span>
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
		display: block;
		padding: 10px 16px 10px 16px;
		font-weight: 500;
		transition: background-color .2s;
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
</style>
