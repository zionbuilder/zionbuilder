<template>
	<li
		ref="root"
		class="znpb-editor-library-modal__item"
		:class="{ 'znpb-editor-library-modal__item--favorite': favorite }"
	>
		<div class="znpb-editor-library-modal__itemInner">
			<div
				class="znpb-editor-library-modal__item-image"
				:class="{ ['--no-image']: !item.thumbnail && !item.loadingThumbnail }"
				@click="$emit('activate-item', item)"
			>
				<Loader v-if="item.loadingThumbnail" />
				<img
					v-else-if="item.thumbnail"
					ref="imageHolder"
					class="znpb-editor-library-modal__item-imageTag"
					src=""
					:data-zbg="image"
					@mouseover="onMouseOver"
					@mouseout="onMouseOut"
				/>
			</div>

			<div v-if="item.pro" class="znpb-editor-library-modal__item-pro">{{ __('pro', 'zionbuilder') }}</div>

			<div class="znpb-editor-library-modal__item-bottom">
				<h4 class="znpb-editor-library-modal__item-title" :title="item.name">{{ item.name }}</h4>
				<div v-if="!insertItemLoading && !item.loading" class="znpb-editor-library-modal__item-actions">
					<a
						v-if="isProInstalled && !isProActive && item.pro"
						class="znpb-button znpb-button--line"
						target="_blank"
						:href="dashboardURL"
					>
						{{ __('Activate PRO', 'zionbuilder') }}
					</a>

					<a
						v-else-if="!isProInstalled && item.pro"
						class="znpb-button znpb-button--line"
						:href="purchaseURL"
						target="_blank"
						>{{ __('Buy Pro', 'zionbuilder') }}
					</a>

					<span
						v-else
						v-znpb-tooltip="__('Insert this item into page', 'zionbuilder')"
						class="znpb-button znpb-button--line znpb-editor-library-modal__item-action"
						@click.stop="insertLibraryItem"
						>{{ __('Insert', 'zionbuilder') }}</span
					>

					<Icon
						v-znpb-tooltip="__('Click to preview this item', 'zionbuilder')"
						icon="eye"
						class="znpb-editor-library-modal__item-action"
						@click="$emit('activate-item', item)"
					/>

					<HiddenMenu
						v-if="item.librarySource.id === 'local_library'"
						class="znpb-editor-library-modal__item-action"
						:actions="itemMenuActions"
					/>
				</div>
				<Loader v-else :size="12" />
			</div>
			<div v-if="item.type === 'multiple'" class="znpb-editor-library-modal__item-bottom-multiple" />
		</div>
	</li>
</template>

<script>
import { __ } from '@wordpress/i18n';
import { ref, inject, onMounted, onBeforeUnmount, computed, watch } from 'vue';
import { useEditorData } from '/@/editor/composables';
import { useThumbnailGeneration } from './composables/useThumbnailGeneration.js';

export default {
	name: 'LibraryItem',
	props: {
		item: {
			type: Object,
			required: false,
		},
		favorite: {
			type: Boolean,
			required: false,
		},
		inView: {
			type: Boolean,
			required: false,
			default: false,
		},
	},
	setup(props, { emit }) {
		const Library = inject('Library');
		const { editorData } = useEditorData();
		const isProActive = editorData.value.plugin_info.is_pro_active;
		const isProInstalled = editorData.value.plugin_info.is_pro_installed;
		const dashboardURL = ref('');
		const purchaseURL = ref('');
		const imageHolder = ref(null);
		const root = ref(null);

		dashboardURL.value = `${editorData.value.urls.zion_admin}#/pro-license`;
		purchaseURL.value = editorData.value.urls.purchase_url;

		const iObserver = new IntersectionObserver(onItemInView);
		const image = computed(() => {
			return props.item.thumbnail;
		});

		// Check to see if we need to generate image
		if (
			props.item.librarySource.type === 'local' &&
			props.item.thumbnail.length === 0 &&
			!props.item.thumbnail_failed
		) {
			// Register the thumbnail for generation
			const { generateScreenshot, removeFromQueue } = useThumbnailGeneration();
			generateScreenshot(props.item);

			onBeforeUnmount(() => {
				removeFromQueue(props.item);
			});
		}

		function onItemInView(entries) {
			entries.forEach(({ isIntersecting }) => {
				if (!isIntersecting) {
					return;
				}

				if (props.item.thumbnail) {
					imageHolder.value.src = imageHolder.value.getAttribute('data-zbg');
				}

				iObserver.unobserve(root.value);
			});
		}

		watch(
			() => props.item.thumbnail,
			newValue => {
				iObserver.observe(root.value);
			},
		);

		onMounted(() => {
			iObserver.observe(root.value);
		});

		onBeforeUnmount(() => {
			if (root.value) {
				iObserver.unobserve(root.value);
			}
		});

		const itemMenuActions = computed(() => {
			if (!props.item.librarySource.id === 'local_library') {
				return [];
			}

			return [
				{
					title: __('Edit template', 'zionbuilder'),
					action: () => {
						return window.open(props.item.urls.edit_url, '_blank').focus();
					},
					icon: 'edit',
				},
				{
					title: __('Export template', 'zionbuilder'),
					action: () => {
						props.item.export();
					},
					icon: 'export',
				},
				{
					title: __('Regenerate screenshot', 'zionbuilder'),
					action: () => {
						const { generateScreenshot } = useThumbnailGeneration();

						generateScreenshot(props.item);
					},
					icon: 'export',
				},
				{
					title: __('Delete template', 'zionbuilder'),
					action: () => {
						props.item.delete();
					},
					icon: 'delete',
				},
			];
		});

		// Image scroll on hover
		function onMouseOver(event) {
			const { height } = event.target.getBoundingClientRect();

			if (height > 200) {
				const newTop = height - 200;
				event.target.style.top = `-${newTop}px`;
			}
		}

		function onMouseOut(event) {
			event.target.style.top = null;
		}

		return {
			// Refs
			imageHolder,
			root,
			purchaseURL,
			dashboardURL,
			isProInstalled,
			isProActive,
			Library,
			image,

			// Computed
			itemMenuActions,

			// methods
			onMouseOver,
			onMouseOut,
		};
	},
	data() {
		return {
			insertItemLoading: false,
		};
	},
	methods: {
		insertLibraryItem() {
			this.insertItemLoading = true;
			// If it's pro, get the download URL
			this.Library.insertItem(this.item)
				.then(() => {})
				.finally(() => {
					this.insertItemLoading = false;
				});
		},
	},
};
</script>
<style lang="scss">
.znpb-editor-library-modal__item--grid-sizer,
.znpb-editor-library-modal__item {
	width: 100%;
}

@media (min-width: 992px) {
	.znpb-editor-library-modal__item--grid-sizer,
	.znpb-editor-library-modal__item {
		width: 50%;
	}

	.znpb-editor-library-modal__item--gutter-sizer {
		width: 20px;
	}
}

@media (min-width: 1200px) {
	.znpb-editor-library-modal__item--grid-sizer,
	.znpb-editor-library-modal__item {
		width: calc(100% / 3);
	}
}

.znpb-editor-library-modal__item {
	position: relative;
	float: left;
	padding: 10px;
	margin-bottom: 20px;
	border-radius: 3px;
	transition: box-shadow 0.2s;
	&Inner {
		cursor: pointer;
	}

	&Inner:hover {
		box-shadow: 0 12px 30px 0 var(--zb-surface-shadow-hover);

		.znpb-editor-library-modal__item-bottom-multiple {
			box-shadow: 0 12px 30px 0 var(--zb-surface-shadow-hover);
		}
	}

	&-image {
		position: relative;
		display: flex;
		align-content: center;
		overflow: hidden;
		height: 200px;
		background-position: center;
		border-bottom: 1px solid var(--zb-surface-lighter-color);

		&Tag {
			position: absolute;
			top: 0;
			left: 0;
			align-self: center;
			transition: top 5s;
		}

		&.--no-image {
			min-height: 180px;
			background-color: var(--zb-surface-lighter-color);
			background-image: url('./no_preview_available.svg');
		}
	}

	&-pro {
		position: absolute;
		top: 20px;
		right: 20px;
		padding: 5px 8px;
		color: #fff;
		font-size: 10px;
		font-weight: 700;
		text-transform: uppercase;
		background-color: var(--zb-pro-color);
		border-radius: 2px;
	}

	&-bottom {
		position: relative;
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 10px 20px;
		background: var(--zb-surface-lighter-color);
		border-bottom-right-radius: 3px;
		border-bottom-left-radius: 3px;

		.znpb-loader-wrapper {
			max-width: 40px;
		}
	}

	&-title {
		overflow: hidden;
		color: var(--zb-surface-text-active-color);
		font-size: 13px;
		font-weight: 500;
		line-height: 40px !important;
		text-overflow: clip;
		white-space: nowrap;
	}

	&-actions {
		display: flex;
		align-items: center;
		margin-left: 8px;

		& > span,
		& > a {
			margin-right: 8px;
			white-space: pre;
		}

		& > span:last-child {
			margin-right: 0;
		}
	}

	&-action {
		.znpb-button {
			padding: 10px 18px;
		}
	}

	&-bottom-multiple {
		position: absolute;
		bottom: 0;
		left: 0;
		z-index: -1;
		width: 100%;
		height: 30px;
		background-color: rgb(255, 255, 255);
		border: 1px solid var(--zb-surface-border-color);
		border-radius: 3px;
		transform: scale(0.9) translateY(15px);
		transition: box-shadow 0.2s;

		break-inside: avoid;

		&:after {
			content: '';
			position: absolute;
			bottom: 0;
			left: 0;
			width: 100%;
			height: 30px;
			background-color: rgb(255, 255, 255);
			box-shadow: 0 4px 10px 0 rgba(164, 164, 164, 0.08);
			border: 1px solid var(--zb-surface-border-color);
			border-radius: 3px;
			transform: scale(1.07) translateY(-6px);
		}
	}

	&-bottom-actions {
		.znpb-editor-icon-wrapper {
			margin-right: 8px;
			font-size: 18px;
			cursor: pointer;

			&:last-child {
				margin-right: 0;
			}
		}
	}
	&--favorite {
		.znpb-editor-icon.zion-heart {
			path,
			path:first-child {
				fill: var(--zb-secondary-color);
			}
		}
	}
}
</style>
