<template>
	<Tooltip
		v-if="activeElementMenu"
		tooltip-class="hg-popper--big-arrows znpb-rightClickMenu__Tooltip"
		placement='auto'
		:show="true"
		append-to="body"
		trigger="click"
		:close-on-outside-click="true"
		:close-on-escape="true"
		:popperRef="activeElementMenu.selector"
		@hide="hideElementMenu"
		:key="activeElementMenu.key"
	>
		<template #content>
			<ElementActions
				class="znpb-element-options__element-actions"
				@click.stop="hideElementMenu"
				@changename="$emit('changename',true), showOptions=false"
				:element="activeElementMenu.element"
				:actions="activeElementMenu.actions"
			/>
		</template>
	</Tooltip>
</template>

<script>
import { ref } from 'vue'
import ElementActions from './ElementActions.vue'
import { useElementMenu } from '@composables'

export default {
	name: 'ElementMenu',
	components: {
		ElementActions
	},
	props: {
		elementUid: String,
		parentUid: String,
		isActive: {
			type: Boolean,
			required: false
		}
	},
	setup () {
		const showOptions = ref(false)
		const { activeElementMenu, hideElementMenu } = useElementMenu()

		return {
			showOptions,
			activeElementMenu,
			hideElementMenu
		}
	}
}
</script>
<style lang="scss">
.znpb-element-options {
	&__dropdown-icon {
		padding: 15px;
	}

	&__options-container {
		position: relative;
		position: absolute;
		top: 100%;
		right: 0;
		z-index: 9999;
		width: 172px;
		padding: 12px 0;
		margin-top: -1px;
		text-align: left;
		list-style-type: none;
		background: $surface;
		box-shadow: 0 0 16px 0 rgba(0, 0, 0, .08);
		border-radius: 3px;
		transition: all .5s;
		user-select: none;

		li {
			min-width: 172px;
			padding: 12px 29px;
			color: $font-color;
			font-size: 12px;
			line-height: 14px;
			transition: color .2s ease;
			&:hover {
				color: $surface-active-color;
				cursor: pointer;
			}
		}
	}
}

.list-enter-to, .list-leave-from {
	transition: all .2s;
}

.list-enter-from, .list-leave-to {
	transform: translateY(10%);
	opacity: 0;
}

.znpb-rightClickMenu__Tooltip {
	padding: 0;
}
</style>
