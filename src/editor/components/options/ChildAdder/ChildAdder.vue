<template>
	<div class="znpb-options-childs__wrapper">
		<Sortable v-model="element.content" class="znpb-options-childs__items-wrapper">
			<SingleChild
				v-for="element in element.content"
				:key="element.uid"
				:element="element"
				:item-option-name="item_name"
				:show-delete="canShowDeleteButton"
				@delete="element.delete"
				@clone="element.duplicate"
			/>
		</Sortable>

		<Button class="znpb-option-repeater__add-button" type="line" @click="addChild"> ADD </Button>
	</div>
</template>

<script>
import { computed } from 'vue';
import { useElementProvide, useElements } from '../../../composables';
import SingleChild from './SingleChild.vue';

export default {
	name: 'ChildAdder',
	components: {
		SingleChild,
	},
	props: {
		modelValue: {
			default() {
				return {};
			},
		},
		child_type: {
			type: String,
			required: true,
		},
		item_name: {
			type: String,
			required: false,
		},
		min: {
			type: Number,
		},
		add_template: {
			type: Object,
		},
	},
	setup(props) {
		const { injectElement } = useElementProvide();
		const element = injectElement();

		const canShowDeleteButton = computed(() => {
			if (props.min && element.value.content.length === props.min) {
				return false;
			}

			return true;
		});

		// Check to see if we need to add some tabs
		if (element.value.content.length === 0 && props.modelValue) {
			element.value.addChildren(props.modelValue);
		}

		function addChild() {
			const template = props.add_template
				? props.add_template
				: {
						element_type: props.child_type,
				  };
			element.value.addChild(template);
		}

		return {
			element,

			// Computed
			canShowDeleteButton,

			// Methods
			addChild,
		};
	},
};
</script>
