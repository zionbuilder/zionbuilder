<template>
	<div
		ref="root"
		class="zbpb-element-toolbox__titleFakeWrapper"
		:class="{
			'zbpb-element-toolbox__titleFakeWrapper--bottom': exitsTop,
			'zbpb-element-toolbox__titleFakeWrapper--left': exitsRight,
		}"
	>
		<div class="zbpb-element-toolbox__titleWrapper">
			<span
				v-for="parent in parents"
				:key="parent.uid"
				class="zbpb-element-toolbox__titleContainer"
				:class="{ 'zbpb-element-toolbox__titleContainer--active': parent === editedElement }"
				@click.stop="editElement(parent)"
				@contextmenu="showElementMenu($event, parent)"
			>
				<Icon icon="select" class="zbpb-element-toolbox__icon" :size="9" />

				<span class="zbpb-element-toolbox__title">{{ parent.name }}</span>
			</span>
		</div>
	</div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useUIStore } from '/@/editor/store';

export default {
	name: 'ToolboxTitle',
	props: {
		element: {
			type: Object,
			required: true,
		},
	},
	setup(props) {
		const root = ref(null);
		const { element: editedElement } = window.zb.editor.useEditElement();
		const UIStore = useUIStore();

		const parents = computed(() => {
			let parents = [];
			let activeElement = props.element;

			while (activeElement) {
				parents.push(activeElement);

				activeElement =
					activeElement.parent && activeElement.parent.element_type !== 'contentRoot' ? activeElement.parent : null;
			}

			return parents.reverse();
		});

		function editElement(element) {
			const { editElement } = useEditElement();

			editElement(element);
		}

		// Lifecycle
		onMounted(checkReposition);

		const exitsTop = ref(false);
		const exitsRight = ref(false);

		function checkReposition() {
			const boundingClientRect = root.value.getBoundingClientRect();

			// Check if exits top
			exitsTop.value = boundingClientRect.top + window.scrollY < 0;
			exitsRight.value = boundingClientRect.right > (window.innerWidth || document.documentElement.clientWidth);
		}

		function showElementMenu(event, element) {
			event.preventDefault();
			event.stopPropagation();

			UIStore.showElementMenuFromEvent(element, event);
		}

		return {
			parents,
			root,
			exitsTop,
			exitsRight,
			editedElement,

			// Methods
			editElement,
			showElementMenu,
		};
	},
};
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
	box-shadow: 0 0 5px -1px rgba(0, 0, 0, 0.35);
	border-radius: 2px;
}

.zbpb-element-toolbox__titleContainer {
	display: flex;
	overflow: hidden;
	max-width: 0;
	font-weight: 400;
	white-space: nowrap;
	transition: max-width 0.25s;
	cursor: pointer;
	opacity: 0.8;

	&--active,
	&:hover {
		opacity: 1;
	}

	&--active {
		font-weight: 500;
	}

	&:last-child {
		.zbpb-element-toolbox__icon {
			transform: rotate(90deg);
			transition: all 0.25s;
		}
	}

	.zbpb-element-toolbox__titleWrapper:hover &:last-child .zbpb-element-toolbox__icon {
		transform: rotate(-90deg);
	}

	&:first-child .zbpb-element-toolbox__icon {
		display: none;
	}

	.zbpb-element-toolbox__icon {
		transform: rotate(-90deg);
	}

	.zbpb-element-toolbox__titleWrapper:hover &,
	&:last-child {
		max-width: 200px;
	}

	.zbpb-element-toolbox__title {
		padding: 0 5px;
	}
}

// Reposition in case of outside
.zbpb-element-toolbox__titleFakeWrapper--bottom {
	top: 100%;
	bottom: auto;
	padding-top: 5px;
	padding-bottom: 0;
}

.zbpb-element-toolbox__titleFakeWrapper--left {
	right: 0;
	left: auto;
}
</style>
