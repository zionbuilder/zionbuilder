<template>
	<div ref="root" class="znpb-horizontal-accordion">
		<transition name="slide-title">
			<div
				v-show="!localCollapsed"
				class="znpb-horizontal-accordion__header"
				:class="{ 'znpb-horizontal-accordion__header--has-slots': hasHeaderSlot }"
				@click="openAccordion"
			>
				<template v-if="!hasHeaderSlot">
					<span class="znpb-horizontal-accordion__title">
						<template v-if="!hasTitleSlot">
							<Icon v-if="icon" :icon="icon" />
							<span> {{ title }}</span>
						</template>
						<slot name="title"></slot>
					</span>
				</template>
				<!-- @slot Content that will be placed inside the header -->
				<slot name="header"></slot>

				<!-- @slot Actions that will be placed inside the header -->
				<div class="znpb-horizontal-accordion__header-actions">
					<slot name="actions"></slot>
					<Icon v-if="showTriggerArrow" icon="right-arrow" />
				</div>
			</div>
		</transition>
		<transition name="slide-body">
			<div v-if="localCollapsed" class="znpb-horizontal-accordion__content">
				<OptionBreadcrumbs
					v-if="hasBreadcrumbs && !(combineBreadcrumbs && parentAccordion)"
					:show-back-button="showBackButton"
					:breadcrumbs="breadcrumbs"
				/>

				<!-- end bred -->
				<div class="znpb-horizontal-accordion-wrapper znpb-fancy-scrollbar" :style="wrapperStyles">
					<!-- @slot Content that will be placed inside the content -->
					<slot></slot>
				</div>
			</div>
		</transition>
	</div>
</template>

<script lang="ts">
export default {
	name: 'HorizontalAccordion',
};
</script>

<script lang="ts" setup>
import { ref, computed, useSlots, watch, provide, inject, CSSProperties } from 'vue';
import { Icon } from '../Icon';
import OptionBreadcrumbs from './OptionBreadcrumbs.vue';

type Breadcrumb = { title: string; previousCallback: () => void };

const props = withDefaults(
	defineProps<{
		hasBreadcrumbs?: boolean;
		collapsed?: boolean;
		title?: string;
		icon?: string;
		/**
		 * Level Name for breadcrumbs only if no title
		 */
		level?: string;
		showTriggerArrow?: boolean;
		showBackButton?: boolean;
		showHomeButton?: boolean;
		/**
		 * Breadcrumbs home text
		 */
		homeButtonText?: string;
		combineBreadcrumbs?: boolean;
	}>(),
	{
		collapsed: false,
		hasBreadcrumbs: true,
		showTriggerArrow: true,
	},
);

const emit = defineEmits(['collapse', 'expand']);

const parentAccordion = inject('parentAccordion', null);

provide(
	'parentAccordion',
	parentAccordion || {
		addBreadcrumb,
		removeBreadcrumb,
	},
);

defineExpose({
	closeAccordion,
});

const root = ref<HTMLDivElement | null>(null);

const localCollapsed = ref(props.collapsed);
const breadcrumbs = ref<{ title?: string; callback?: () => void }[]>([
	{
		title: props.homeButtonText,
		callback: closeAccordion,
	},
	{
		title: props.title,
	},
]);
const breadCrumbConfig = ref<Breadcrumb | null>(null);
const firstChildOpen = ref(false);

const slots = useSlots();

const hasHeaderSlot = computed(() => !!slots.header);
const hasTitleSlot = computed(() => !!slots.title);

watch(
	() => props.collapsed,
	(newValue: boolean) => {
		localCollapsed.value = newValue;
	},
);

/**
 * Returns the position of the pointer
 */
const wrapperStyles = computed(() => {
	const cssStyles: CSSProperties = {};
	if (!props.combineBreadcrumbs && parentAccordion === null && localCollapsed.value && firstChildOpen.value) {
		cssStyles['overflow'] = 'hidden';
	}
	return cssStyles;
});

function addBreadcrumb(breadcrumb: { title: string; previousCallback: () => void }) {
	// Check to see if we need to add a callback to previous item
	if (typeof breadcrumb.previousCallback === 'function') {
		const lastItem = breadcrumbs.value[breadcrumbs.value.length - 1];

		lastItem.callback = breadcrumb.previousCallback;
	}
	breadcrumbs.value.push(breadcrumb);
	firstChildOpen.value = true;
}

function removeBreadcrumb(breadcrumb: Breadcrumb) {
	const breadCrumbIndex = breadcrumbs.value.indexOf(breadcrumb);
	if (breadCrumbIndex !== -1) {
		breadcrumbs.value.splice(breadCrumbIndex, 1);
		firstChildOpen.value = false;
	}
}

function closeAccordion() {
	localCollapsed.value = false;

	if (parentAccordion && breadCrumbConfig.value) {
		removeBreadcrumb(breadCrumbConfig.value);
	}

	emit('collapse', true);
}

function openAccordion() {
	localCollapsed.value = true;

	root.value?.closest('.znpb-horizontal-accordion-wrapper')?.scrollTo(0, 0);

	// Inject to existing breadcrumbs
	if (props.combineBreadcrumbs && parentAccordion) {
		injectBreadcrumbs();
	}

	emit('expand', false);
}

function injectBreadcrumbs() {
	breadCrumbConfig.value = {
		title: props.title || '',
		previousCallback: closeAccordion,
	};

	addBreadcrumb(breadCrumbConfig.value);
}
</script>

<style lang="scss">
.znpb-horizontal-accordion {
	width: 100%;

	&__header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 16px;
		margin-bottom: 5px;
		font-size: 13px;
		font-weight: 500;
		background: var(--zb-surface-lighter-color);
		border-radius: 3px;
		cursor: pointer;

		&:hover {
			background: var(--zb-surface-lightest-color);
		}

		.znpb-accordion-title {
			margin-right: 15px;
		}
		&--has-slots {
			padding: 0;
		}

		&-actions {
			display: flex;
			align-items: center;

			& > * {
				margin-right: 10px;

				&:last-child {
					margin-right: 0;
				}
			}
		}
	}

	&__title {
		flex-grow: 1;
		padding-right: 15px;
		color: var(--zb-surface-text-active-color);
		font-size: 13px;
		transition: color 0.15s ease-in-out;

		& > .znpb-editor-icon-wrapper {
			margin-right: 15px;
			color: var(--zb-surface-icon-color);
			font-size: 22px;
		}
	}

	&__content {
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		z-index: 1;
		display: flex;
		flex-direction: column;
		height: 100%;
		background: var(--zb-surface-color);
	}
	&-wrapper {
		position: relative;
		overflow-x: hidden;
		height: 100%;

		.znpb-horizontal-accordion-wrapper {
			padding: 0;
		}
	}
}

.slide-title-enter-from {
	transform: translateX(-100%);
}
.slide-title-enter-to {
	transform: translateX(0);
}
.slide-title-leave-from {
	transform: translateX(-100%);
}

.slide-title-enter-to,
.slide-title-leave-from {
	transition: all 0.2s;
}

.slide-body-enter-from {
	transform: translateX(100%);
}
.slide-body-enter-to {
	transform: translateX(0%);
}
.slide-body-leave-from {
	transform: translateX(50%);
}
.slide-body-leave-to {
	transform: translateX(100%);
}
.slide-body-enter-to,
.slide-body-leave-from {
	transition: all 0.2s;
}
</style>
