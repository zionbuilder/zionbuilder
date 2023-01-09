<template>
	<HorizontalAccordion
		ref="root"
		class="znpb-option-layout__menu"
		:title="title"
		:icon="$attrs.icon"
		:show-back-button="true"
		:show-home-button="true"
		:home-button-text="homeButtonText || 'Options'"
		:has-breadcrumbs="showBreadcrumbs"
		@expand="onAccordionExpanded"
		@collapse="onAccordionCollapsed"
	>
		<template v-if="hasHeaderSlot" #header>
			<slot name="header"></slot>
		</template>

		<template v-if="hasTitleSlot" #title>
			<slot name="title"></slot>
		</template>

		<template v-else #title>
			<Icon v-if="$attrs.icon" :icon="$attrs.icon" />
			<!-- eslint-disable-next-line vue/no-v-html -->
			<span v-html="title"></span>
			<ZionLabel v-if="label.text" :text="label.text" :type="label.type" />
			<ChangesBullet v-if="showChanges && hasChanges" @remove-styles="$emit('update:modelValue', null)" />
		</template>

		<template #actions>
			<slot name="actions" />
		</template>

		<OptionsForm
			v-if="expanded"
			v-model="optionsValue"
			class="znpb-option-layout__menu-options-form"
			:schema="child_options"
			:show-changes="showChanges"
		/>
	</HorizontalAccordion>
</template>

<script lang="ts" setup>
import { ref, computed, inject, onMounted, onBeforeUnmount, useSlots } from 'vue';

import ZionLabel from '/@/editor/common/Label.vue';

const props = withDefaults(
	defineProps<{
		child_options?: Record<string, unknown>;
		title: string;
		modelValue?: Record<string, unknown>;
		homeButtonText?: string;
		add_to_parent_breadcrumbs?: boolean;
		label: Record<string, unknown>;
	}>(),
	{
		modelValue: () => ({}),
		child_options: () => ({}),
		add_to_parent_breadcrumbs: false,
		homeButtonText: '',
		label: () => ({
			text: '',
			type: '',
		}),
	},
);

const emit = defineEmits(['update:modelValue']);
const slots = useSlots();

const root = ref(null);
const parentAccordion = inject('parentAccordion', null);
const showChanges = inject('showChanges', true);
const showBreadcrumbs = ref(props.parentAccordion === null);
const expanded = ref(false);

const valueModel = computed(() => {
	return props.modelValue;
});

const optionsValue = computed({
	get() {
		return props.modelValue;
	},
	set(newValue) {
		emit('update:modelValue', newValue);
	},
});

let breadCrumbConfig: null | {
	title: string;
	previousCallback: () => void;
} = null;

function onAccordionExpanded() {
	if (parentAccordion !== null) {
		breadCrumbConfig = {
			title: props.title,
			previousCallback: root.value.closeAccordion,
		};

		parentAccordion.addBreadcrumb(breadCrumbConfig);
	}

	expanded.value = true;
}
function onAccordionCollapsed() {
	if (parentAccordion !== null && parentAccordion) {
		parentAccordion.removeBreadcrumb(breadCrumbConfig);
	}

	expanded.value = false;
}

onMounted(() => {
	console.log(root.value);
	// this.$el.parentNode.classList.add('znpb-option-layout__menu-container');
});

onBeforeUnmount(() => {
	if (breadCrumbConfig) {
		parentAccordion.removeBreadcrumb(breadCrumbConfig);
	}
});

const hasHeaderSlot = computed(() => !!slots.header);
const hasTitleSlot = computed(() => !!slots.title);
const InputWrapper = inject('inputWrapper');
const hasChanges = computed(() => {
	return InputWrapper.hasChanges.value;
});
</script>
<style lang="scss">
.znpb-options-form-wrapper .znpb-input-type--accordion_menu {
	margin-bottom: 0;
}

.znpb-options-form-wrapper .znpb-option-layout__menu {
	padding: 0;
}

.znpb-option-layout__menu {
	padding: 0 5px;

	& > .znpb-horizontal-accordion__content > .znpb-horizontal-accordion-wrapper > &-options-form {
		overflow: visible;
		padding-top: 0;
	}

	& > .znpb-horizontal-accordion__header > {
		.znpb-horizontal-accordion__title {
			display: flex;
			align-items: center;
		}
	}

	&-has-changes {
		display: flex;
		align-items: center;
		margin-left: 15px;
	}

	&-container {
		overflow-x: hidden;
	}
}
.znpb-option-layout__menu > .znpb-horizontal-accordion__header {
	& > .znpb-horizontal-accordion__title {
		color: var(--zb-surface-text-active-color);
		font-size: 13px;
		font-weight: 500;
		text-transform: capitalize;
	}
	&:hover {
		.znpb-horizontal-accordion__title {
			& .znpb-editor-icon {
				path {
					fill: var(--zb-surface-icon-color);
				}
				circle {
					fill: var(--zb-surface-icon-color);
				}
			}
		}
	}
}
.znpb-accordion-expanded {
	overflow: hidden !important;
}
</style>
