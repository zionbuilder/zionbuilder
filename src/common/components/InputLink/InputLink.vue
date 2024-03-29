<template>
	<div ref="rootRef" class="znpb-link-wrapper">
		<InputWrapper layout="full">
			<Tooltip
				v-model:show="showResults"
				placement="bottom"
				append-to="element"
				strategy="fixed"
				:show-arrows="false"
				:trigger="null"
				:close-on-outside-click="true"
				tooltip-class="hg-popper--no-padding"
				class="znpb-optionLinkTooltip"
			>
				<template #content>
					<Loader v-if="isSearchLoading" :size="14" />

					<ul v-else class="znpb-menuList znpb-mh-200 znpb-fancy-scrollbar">
						<li
							v-for="(post, index) in searchResults"
							:key="index"
							class="znpb-menuListItem"
							@click="onSearchItemClick(post.url)"
						>
							{{ post.post_title }}
						</li>
					</ul>
				</template>

				<component
					:is="linkURLComponent"
					ref="urlInput"
					v-model="linkModel"
					:placeholder="i18n.__('Type to search or enter URL', 'zionbuilder')"
					spellcheck="false"
				>
					<template #prepend>
						<Loader v-if="isSearchLoading" :size="14" />
						<Icon v-else icon="link"></Icon>
					</template>

					<template #append>
						<Tooltip
							trigger="click"
							:close-on-outside-click="true"
							tooltip-class="znpb-link-optionsTooltip"
							placement="bottom"
							class="znpb-flex znpb-flex--vcenter"
						>
							<template #content>
								<div class="znpb-link-options">
									<div class="znpb-link-options-title">{{ i18n.__('Link attributes', 'zionbuilder') }}</div>
									<div class="znpb-link-optionsAttributes">
										<LinkAttributeForm
											v-for="(attribute, index) in linkAttributes"
											:key="index"
											:attribute-config="attribute"
											@update-attribute="onAttributeUpdate(index, $event)"
											@delete="deleteAttribute(index)"
										/>

										<div class="znpb-link-optionsAttributesAdd" @click="addLinkAttribute">
											<Icon icon="plus" /> <span>{{ i18n.__('Add custom link attribute', 'zionbuilder') }}</span>
										</div>
									</div>
								</div>
							</template>

							<Icon v-znpb-tooltip="i18n.__('Edit link attributes', 'zionbuilder')" icon="tags-attributes"></Icon>
						</Tooltip>

						<!-- Injection point -->
						<Injection location="options/link/append" />
					</template>
				</component>
			</Tooltip>
		</InputWrapper>

		<OptionsForm
			v-model="computedModel"
			class="znpb-link--optionsForm"
			:schema="targetTitleSchema"
			:enable-dynamic-data="true"
			:no-space="true"
		/>
	</div>
</template>

<script lang="ts" setup>
// External imports
import * as i18n from '@wordpress/i18n';
import { computed, ref, watchEffect, watch, Ref } from 'vue';
import { get, debounce } from 'lodash-es';

import { BaseInput } from '../BaseInput';
import LinkAttributeForm from './LinkAttributeForm.vue';

// Common API
const { applyFilters } = window.zb.hooks;

interface Post {
	post_title: string;
	url: string;
}

interface Attribute {
	name: string;
	value: string;
}

const props = withDefaults(
	defineProps<{
		modelValue?: {
			link?: string;
			target?: string;
			title?: string;
			attributes?: Record<string, string>;
		};
		title?: string;
		// eslint-disable-next-line vue/prop-name-casing
		show_title?: boolean;
		// eslint-disable-next-line vue/prop-name-casing
		show_target?: boolean;
	}>(),
	{
		show_title: true,
		show_target: true,
		title: '',
		modelValue: () => {
			return {};
		},
	},
);

const emit = defineEmits(['update:modelValue']);

const targetOptions = [
	{
		id: '_blank',
		name: i18n.__('New Window', 'zionbuilder'),
	},
	{
		id: '_self',
		name: i18n.__('Same Window', 'zionbuilder'),
	},
];

const rootRef = ref(null);
const urlInput = ref(null);
const canShowSearchTooltip = ref(false);
const popperRef = ref(false);
const isSearchLoading = ref(false);
const showResults = ref(false);
const searchResults: Ref<Post[]> = ref([]);

const linkURLComponent = computed(() => {
	return applyFilters('zionbuilder/options/link/url_component', 'BaseInput', props.modelValue);
});

const computedModel = computed({
	get() {
		return props.modelValue;
	},
	set(newValue) {
		emit('update:modelValue', newValue);
	},
});

const targetTitleSchema = {
	target: {
		type: 'select',
		label: i18n.__('Target', 'zionbuilder'),
		options: targetOptions,
		width: 50,
		default: '_self',
		id: 'target',
	},
	title: {
		type: 'text',
		label: i18n.__('Title', 'zionbuilder'),
		width: 50,
		id: 'title',
	},
};

const linkModel = computed({
	get() {
		return props.modelValue && props.modelValue.link ? props.modelValue.link : '';
	},
	set(newValue) {
		emit('update:modelValue', {
			...props.modelValue,
			link: newValue,
		});
	},
});

const targetModel = computed({
	get() {
		return props.modelValue && props.modelValue['target'] ? props.modelValue['target'] : '_self';
	},
	set(newValue) {
		emit('update:modelValue', {
			...props.modelValue,
			target: newValue,
		});
	},
});

const titleModel = computed({
	get() {
		return props.modelValue && props.modelValue['title'] ? props.modelValue['title'] : '';
	},
	set(newValue) {
		emit('update:modelValue', {
			...props.modelValue,
			title: newValue,
		});
	},
});

const linkAttributes = computed({
	get() {
		const attributes = get(props.modelValue, 'attributes');
		if (Array.isArray(attributes) && attributes.length > 0) {
			return attributes;
		} else {
			return [
				{
					key: '',
					value: '',
				},
			];
		}
	},
	set(newValue) {
		emit('update:modelValue', {
			...props.modelValue,
			attributes: newValue,
		});
	},
});

function addLinkAttribute() {
	linkAttributes.value = [
		...linkAttributes.value,
		{
			key: '',
			value: '',
		},
	];
}

function deleteAttribute(index: number) {
	const clone = [...linkAttributes.value];

	clone.splice(index, 1);
	linkAttributes.value = clone;
}

function onAttributeUpdate(index: number, attribute: Attribute) {
	const clone = [...linkAttributes.value];

	clone.splice(index, 1, attribute);

	linkAttributes.value = clone;
}

// Post/page search
watchEffect(
	() => {
		canShowSearchTooltip.value = linkURLComponent.value === 'BaseInput';
		if (urlInput.value) {
			popperRef.value = (urlInput.value as typeof BaseInput).input;
		}
	},
	{
		flush: 'post',
	},
);

watch(linkModel, newValue => {
	// Perform the search only if we don't have a link already
	if (newValue.length > 2 && newValue.indexOf('htt') === -1 && newValue.indexOf('#') !== 0) {
		searchPostDebounced();
	}

	if (newValue.length === 0) {
		showResults.value = false;
	}
});

const searchPostDebounced = debounce(() => {
	searchPost();
}, 300);

function searchPost() {
	const keyword = linkModel.value;
	const requester = window.zb.editor.serverRequest;

	isSearchLoading.value = true;

	requester.request(
		{
			type: 'search_posts',
			config: {
				keyword,
			},
		},
		response => {
			isSearchLoading.value = false;
			showResults.value = true;

			// response.data
			searchResults.value = response.data;
		},
		function (message) {
			console.error(message);
		},
	);
}

function onSearchItemClick(url: string) {
	linkModel.value = url;
	showResults.value = false;
}
</script>

<style lang="scss">
.znpb-link-wrapper {
	display: flex;
	flex-wrap: wrap;
	margin: 0 -5px;

	& > .znpb-input-wrapper {
		&:first-child {
			padding-bottom: 10px;
		}

		& > .znpb-forms-input-content {
			display: flex;

			label {
				margin-right: 10px;

				&:last-child {
					margin-right: 0;
				}
			}
		}

		& .zion-input__prepend {
			background: var(--zb-surface-lighter-color);
			padding: 10px;
		}

		& .zion-input__append {
			.zion-tags-attributes {
				margin-right: 5px;
				cursor: pointer;

				&:hover {
					color: #959595;
				}
			}
		}
	}

	& > .zion-input {
		margin-bottom: 15px;
	}
}

.znpb-link-optionsTooltip {
	width: 500px;
}

.znpb-link-optionsAttributesAdd {
	display: flex;
	justify-content: center;
	align-items: center;
	color: var(--zb-surface-text-active-color);
	padding: 10px 5px 5px;
	font-weight: 500;
	line-height: 1;
	cursor: pointer;

	.znpb-editor-icon-wrapper {
		margin-right: 3px;
	}
}

.znpb-link-options-title {
	position: relative;
	display: flex;
	justify-content: center;
	align-items: center;
	padding: 5px;
	margin-bottom: 10px;
	color: var(--zb-surface-text-color);
	font-family: 'Roboto', sans-serif;
	font-size: 13px;
	font-weight: 500;
	line-height: 14px;
}

.znpb-optionLinkTooltip {
	width: 100%;
}

.znpb-link--optionsForm {
	width: 100%;
}
</style>
