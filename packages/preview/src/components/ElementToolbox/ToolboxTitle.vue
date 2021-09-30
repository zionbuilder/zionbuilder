<template>
	<div class="zbpb-element-toolbox__titleWrapper">
		<span
			v-for="parent in parents"
			:key="parent.uid"
			class="zbpb-element-toolbox__titleContainer"
		>
			<Icon
				icon="select"
				class="zbpb-element-toolbox__icon"
				:size="9"
			/>

			<span class="zbpb-element-toolbox__title">{{parent.name}}</span>
		</span>
	</div>

</template>

<script>
import { computed } from 'vue'

export default {
	name: 'ToolboxTitle',
	props: {
		element: {
			type: Object,
			required: true
		}
	},
	setup (props) {
		const parents = computed(() => {
			let parents = []
			let activeElement = props.element

			while (activeElement) {
				parents.push(activeElement)

				activeElement = activeElement.parent && activeElement.parent.element_type !== 'contentRoot' ? activeElement.parent : null
			}

			return parents.reverse()
		})

		return {
			parents
		}
	}
}
</script>

<style lang="scss">
.zbpb-element-toolbox__titleWrapper {
	position: absolute;
	top: -30px;
	left: -1px;
	z-index: 999;
	display: flex;
	padding: 5px 3px;
	color: #fff;
	font-size: 11px;
	line-height: 1;
	background: #006dd2;
	border-radius: 2px;
	pointer-events: all;
}

.zbpb-element-toolbox__titleContainer {
	display: flex;
	overflow: hidden;
	max-width: 0;
	transition: all .3s;

	&:first-child .zbpb-element-toolbox__icon {
		display: none;
	}

	.zbpb-element-toolbox__icon {
		transform: rotate(-90deg);
	}

	.zbpb-element-toolbox__titleWrapper:hover &, &:last-child {
		max-width: 200px;
	}

	.zbpb-element-toolbox__title {
		padding: 0 5px;
	}
}
</style>