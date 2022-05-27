<template>
	<div class="znpb-link-wrapper">
		<InputWrapper layout="full">
			<component
				:is="linkURLComponent"
				ref="urlInput"
				v-model="linkModel"
				:placeholder="$translate('add_an_url')"
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
						append-to="body"
						tooltip-class="znpb-link-optionsTooltip"
						placement="bottom-end"
						class="znpb-flex znpb-flex--vcenter"
					>
						<template #content>
							<div class="znpb-link-options">
								<div class="znpb-link-options-title">{{ $translate('link_attributes') }}</div>
								<div class="znpb-link-optionsAttributes">
									<LinkAttributeForm
										v-for="(attribute, index) in linkAttributes"
										:key="index"
										:attribute-config="attribute"
										:can-delete="canDeleteAttributes"
										@update-attribute="onAttributeUpdate(index, $event)"
										@delete="deleteAttribute(index)"
									/>

									<div class="znpb-link-optionsAttributesAdd" @click="addLinkAttribute">
										<Icon icon="plus" /> <span>{{ $translate('add_custom_link_attribute') }}</span>
									</div>
								</div>
							</div>
						</template>

						<Icon v-znpb-tooltip="$translate('edit_link_attributes')" icon="tags-attributes"></Icon>
					</Tooltip>

					<!-- Injection point -->
					<Injection location="options/link/append" />
				</template>
			</component>
		</InputWrapper>
		<InputWrapper layout="inline" :schema="{ width: 50 }">
			<InputSelect v-model="targetModel" :options="targetOptions"></InputSelect>
		</InputWrapper>
		<InputWrapper layout="inline" :schema="{ width: 50 }">
			<BaseInput v-model="titleModel" :clearable="false" :placeholder="$translate('set_a_title')" />
		</InputWrapper>

		<Tooltip
			v-if="canShowSearchTooltip && popperRef"
			v-model:show="showResults"
			:popper-ref="popperRef"
			placement="bottom"
			:show-arrows="false"
			:tooltip-style="{ width: tooltipWidth + 'px' }"
			trigger="click"
			:close-on-outside-click="true"
			tooltip-class="hg-popper--no-padding"
			@show="onModalShow"
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
		</Tooltip>
	</div>
</template>

<script>
import { computed, ref, watchEffect, watch } from 'vue';
import { get, debounce } from 'lodash-es';
import { Injection, Tooltip } from '@/common';
import { applyFilters } from '@/common/modules/hooks';
import LinkAttributeForm from './LinkAttributeForm.vue';

export default {
	name: 'Link',
	components: {
		Injection,
		Tooltip,
		LinkAttributeForm,
	},
	props: {
		modelValue: {
			default() {
				return {};
			},
		},
		title: {},
	},
	setup(props, { emit }) {
		const urlInput = ref(false);
		const canShowSearchTooltip = ref(false);
		const popperRef = ref(false);
		const isSearchLoading = ref(false);
		const showResults = ref(false);
		const tooltipWidth = ref(null);
		const searchResults = ref([]);

		const linkURLComponent = computed(() => {
			return applyFilters('zionbuilder/options/link/url_component', 'BaseInput', props.modelValue);
		});

		const linkModel = computed({
			get() {
				return props.modelValue && props.modelValue['link'] ? props.modelValue['link'] : '';
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
				let attributes = get(props.modelValue, 'attributes');
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

		const canDeleteAttributes = computed(() => linkAttributes.value.length > 1);

		function addLinkAttribute() {
			linkAttributes.value = [
				...linkAttributes.value,
				{
					key: '',
					value: '',
				},
			];
		}

		function deleteAttribute(index) {
			const clone = [...linkAttributes.value];

			clone.splice(index, 1);
			linkAttributes.value = clone;
		}

		function onAttributeUpdate(index, attribute) {
			const clone = [...linkAttributes.value];

			clone.splice(index, 1, attribute);

			linkAttributes.value = clone;
		}

		// Post/page search
		watchEffect(
			() => {
				canShowSearchTooltip.value = linkURLComponent.value === 'BaseInput';
				popperRef.value = urlInput.value.$el;
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

		function onModalShow() {
			// Set the tooltip width
			if (urlInput.value) {
				tooltipWidth.value = urlInput.value.$el.getBoundingClientRect().width;
			}
		}

		function onSearchItemClick(url) {
			linkModel.value = url;
			showResults.value = false;
		}

		return {
			// Model
			titleModel,
			targetModel,
			linkModel,
			linkURLComponent,
			addLinkAttribute,
			linkAttributes,
			deleteAttribute,
			onAttributeUpdate,
			canDeleteAttributes,
			// Link search
			urlInput,
			popperRef,
			canShowSearchTooltip,
			isSearchLoading,
			showResults,
			onModalShow,
			onSearchItemClick,
			tooltipWidth,
			searchResults,
		};
	},
	data() {
		return {
			targetOptions: [
				{
					id: '_blank',
					name: this.$translate('link_new_window'),
				},
				{
					id: '_self',
					name: this.$translate('link_blank'),
				},
			],
		};
	},
};
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
	width: 320px;
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
</style>
