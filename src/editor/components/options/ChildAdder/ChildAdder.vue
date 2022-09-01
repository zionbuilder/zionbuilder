<template>
	<div class="znpb-options-children__wrapper">
		<Sortable v-model="element.content" class="znpb-options-children__items-wrapper">
			<SingleChild
				v-for="childElement in elementChildren"
				:key="childElement.uid"
				:element="childElement"
				:item-option-name="item_name"
				:show-delete="canShowDeleteButton"
				@delete="childElement.delete"
				@clone="childElement.duplicate"
			/>
		</Sortable>

		<Button class="znpb-option-repeater__add-button" type="line" @click="addChild"> ADD </Button>
	</div>
</template>

<script lang="ts">
export default {
	name: 'ChildAdder',
};
</script>

<script lang="ts" setup>
import { computed } from 'vue';
import { useElementProvide } from '../../../composables';
import SingleChild from './SingleChild.vue';
import { useContentStore } from '/@/editor/store';

const props = defineProps<{
	modelValue?: string;
	// eslint-disable-next-line vue/prop-name-casing
	child_type: string;
	// eslint-disable-next-line vue/prop-name-casing
	item_name?: string;
	min?: number;
	// eslint-disable-next-line vue/prop-name-casing
	add_template?: Record<string, unknown>;
}>();

const { injectElement } = useElementProvide();
const element = injectElement();
const contentStore = useContentStore();

const canShowDeleteButton = computed(() => {
	if (props.min && element.content.length === props.min) {
		return false;
	}

	return true;
});

const elementChildren = computed(() => {
	return element.content.map(elementUID => {
		return contentStore.getElement(elementUID);
	});
});

// Check to see if we need to add some tabs
if (element.content.length === 0 && props.modelValue) {
	element.addChildren(props.modelValue);
}

function addChild() {
	const template = props.add_template
		? props.add_template
		: {
				element_type: props.child_type,
		  };
	element.addChild(template);
}
</script>
