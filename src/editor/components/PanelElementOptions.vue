<template>
	<BasePanel
		v-if="UIStore.editedElement"
		class="znpb-element-options__panel-wrapper"
		:class="{
			'znpb-element-options__panel-wrapper--hidden': isPanelHidden,
		}"
		:panel-id="panel.id"
		:show-expand="false"
		:allow-horizontal-resize="!isPanelHidden"
		:allow-vertical-resize="!isPanelHidden"
		:panel="panel"
		:style="panelStyles"
		@close-panel="closeOptionsPanel"
	>
		<template #before-header>
			<div class="znpb-element-options__hide" @click.stop="isPanelHidden = !isPanelHidden">
				<Icon
					icon="select"
					class="znpb-element-options__hideIcon"
					:class="{
						'znpb-element-options__hide--hidden': isPanelHidden,
					}"
				/>
			</div>
		</template>

		<template #header>
			<div class="znpb-element-options__header">
				<!-- Show back button for child elements -->
				<div
					v-if="UIStore.editedElement?.elementDefinition.is_child"
					class="znpb-element-options__header-back"
					@click="onBackButtonClick"
				>
					<Icon class="znpb-element-options__header-back-icon" icon="select" />
				</div>

				<h4
					class="znpb-panel__header-name"
					@click="onBackButtonClick"
					@mouseenter="showBreadcrumbs = true"
					@mouseleave="showBreadcrumbs = false"
				>
					{{ `${contentStore.getElementName(UIStore.editedElement)} ${translate('options')}` }}
					<Icon icon="select" />
					<BreadcrumbsWrapper v-if="showBreadcrumbs" :element="UIStore.editedElement" />
				</h4>
			</div>
		</template>

		<div class="znpb-element-options-content-wrapper">
			<Tabs
				v-model:activeTab="activeKeyTab"
				:has-scroll="['general', 'advanced']"
				class="znpb-element-options__tabs-wrapper"
			>
				<Tab name="General">
					<OptionsForm
						v-if="
							UIStore.editedElement.elementDefinition.options &&
							Object.keys(UIStore.editedElement.elementDefinition.options).length > 0
						"
						v-model="elementOptions"
						class="znpb-element-options-content-form znpb-fancy-scrollbar"
						:schema="UIStore.editedElement.elementDefinition.options"
						:replacements="optionsReplacements"
					/>

					<p v-else class="znpb-element-options-no-option-message">
						{{ $translate('element_has_no_specific_options') }}
					</p>
				</Tab>
				<Tab name="Styling">
					<OptionsForm
						v-model="computedStyleOptions"
						class="znpb-fancy-scrollbar"
						:schema="computedStyleOptionsSchema"
						:replacements="optionsReplacements"
					/>
				</Tab>
				<Tab name="Advanced">
					<OptionsForm
						v-model="advancedOptionsModel"
						class="znpb-element-options-content-form znpb-fancy-scrollbar"
						:schema="getSchema('element_advanced')"
						:replacements="optionsReplacements"
					/>
				</Tab>
				<Tab name="Search">
					<template #title>
						<div class="znpb-element-options__search-tab-title" @click.stop="toggleSearchIcon">
							<Icon :icon="searchIcon" />
						</div>

						<BaseInput
							v-if="searchActive"
							ref="searchInput"
							v-model="optionsFilterKeyword"
							:filterable="true"
							placeholder="Search option"
							class="znpb-tabs__header-item-search-options"
						>
						</BaseInput>
					</template>

					<p
						v-if="optionsFilterKeyword.length > 2 && Object.keys(filteredOptions).length === 0"
						class="znpb-element-options-default-message"
					>
						{{ translate('no_options_found') }}
					</p>
					<p v-if="optionsFilterKeyword.length < 3" class="znpb-element-options-no-option-message">
						{{ defaultMessage }}
					</p>

					<OptionsForm
						v-model="elementOptions"
						class="znpb-element-options-content-form znpb-fancy-scrollbar"
						:schema="filteredOptions"
					/>
				</Tab>
			</Tabs>
		</div>
		<div class="znpb-element-options-action">
			<div
				class="znpb-element-options-action__undo"
				:class="{ 'znpb-element-options-action__undo--disabled': !canUndo }"
				@click="undo"
			>
				<Icon icon="undo" />
			</div>
			<div
				class="znpb-element-options-action__redo"
				:class="{ 'znpb-element-options-action__redo--disabled': !canRedo }"
				@click="redo"
			>
				<Icon icon="redo" />
			</div>
		</div>
	</BasePanel>
</template>

<script lang="ts" setup>
import { ref, Ref, watch, provide, computed, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { addAction, removeAction } from '/@/common/modules/hooks';
import { translate } from '/@/common/modules/i18n';
import { isEditable, Environment } from '/@/common/utils';
import { useElementProvide, useWindows, useHistory } from '../composables';
import { usePseudoSelectors, useOptionsSchemas } from '/@/common/composables';
import { debounce } from 'lodash-es';
import { useUIStore, useContentStore, useElementDefinitionsStore } from '../store';

// Components
import BreadcrumbsWrapper from './elementOptions/BreadcrumbsWrapper.vue';
import BasePanel from './BasePanel.vue';

const props = defineProps<{
	panel: ZionPanel;
}>();

const UIStore = useUIStore();
const contentStore = useContentStore();
const isPanelHidden = ref(false);
const searchInput: Ref<HTMLElement | null> = ref(null);
const showBreadcrumbs = ref(false);
const lastTab = ref(null);
const defaultMessage = ref(translate('element_options_default_message'));
let ignoreLocalHistory = false;
const { setActivePseudoSelector } = usePseudoSelectors();

const { provideElement } = useElementProvide();
const { getSchema } = useOptionsSchemas();
const activeKeyTab: Ref<string | null> = ref(null);
const searchActive = ref(false);
const history = ref([]);
const historyIndex = ref(0);

const optionsFilterKeyword = ref('');

const panelStyles = computed(() => {
	return {
		'--optionsPanelWidth': `-${props.panel.width}px`,
	};
});

const elementOptions = computed({
	get() {
		return UIStore.editedElement ? UIStore.editedElement.options : {};
	},
	set(newValues) {
		contentStore.updateElement(UIStore.editedElement?.uid, 'options', newValues);
	},
});

watch(
	elementOptions,
	() => {
		// Add to history
		if (!ignoreLocalHistory) {
			addToLocalHistory();
			ignoreLocalHistory = false;
		}
	},
	{ deep: true },
);

const advancedOptionsModel = computed({
	get() {
		return elementOptions.value._advanced_options || {};
	},
	set(newValues) {
		if (newValues === null) {
			const oldValues = { ...elementOptions.value };
			delete oldValues._advanced_options;

			elementOptions.value = oldValues;
		} else {
			elementOptions.value = {
				...elementOptions.value,
				_advanced_options: newValues,
			};
		}
	},
});

const searchIcon = computed(() => {
	return searchActive.value ? 'close' : 'search';
});

const hasChanges = computed(() => history.value.length > 1 && historyIndex.value > 0);

const addToLocalHistory = debounce(function () {
	// Clone store
	const clonedValues = JSON.parse(JSON.stringify(elementOptions.value));

	if (historyIndex.value + 1 < history.value.length) {
		history.value.splice(historyIndex.value + 1);
	}

	history.value.push(clonedValues);
	historyIndex.value++;
}, 500);

const canUndo = computed(() => {
	return history.value[historyIndex.value - 1];
});

const canRedo = computed(() => {
	return history.value[historyIndex.value + 1];
});

function undo() {
	const prevState = history.value[historyIndex.value - 1];

	if (prevState) {
		ignoreLocalHistory = true;
		elementOptions.value = prevState;
		historyIndex.value--;
	}
}

function redo() {
	const nextState = history.value[historyIndex.value + 1];

	if (nextState) {
		ignoreLocalHistory = true;
		elementOptions.value = nextState;
		historyIndex.value++;
	}
}

// Set initial history
history.value.push(JSON.parse(JSON.stringify(elementOptions.value)));

// Change the tab when a new element is selected
// watch(UIStore.editedElement, newValue => {
// 	activeKeyTab.value = 'general';
// 	searchActive.value = false;
// 	optionsFilterKeyword.value = '';

// 	// Clear selected pseudo selector
// 	setActivePseudoSelector(null);
// });

provideElement(UIStore.editedElement);
provide('elementInfo', UIStore.editedElement);
provide('OptionsFormTopModelValue', elementOptions);

const computedStyleOptionsSchema = computed(() => {
	const schema = {};
	let styledElements = UIStore.editedElement.elementDefinition.style_elements;
	const elementHTMLID = UIStore.editedElement.elementCssId;
	Object.keys(styledElements).forEach(styleId => {
		const config = styledElements[styleId];

		schema[styleId] = {
			type: 'css_selector',
			name: config.title,
			icon: 'brush',
			allow_class_assignments:
				typeof config.allow_class_assignments !== 'undefined' ? config.allow_class_assignments : true,
			selector: config.selector.replace('{{ELEMENT}}', `#${elementHTMLID}`),
			allow_delete: false,
			show_breadcrumbs: true,
			allow_custom_attributes:
				typeof config.allow_custom_attributes === 'undefined' || config.allow_custom_attributes === true,
			allowRename: false,
		};
	});

	return {
		_styles: {
			id: 'styles',
			child_options: schema,
			optionsLayout: 'full',
			type: 'group',
		},
	};
});

const allOptionsSchema = computed(() => {
	const elementOptionsSchema = UIStore.editedElement?.elementDefinition.options
		? UIStore.editedElement.elementDefinition.options
		: {};
	const optionsSchema = {
		...elementOptionsSchema,
		...computedStyleOptionsSchema.value,
		...getSchema('element_advanced'),
	};

	return optionsSchema;
});

const computedStyleOptions = computed({
	get() {
		return elementOptions.value || {};
	},
	set(newValue) {
		if (newValue === null) {
			const oldValues = { ...elementOptions.value };
			delete oldValues._styles;

			elementOptions.value = oldValues;
		} else {
			elementOptions.value = newValue;
		}
	},
});

const filteredOptions = computed(() => {
	const keyword = optionsFilterKeyword.value;
	if (keyword.length > 2) {
		return filterOptions(keyword, allOptionsSchema.value);
	}

	return {};
});

// Watchers
watch(
	() => UIStore.editedElement,
	(newValue, oldValue) => {
		if (newValue && newValue !== oldValue) {
			addToGlobalHistory(oldValue);
		}
	},
);

watch(searchActive, newValue => {
	if (newValue) {
		nextTick(() => {
			if (searchInput.value) {
				searchInput.value.focus();
			}
		});
	}
});

addAction('change-tab-styling', changeTabByEvent());

// Lifecycle
onMounted(() => {
	const { addEventListener } = useWindows();
	addEventListener('keydown', onKeyPress, true);
});

onBeforeUnmount(() => {
	const { removeEventListener } = useWindows();
	removeEventListener('keydown', onKeyPress, true);

	// remove events
	removeAction('change-tab-styling', changeTab());
});

const optionsReplacements = [
	{
		search: /%%ELEMENT_TYPE%%/g,
		replacement: () => {
			const elementsDefinitionsStore = useElementDefinitionsStore();
			return elementsDefinitionsStore.getElementDefinition(UIStore.editedElement?.element_type).name;
		},
	},
	{
		search: /%%ELEMENT_UID%%/g,
		replacement: () => {
			return UIStore.editedElement.elementCssId;
		},
	},
];

// Methods
function onBackButtonClick() {
	if (UIStore.editElement.parent && UIStore.editElement.parent.elementDefinition.element_type !== 'contentRoot') {
		const parentElement = contentStore.getElement(UIStore.editElement.parent);
		UIStore.editElement(parentElement);
	}
}
function changeTabByEvent(event) {
	if (event !== undefined) {
		if (tabId !== 'search') {
			lastTab.value = activeKeyTab.value;
			optionsFilterKeyword.value = '';
		}
		activeKeyTab.value.value = event.detail;
	}
}
function filterOptions(keyword, optionsSchema, currentId, currentName) {
	let lowercaseKeyword = keyword.toLowerCase();
	let foundOptions = {};

	Object.keys(optionsSchema).forEach(optionId => {
		const optionConfig = optionsSchema[optionId];

		let syncValue = [];
		let syncValueName = [];

		if (!optionConfig.sync) {
			if (currentId) {
				syncValue.push(...currentId);
			}

			if (currentName) {
				let name = getInnerStyleName(currentName[currentName.length - 1]);
				currentName[currentName.length - 1] = name;
				syncValueName.push(...currentName);
			}

			if (optionId === 'animation-group' || optionId === 'custom-css-group' || optionId === 'general-group') {
				syncValueName.push(translate('advanced'));
			}

			if (!optionConfig.is_layout) {
				syncValue.push(optionId);
			}

			if (optionConfig.type === 'element_styles' || optionConfig.type === 'css_selector') {
				syncValue.push('styles');
				syncValueName.push(translate('styles'), optionConfig.name);
			}

			if (optionConfig.type === 'responsive_group') {
				syncValue.push('%%RESPONSIVE_DEVICE%%');
			}

			if (optionConfig.type === 'pseudo_group') {
				syncValue.push('%%PSEUDO_SELECTOR%%');
			}

			syncValueName.push(optionId);
		}

		// Search in areas
		let searchOptions = optionConfig.search_tags ? [...optionConfig.search_tags] : [];
		if (optionConfig.title) {
			searchOptions.push(optionConfig.title);
		}
		if (optionConfig.id) {
			searchOptions.push(optionConfig.id);
		}
		if (optionConfig.description) {
			searchOptions.push(optionConfig.description);
		}
		if (optionConfig.label) {
			searchOptions.push(optionConfig.label);
		}

		if (optionConfig.type !== 'accordion_menu' && optionConfig.type !== 'element_styles') {
			if (searchOptions.join(' ').toLowerCase().indexOf(lowercaseKeyword) !== -1) {
				let filteredBreadcrumbs = [];
				if (currentName) {
					filteredBreadcrumbs = currentName.filter(function (value) {
						return value !== undefined;
					});
				}
				foundOptions[syncValue.join('.')] = {
					...optionConfig,
					id: syncValue.join('.'),
					sync: optionConfig.sync || syncValue.join('.'),
					breadcrumbs: filteredBreadcrumbs,
				};
			}
		}

		if (optionConfig.type === 'repeater') {
			return;
		}

		if (optionConfig.type === 'element_styles' || optionConfig.type === 'css_selector') {
			const childOptions = filterOptions(keyword, getSchema('element_styles'), syncValue, syncValueName);

			foundOptions = {
				...foundOptions,
				...childOptions,
			};
		}

		if (optionConfig.child_options && Object.keys(optionConfig.child_options).length > 0) {
			const childOptions = filterOptions(keyword, optionConfig.child_options, syncValue, syncValueName);

			foundOptions = {
				...foundOptions,
				...childOptions,
			};
		}
	});

	return foundOptions;
}
function getInnerStyleName(id) {
	if (id === 'pseudo_selectors') {
		return undefined;
	}

	return computedStyleOptionsSchema.value._styles.child_options[id] !== undefined
		? computedStyleOptionsSchema.value._styles.child_options[id].title
		: allOptionsSchema.value[id] !== undefined
		? allOptionsSchema.value[id].title
		: undefined;
}
function toggleSearchIcon() {
	searchActive.value = !searchActive.value;
	if (!searchActive.value) {
		changeTab('general');
	} else {
		changeTab('search');
	}
	optionsFilterKeyword.value = '';
}

function changeTab(tabId) {
	activeKeyTab.value = tabId;

	if (tabId !== 'search') {
		lastTab.value = activeKeyTab.value;
		optionsFilterKeyword.value = '';
	}
}

function addToGlobalHistory(element) {
	if (hasChanges.value) {
		const activeEl = element ? element : UIStore.editElement;
		const { addToHistory } = useHistory();
		const elementSavedName = activeEl.name;
		addToHistory(`Edited ${elementSavedName}`);
	}
}
function closeOptionsPanel() {
	addToGlobalHistory();
	UIStore.closePanel(props.panel.id);
	UIStore.unEditElement();
}

function onKeyPress(e) {
	const controlKey = Environment.isMac ? 'metaKey' : 'ctrlKey';

	if (isEditable()) {
		return;
	}

	// Undo CTRL+Z
	if (e.which === 90 && e[controlKey] && !e.shiftKey && canUndo.value) {
		undo();
		e.preventDefault();
		e.stopPropagation();
	}
	// Redo CTRL+SHIFT+Z CTRL + Y
	if ((e.which === 90 && e[controlKey] && e.shiftKey) || (e[controlKey] && e.which === 89)) {
		if (canRedo.value) {
			redo();
			e.preventDefault();
			e.stopPropagation();
		}
	}
}
</script>

<style lang="scss">
@keyframes searchFade {
	0% {
		opacity: 0;
	}
	100% {
		opacity: 1;
	}
}

.znpb-tabs__header-item--search {
	flex-grow: 1;
}
.znpb-element-options__search-tab {
	padding: 0;
}
.znpb-element-options {
	&__panel-wrapper .znpb-panel__content_wrapper {
		padding-top: 0;
	}

	&__panel-wrapper--hidden .znpb-panel__content_wrapper {
		z-index: 0;
	}

	&__header {
		position: relative;
		display: flex;
		align-items: center;

		&-back {
			display: flex;
			align-items: center;
			padding-left: 20px;
			cursor: pointer;

			& ~ .znpb-panel__header-name {
				padding-left: 0;
			}

			&-icon {
				width: 22px;
				height: 22px;
				margin-right: 10px;
				color: var(--zb-surface-icon-color);
				font-size: 14px;
				background-color: var(--zb-surface-lighter-color);
				border-radius: 3px;
				transition: 0.15s all;
				transition: all 0.3s;
				cursor: pointer;

				&:hover {
					color: var(--zb-surface-text-hover-color);
					background-color: var(--zb-surface-lightest-color);
				}

				svg {
					transform: rotate(90deg);
				}
			}
		}

		.znpb-panel__header-name {
			position: relative;
			display: flex;
			align-items: center;
			cursor: pointer;
		}

		.znpb-element-options__breadcrumbs {
			position: absolute;
			top: 100%;
			left: 0;
			z-index: 20000;
			min-width: 200px;
			max-height: 360px;
			padding: 16px 16px 4px;
			color: var(--zb-surface-text-color);
			font-size: 13px;
			font-weight: 400;
			background-color: var(--zb-dropdown-bg-color);
			box-shadow: var(--zb-dropdown-shadow);
			border: 1px solid var(--zb-dropdown-border-color);
			border-radius: 3px;
			transform: translate(10px, -10px);

			& > span {
				display: block;
				padding-bottom: 12px;
			}
		}
	}

	&-content-wrapper {
		position: relative;
		display: flex;
		flex-grow: 1;
		overflow: hidden;

		.znpb-element-options__tabs-wrapper {
			flex-grow: 1;
			padding-top: 20px;
			background-color: var(--zb-surface-darker-color);
		}
		.znpb-tabs {
			display: flex;
			flex-direction: column;

			.znpb-tabs__content,
			.znpb-tabs__wrapper {
				height: calc(100% - 38px);
			}

			.znpb-tab__wrapper {
				position: relative;
				height: 100%;
			}
			.znpb-tabs__content {
				display: flex;
				flex-direction: column;
			}
		}
	}

	&-content-form {
		flex-grow: 1;
		max-height: 100%;
	}

	&-action {
		display: flex;
		justify-content: space-between;
		flex-grow: 0;
		flex-shrink: 0;
		padding: 20px;

		&__undo {
			margin-right: 10px;
		}

		&__undo,
		&__redo {
			display: flex;
			justify-content: center;
			flex: 1;
			padding: 15px 37px;
			line-height: 1;
			background-color: var(--zb-surface-lighter-color);
			border-radius: 3px;
			cursor: pointer;

			&--active {
				&:hover {
					opacity: 0.9;
				}
			}
			&--disabled {
				opacity: 0.5;
				pointer-events: none;
			}
		}
	}
}

// Options filter
.znpb-tabs__header-item {
	.znpb-tabs__header-item-search-options {
		position: absolute;
		top: 20px;
		left: 0;
		width: calc(100% - 78px);
		height: 38px;
		margin-left: 20px;
		background: var(--zb-surface-lighter-color);
		border: none;
		border-radius: 0;

		input {
			height: 100%;
			padding-left: 0;
			border-radius: 0;
		}
	}
}
.znpb-element-options__tabs-wrapper {
	p.znpb-element-options-default-message,
	p.znpb-element-options-no-option-message {
		padding: 20px;
	}

	.znpb-element-options-no-option-message {
		position: absolute;
		top: 0;
		z-index: 9;
	}
}

//search tab
.znpb-tabs--card > .znpb-tabs__header > .znpb-tabs__header-item.znpb-tabs__header-item--search {
	flex: 0 1 auto;
	padding: 0;
	margin-left: auto;
	background: var(--zb-surface-color);
	border-top-right-radius: 3px;
	border-top-left-radius: 3px;

	.znpb-editor-icon-wrapper {
		display: block;
	}
}

.znpb-element-options__search-tab-title {
	padding: 10px 12px;
}

// Some style from wp common.css is adding borders and shadow
// A different solution would be to add the admin bar css from common in the builder
.znpb-input-content .widget-inside {
	box-shadow: none;
	border: none;
}

// Hide options panel
.znpb-element-options__hide {
	position: absolute;
	top: 16px;
	left: 100%;
	z-index: 0;
	display: flex;
	justify-content: center;
	align-items: center;
	width: 20px;
	height: 24px;
	color: var(--zb-surface-icon-color);
	background: var(--zb-surface-color);
	border: 1px solid var(--zb-surface-border-color);
	border-radius: 0 50% 50% 0;
	transform: translateX(-100%);
	transition: transform 0.15s 0s, z-index 0s;
	cursor: pointer;

	&Icon {
		position: relative;
		left: -2px;
		font-size: 12px;
		transform: rotate(90deg);
		transition: color 0.15s;
	}

	&:hover &Icon {
		color: var(--zb-surface-text-hover-color);
	}

	.znpb-editor-panel--detached & {
		display: none;
	}

	.znpb-editor-panel--left & {
		border-left: 0;
	}

	.znpb-editor-panel--right & {
		right: 100%;
		left: auto;
		border-right: 0;
		border-radius: 50% 0 0 50%;
		transform: translateX(100%);
	}

	.znpb-editor-panel--right &Icon {
		left: 2px;
		transform: rotate(-90deg);
	}

	.znpb-element-options__panel-wrapper--hidden & {
		left: calc(100% + 2px);
		transition: opacity 0.15s;
		opacity: 0.6;

		&:hover {
			opacity: 1;
		}
	}

	.znpb-element-options__panel-wrapper--hidden.znpb-editor-panel--right & {
		right: calc(100% + 2px);
		left: auto;
	}

	body.znpb-theme-dark .znpb-element-options__panel-wrapper--hidden.znpb-editor-panel--right & {
		right: calc(100% + 1px);
		left: auto;
	}

	body.znpb-theme-dark .znpb-element-options__panel-wrapper--hidden & {
		left: calc(100% + 1px);
	}

	.znpb-element-options__panel-wrapper:hover &,
	.znpb-element-options__panel-wrapper--hidden & {
		z-index: 10;
		transform: translateX(0);
		transition: transform 0.15s 0s, z-index 0.15s;
	}
}

// Hide icon
.znpb-element-options__panel-wrapper--hidden .znpb-element-options__hideIcon {
	left: -1px;
	transform: rotate(270deg);
}

.znpb-editor-panel--right.znpb-element-options__panel-wrapper--hidden .znpb-element-options__hideIcon {
	left: 1px;
	transform: rotate(-270deg);
}

.znpb-element-options__panel-wrapper {
	transition: margin-left 0.15s;
}

.znpb-element-options__panel-wrapper--hidden {
	margin-left: var(--optionsPanelWidth, -360px);
}

.znpb-editor-panel--right.znpb-element-options__panel-wrapper {
	transition: margin-right 0.15s;
}

.znpb-editor-panel--right.znpb-element-options__panel-wrapper--hidden {
	margin-right: var(--optionsPanelWidth, -360px);
	margin-left: 0;
}
</style>
