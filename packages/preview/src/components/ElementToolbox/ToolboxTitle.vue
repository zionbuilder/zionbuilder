<template>
	<div class="zbpb-element-toolbox__titleFakeWrapper">
		<div class="zbpb-element-toolbox__titleWrapper">
			<span
				v-for="parent in parents"
				:key="parent.uid"
				class="zbpb-element-toolbox__titleContainer"
				@click.stop="editElement(parent)"
			>
				<Icon
					icon="select"
					class="zbpb-element-toolbox__icon"
					:size="9"
				/>

				<span class="zbpb-element-toolbox__title">{{parent.name}}</span>
			</span>
		</div>
	</div>

</template>

<script>
import { computed } from 'vue'
import { useEditElement } from '@zb/editor'

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

		function editElement (element) {
			const { editElement } = useEditElement()

			editElement(element)
		}

		return {
			parents,

			// Methods
			editElement
		}
	}
}
</script>

<style lang="scss">
.zbpb-element-toolbox__titleFakeWrapper {
	position: absolute;
	bottom: 100%;
	left: -1px;
	z-index: 1001;
	display: flex;
	padding-bottom: 5px;
	pointer-events: all;
}

.zbpb-element-toolbox__titleWrapper {
	display: flex;
	padding: 3px 5px;
	color: #fff;
	font-size: 11px;
	background: #006dd2;
	box-shadow: 0 0 5px -1px rgba(0, 0, 0, .35);
	border-radius: 2px;
}

.zbpb-element-toolbox__titleContainer {
	display: flex;
	overflow: hidden;
	max-width: 0;
	font-weight: 400;
	transition: all .25s;
	cursor: pointer;

	&:last-child {
		.zbpb-element-toolbox__title {
			font-weight: 700;
		}

		.zbpb-element-toolbox__icon {
			transform: rotate(90deg);
			transition: all .25s;
		}
	}

	.zbpb-element-toolbox__titleWrapper:hover
	&:last-child
	.zbpb-element-toolbox__icon {
		transform: rotate(-90deg);
	}

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