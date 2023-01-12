<template>
	<SelectorAndPseudo
		v-model="computedModelValue"
		v-model:activeClass="activeClass"
		:title="activeSelectorTitle"
		:selector="activeSelector"
		:allow_class_assignments="allowClassAssignment"
	/>
</template>

<script lang="ts" setup>
import { ref, watchEffect, inject } from 'vue';
import SelectorAndPseudo from './options/ElementStyles/SelectorAndPseudo.vue';

const props = defineProps<{
	// eslint-disable-next-line vue/prop-name-casing
	editedElement: ZionElement;
}>();

// Pseudo and css classes
// Provide an API that can be used by the ElementStyles component
const activeClass = ref('');
const activeSelector = ref('');
const activeSelectorTitle = ref('');
const allowClassAssignment = ref(true);

// Set the main active class
watchEffect(() => {
	const elementHtmlID = props.editedElement.elementCssId;
	const styledElements = props.editedElement.elementDefinition.style_elements;
	activeClass.value = `#${elementHtmlID}`;
	activeSelectorTitle.value = styledElements.wrapper.title;
	activeSelector.value = `#${elementHtmlID}`;
});

function registerActiveClass(cssClass: string) {
	activeClass.value = cssClass;
}

function registerActiveClassTitle(title: string) {
	console.log(title);
	activeSelectorTitle.value = title;
}

inject('ElementOptionsPanelAPI', {
	activeClass,
	registerActiveClass,
	registerActiveClassTitle,
});
</script>

<style lang="scss">
.znpb-panel__content_wrapper .znpb-element-styles__media-wrapper {
	flex-grow: 0;
	margin: 20px;
	flex-direction: column;
}
</style>
