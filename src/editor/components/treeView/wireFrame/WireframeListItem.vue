<template>
	<li
		class="znpb-wireframe-item"
		:class="getClasses"
		@click.stop="element.focus"
		@contextmenu.stop.prevent="showElementMenu"
	>
		<div class="znpb-wireframe-item__header">
			<div class="znpb-wireframe-item__header-area znpb-wireframe-item__header-area--left">
				<Icon
					v-if="element.isWrapper"
					class="znpb-wireframe-item__header-item znpb-wireframe-item__header-button znpb-wireframe-item__header-more znpb-utility__cursor--pointer"
					icon="select"
					:rotate="expanded ? '180' : false"
					@click="expanded = !expanded"
				/>

				<UIElementIcon :element="elementModel" class="znpb-tree-view__itemIcon" :size="24" />

				<span
					v-if="element.isRepeaterProvider"
					v-znpb-tooltip="__('repeater provider', 'zionbuilder')"
					class="znpb-tree-view__itemLooperIcon"
					>P</span
				>
				<span
					v-if="element.isRepeaterConsumer"
					v-znpb-tooltip="__('repeater consumer', 'zionbuilder')"
					class="znpb-tree-view__itemLooperIcon"
					>C</span
				>
				<InlineEdit v-model="elementName" class="znpb-wireframe-item__header-title znpb-wireframe-item__header-item" />
			</div>
			<div class="znpb-wireframe-item__header-area znpb-wireframe-item__header-area--right">
				<Icon
					v-if="!element.isVisible"
					v-znpb-tooltip="translate('enable_hidden_element')"
					icon="visibility-hidden"
					class="znpb-editor-icon-wrapper--show-element znpb-tree-view__item-enable-visible znpb-wireframe-item__header-area--visibility-icon"
					@click.stop="element.isVisible = !element.isVisible"
				/>

				<div ref="elementOptionsRef" class="znpb-element-options__container" @click.stop="showElementMenu">
					<Icon class="znpb-element-options__dropdown-icon znpb-utility__cursor--pointer" icon="more" />
				</div>
			</div>
		</div>

		<WireframeList
			v-if="expanded && element.isWrapper"
			:element="element"
			class="znpb-wireframe-item__content"
			:class="{ [`znpb-flex--${hasFlexDirection}`]: hasFlexDirection }"
		/>
	</li>
</template>
<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { computed, ref } from 'vue';
import { get } from 'lodash-es';
import { useTreeViewItem } from '../useTreeViewItem';
import { useElementDefinitionsStore } from '/@/editor/store';
import WireframeList from './WireframeList.vue';

// Common API
const { translate } = window.zb.i18n;

const props = defineProps<{
	element: ZionElement;
}>();

const expanded = ref(true);

const { showElementMenu, elementOptionsRef } = useTreeViewItem(props.element);
const columnSize = computed(() => props.element.options.column_size);

const elementsDefinitionsStore = useElementDefinitionsStore();

const elementModel = elementsDefinitionsStore.getElementDefinition(props.element.element_type);

const elementName = computed({
	get: () => props.element.name,
	set(newValue: string) {
		props.element.name = newValue;
	},
});

const hasFlexDirection = computed(() => {
	let orientation = 'column';
	let mediaOrientation = get(props.element.options, '_styles.wrapper.styles.default.default.flex-direction');

	if (props.element.element_type === 'zion_section') {
		mediaOrientation = get(
			props.element.options,
			'_styles.inner_content_styles.styles.default.default.flex-direction',
			'row',
		);
	}

	if (mediaOrientation) {
		orientation = mediaOrientation;
	}

	return orientation;
});

const getClasses = computed(() => {
	let cssClass = {
		[`znpb-wireframe-item--item--hidden`]: !props.element.isVisible,
		[`znpb-wireframe-item--${props.element.element_type}`]: props.element.element_type,
		[`znpb-wireframe-item__empty`]: !props.element.content.length,
		'znpb-wireframe-item--loopProvider': props.element.isRepeaterProvider,
		'znpb-wireframe-item--loopConsumer': props.element.isRepeaterConsumer,
	};

	if (columnSize.value) {
		Object.keys(columnSize.value).forEach(key => {
			let responsivePrefix = getColumnResponsivePrefix(key);
			cssClass[`zb-column--${responsivePrefix}${columnSize.value[key]}`] = !!columnSize.value[key];
		});
	}
	return cssClass;
});

function getColumnResponsivePrefix(responsiveMediaId: string) {
	const devices: Record<string, string> = {
		default: '',
		laptop: 'lg--',
		tablet: 'md--',
		mobile: 'sm--',
	};

	return devices[responsiveMediaId];
}
</script>
<style lang="scss">
@import 'grid.scss';

.znpb-editor-icon-wrapper--show-element {
	padding: 15px 15px 15px;
	transition: opacity 0.2s ease;
	cursor: pointer;

	&:hover {
		opacity: 1;
	}
}

.znpb-wireframe-view-wrapper {
	padding-top: 5px;
	padding-bottom: 50px;
}
.znpb-wireframe-item {
	flex-grow: 1;
	flex-shrink: 1;
	padding: 0 8px 16px 8px;

	&Image {
		height: 24px;
	}
	&Image,
	&Icon {
		padding-right: 15px;
	}

	.znpb-wireframe-item--column .znpb-empty-placeholder {
		border-right: 2px solid #faeec6;
		border-left: 2px solid #faeec6;
	}

	.znpb-empty-placeholder {
		height: auto;
		min-height: 24px;
	}

	.znpb-element-toolbox__add-element-button {
		--button-size: 24px;
		--font-size: 12px;
	}

	&__empty {
		display: block;
		.znpb-wireframe-item__content {
			padding: 15px;
		}
	}
	&__sortable {
		cursor: pointer;
	}

	&__delete-icon {
		padding: 13px 20px 13px 0;
		transition: opacity 0.2s;
		cursor: pointer;

		span {
			transition: none;
		}

		&:hover,
		&:focus {
			opacity: 0.5;
		}
	}

	.znpb-element-toolbox__add-element-button {
		margin: 0 auto;
		text-align: center;
		transform: translate(-50%, -50%);
	}

	&__header {
		display: flex;
		width: 100%;
		color: var(--zb-primary-text-color);
		text-align: center;
		background-color: var(--zb-surface-lightest-color);
		border-radius: 6px;
		padding: 0 15px;
		z-index: 1;
		transition: all 0.2s;

		&-area {
			display: flex;
			flex-basis: 0;
			flex-grow: 1;
			color: var(--zb-surface-text-hover-color);

			&--visibility-icon {
				display: flex;
				align-items: center;

				.znpb-editor-icon-wrapper--show-element {
					padding-right: 0;
				}
			}
			&--left {
				align-items: center;
				overflow: hidden;

				.znpb-utility__text--elipse {
					&:after {
						display: none;
					}
				}
				& > span {
					color: var(--zb-surface-icon-color);
				}
			}
			&--right {
				position: relative;
				justify-content: flex-end;
				flex-grow: 0;
			}
		}

		&-item {
			position: relative;
			padding: 13px 20px;
			transition: opacity 0.2s;

			&:focus {
				outline: 0;
			}
		}

		&-title {
			overflow: hidden;
			padding: 0;
			color: var(--zb-surface-text-hover-color);
			font-weight: 500;
			text-overflow: ellipsis;
			white-space: nowrap;

			.znpb-utility__text--elipse {
				width: 100%;
				max-width: 170px;
			}
		}

		&-more {
			padding: 14px 15px 14px 8px;
			margin-left: -15px;

			&:hover {
				color: var(--zb-surface-icon-active-color);
			}
		}

		.znpb-tree-view__itemIcon {
			padding: 0;
			margin-right: 8px;
		}

		.znpb-element-options__dropdown-icon {
			color: var(--zb-surface-icon-color);
			padding: 14px 15px;
			margin-right: -15px;

			&:hover {
				color: var(--zb-surface-icon-active-color);
			}
		}
	}

	&--loopProvider {
		& > .znpb-wireframe-item__header {
			background-color: #14ae5c;
			color: var(--zb-secondary-text-color);

			& .znpb-wireframe-item__header-area--left > span,
			& .znpb-wireframe-item__header-title,
			& .znpb-element-options__dropdown-icon {
				color: var(--zb-secondary-text-color);
			}
		}

		& > .znpb-wireframe-view-wrapper {
			.znpb-element-toolbox__add-element-button::before {
				background-color: #14ae5c;
			}
		}

		.znpb-tree-view__itemLooperIcon {
			margin-right: 5px;
		}
	}

	&--loopConsumer {
		& > .znpb-wireframe-item__header {
			background-color: #eda926;
			color: var(--zb-secondary-text-color);

			& .znpb-wireframe-item__header-area--left > span,
			& .znpb-wireframe-item__header-title,
			& .znpb-element-options__dropdown-icon {
				color: var(--zb-secondary-text-color);
			}
		}

		& > .znpb-wireframe-view-wrapper {
			.znpb-element-toolbox__add-element-button::before {
				background-color: #eda926;
			}
		}

		.znpb-tree-view__itemLooperIcon {
			margin-right: 5px;
		}
	}

	&--zion_section {
		position: relative;
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
		align-items: center;
		flex: 1 1 auto;
		width: auto;

		& > .znpb-wireframe-item__content {
			position: relative;
			display: flex;
			flex-wrap: wrap;
			align-items: flex-start;
			flex: 1 1 auto;
			width: 100%;
		}
	}

	&--zion_column {
		position: relative;
		display: flex;
		flex-direction: column;
		flex-wrap: wrap;
		flex-grow: 1;
		min-height: 1px;

		& > .znpb-wireframe-item__content {
			position: relative;
			display: flex;
			flex-direction: column;
			flex-wrap: wrap;
			flex: 1 1 auto;
			width: 100%;
			border: 1px solid var(--zb-surface-lightest-color);
			border-bottom-left-radius: 6px;
			border-bottom-right-radius: 6px;
		}
		& > .znpb-wireframe-item__content > .znpb-element-toolbox__add-element-button {
			& > .znpb-editor-icon-wrapper {
				background: var(--zb-column-color);
			}
		}
	}

	&--item--hidden {
		opacity: 0.5;
	}
	&__content {
		position: relative;
		width: 100%;
		&.znpb-flex {
			&--row {
				flex-direction: row;
			}
			&--column {
				flex-direction: column;
				justify-content: center;
				align-items: stretch;
			}
		}
	}

	// Children of content should have dashed border instead of shadow
	&__content &__content {
		box-shadow: none;
		border-top: none;
	}

	// Children of content should have dashed border instead of shadow
	&__content &__content {
		box-shadow: none;
		border-top: none;
	}

	//nested children
	ul.znpb-wireframe-item__content {
		padding: 21px 8px 16px 8px;
		background: var(--zb-surface-light-color);
		cursor: pointer;
		border-bottom-left-radius: 6px;
		border-bottom-right-radius: 6px;
		margin-top: -5px;
	}
}
</style>
