<template>
	<input
		ref="root"
		v-model="computedModelValue"
		:readonly="!isEnabled"
		class="znpb-inlineEditInput"
		:class="{ 'znpb-inlineEditInput--readonly': !isEnabled }"
		@dblclick="isEnabled = true"
		@keydown.escape.stop="disableEdit"
	/>
</template>

<script lang="ts">
export default {
	name: 'InlineEdit',
};
</script>

<script lang="ts" setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue';

const props = withDefaults(
	defineProps<{
		modelValue?: string;
		enabled?: boolean;
		tag?: string;
	}>(),
	{
		modelValue: '',
		enabled: false,
		tag: 'div',
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: string): void;
}>();

const root = ref(null);
const isEnabled = ref(props.enabled);

const computedModelValue = computed({
	get() {
		return props.modelValue;
	},
	set(newValue) {
		emit('update:modelValue', newValue);
	},
});

watch(isEnabled, newValue => {
	if (newValue) {
		document.addEventListener('click', disableOnOutsideClick, true);
	} else {
		document.removeEventListener('click', disableOnOutsideClick, true);
	}
});

onBeforeUnmount(() => {
	document.removeEventListener('click', disableOnOutsideClick, true);
});

function disableOnOutsideClick(event: MouseEvent) {
	if (event.target !== root.value) {
		disableEdit();
	}
}

function disableEdit() {
	isEnabled.value = false;
	window.getSelection()?.removeAllRanges();
}
</script>
<style lang="scss">
.znpb-inlineEditInput {
	border: 0;
	background: transparent;
	color: inherit;
	font-size: inherit;
	font-family: inherit;
}

.znpb-inlineEditInput--readonly {
	cursor: pointer;
}
</style>
