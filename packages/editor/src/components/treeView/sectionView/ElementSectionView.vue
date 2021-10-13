<template>
	<li
		class="znpb-section-view-item"
		:class="{'znpb-section-view-item--hidden': !element.isVisible}"
		@contextmenu.stop.prevent="showElementMenu"
		@mouseover.stop="element.highlight"
		@mouseout.stop="element.unHighlight"
		@click.stop.left="editElement"
	>
		<div v-if="loading || error">
			<Loader :size="16" />
			<span v-if="error">{{ $translate('preview_not_available')}}</span>
		</div>

		<img :src="imageSrc">

		<div
			class="znpb-section-view-item__header"
			:class="{'znpb-panel-item--active': isActiveItem}"
		>
			<UIElementIcon
				:element="elementModel"
				class="znpb-tree-view__itemIcon znpb-utility__cursor--move"
				:size="24"
			/>

			<div class="znpb-section-view-item__header-left">

				<div
					class="znpb-section-view-item__header-title"
					@input="element.name = $event.target.value"
					:contenteditable="true"
				>
					{{element.name}}
				</div>
			</div>

			<Tooltip
				v-if="!element.isVisible"
				:content="$translate('enable_hidden_element')"
				class="znpb-tree-view__item-enable-visible"
			>

				<transition name="fade">
					<Icon
						icon="visibility-hidden"
						@click="element.toggleVisibility()"
						class="znpb-editor-icon-wrapper--show-element"
					>
					</Icon>
				</transition>

			</Tooltip>

			<div
				class="znpb-element-options__container"
				@click.stop="showElementMenu"
				ref="elementOptionsRef"
			>
				<Icon
					class="znpb-element-options__dropdown-icon znpb-utility__cursor--pointer"
					icon="more"
				/>
			</div>
		</div>
	</li>
</template>
<script lang="ts">
import { ref, Ref, PropType } from "vue";
import domtoimage from "dom-to-image";
import { onMounted } from "vue";
import { translate } from "@zb/i18n";
import { Element } from "@composables";
import { useTreeViewItem } from "../useTreeViewItem";

export default {
	name: "element-section-view",
	props: {
		element: Object as PropType<Element>,
	},
	setup(props) {
		const {
			showElementMenu,
			elementOptionsRef,
			isActiveItem,
			editElement,
			elementModel,
		} = useTreeViewItem(props);

		const imageSrc = ref(null);
		const error = ref(null);
		const loading: Ref<boolean> = ref(false);

		onMounted(() => {
			const domElement = window.frames[
				"znpb-editor-iframe"
			].contentDocument.getElementById(props.element.elementCssId);

			if (!domElement) {
				console.warn(
					`Element with id "${props.element.elementCssId}" could not be found in page`
				);
				return;
			}

			function filter(node) {
				if (node && node.classList) {
					if (node.classList.contains("znpb-empty-placeholder")) {
						return false;
					}

					if (node.classList.contains("znpb-element-toolbox")) {
						return false;
					}
				}
				return true;
			}

			domtoimage
				.toPng(domElement, {
					style: {
						width: "100%",
						margin: 0,
					},
					filter: filter,
				})
				.then((dataUrl) => {
					imageSrc.value = dataUrl;
				})
				.catch((error) => {
					error = true;
					// eslint-disable-next-line
					console.error(translate("oops_something_wrong"), error);
				})
				.finally(() => {
					loading.value = false;
				});
		});

		return {
			imageSrc,
			error,
			loading,
			showElementMenu,
			elementOptionsRef,
			isActiveItem,
			editElement,
			elementModel,
		};
	},
};
</script>

<style lang="scss" >
.znpb-section-view-item {
	position: relative;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
	align-items: center;
	padding: 0;
	margin: 0 auto;
	margin-bottom: 20px;
	background-color: var(--zb-surface-lighter-color);
	border-bottom-right-radius: 3px;
	border-bottom-left-radius: 3px;
	cursor: move;

	&__header-left {
		position: relative;
		overflow: hidden;
	}
	&--hidden {
		.znpb-section-view-item__header-left {
			transition: opacity .5s ease;
			opacity: .5;
		}
	}

	.znpb-loader {
		margin: 15px 0;
	}

	&__image-wrapper {
		display: flex;
		justify-content: space-between;
		width: 100%;
		color: var(--zb-surface-text-color);
		line-height: 18px;
		border: 1px solid var(--zb-surface-lighter-color);
		cursor: pointer;
	}

	&__header {
		position: relative;
		display: flex;
		justify-content: space-between;
		align-content: center;
		align-items: center;
		width: 100%;
		margin: 0;
		font-size: 13px;
		font-weight: 500;
		background-color: var(--zb-surface-lighter-color);
		border-bottom-right-radius: 3px;
		border-bottom-left-radius: 3px;

		&:hover {
			background-color: var(--zb-surface-lightest-color);
			cursor: move;
		}

		&.znpb-panel-item--active {
			color: var(--zb-secondary-text-color);
			background-color: var(--zb-secondary-color);
		}

		&-left {
			flex-grow: 1;
		}

		&-title {
			padding: 15px;
			padding-left: 8px;
			color: var(--zb-surface-text-active-color);
			font-weight: 500;
			cursor: text;

			&:focus-visible {
				outline: none;
			}
		}

		&.znpb-panel-item--active &-title {
			color: var(--zb-secondary-text-color);
		}
	}
}
.znpb-admin-small-loader.znpb-admin-small-loader {
	position: absolute;
	top: 50%;
	left: 50%;
	z-index: 999999;
	z-index: 9;
	margin: 0;
	transform: translate(-50%, -50%);
}

.znpb-section-view-item__header .znpb-tree-view__itemIcon {
	padding-left: 15px;
}
</style>
