<template>
	<div class="zb">
		<SortableContent
			v-if="element"
			class="znpb-preview-page-wrapper"
			:element="element"
		/>

		<PageStyles
			:css-classes="CSSClasses"
			:page-settings-model="pageSettings"
			:page-settings-schema="getSchema('pageSettingsSchema')"
		/>

		<ElementStyles :styles="pageSettings._custom_css" />
	</div>

</template>
<script>
import { computed, ref, onBeforeUnmount, watch, provide } from 'vue'
import PageStyles from './components/PageStyles.vue'
import ElementStyles from './components/ElementStyles.vue'
import SortableContent from './components/SortableContent.vue'
import { useElements, useCSSClasses, usePreviewMode, usePreviewLoading, usePageSettings, useWindows, useEditorData } from '@zb/editor'
import { useOptionsSchemas } from '@zb/components'
import { trigger } from '@zb/hooks'

export default {
	name: 'PreviewApp',
	components: {
		SortableContent,
		PageStyles,
		ElementStyles
	},
	setup () {
		const { editorData } = useEditorData()
		const { getElement } = useElements()
		const { getSchema } = useOptionsSchemas()
		const { CSSClasses } = useCSSClasses()
		const { isPreviewMode } = usePreviewMode()
		const { setPreviewLoading } = usePreviewLoading()
		const { pageSettings } = usePageSettings()
		const { removeWindow } = useWindows()

		const element = computed(() => getElement('content'))
		const showExportModal = ref(false)

		// provide masks for ShapeDividerComponent option
		provide('masks', editorData.value.masks)
		provide('plugin_info', editorData.value.plugin_info)
		provide('editor_urls', editorData.value.urls)

		watch(isPreviewMode, (newValue) => {
			if (newValue) {
				window.document.body.classList.add('znpb-editor-preview--active')
			} else {
				window.document.body.classList.remove('znpb-editor-preview--active')
			}
		})

		// Allow other to hook into setup
		trigger('zionbuilder/preview/app/setup')

		return {
			element,
			showExportModal,
			getSchema,
			CSSClasses,
			isPreviewMode,
			pageSettings
		}
	}
}
</script>

<style lang="scss">
// @import "@/scss/preview.scss";
.znpb-editor-preview {
	overflow-x: hidden;
}

.znpb-editor-preview--active {
	& .zb-element, & .zb-element:hover {
		cursor: initial;
	}
}

.hg-popper {
	ul {
		// padding: 0;
		margin: 0;
		list-style-type: none;
		li {
			margin: 0;
			line-height: 1;
		}
	}
}

.zion-tour {
	position: relative;
	&:after {
		content: "" !important;
		position: absolute;
		top: 0;
		left: 0;
		z-index: 9999999;
		display: block !important;
		width: 100%;
		height: 100%;
		border: 3px solid white;
		animation-duration: 1.5s;
		animation-iteration-count: infinite;
		animation-name: scaleInfinit;
		animation-timing-function: ease;
	}
	&.znpb-columns-templates__icons--full {
		&:after {
			border-color: $secondary;
		}
	}
	&.znpb-element__wrapper {
		&:after {
			display: block;
			border-color: $secondary;
		}
	}
	&.znpb-empty-placeholder__tour-icon {
		&:after {
			border-color: $secondary;
			border-radius: 50%;
		}
	}
	&.znpb-empty-placeholder {
		&:after {
			border-color: $secondary;
		}
	}
	&.znpb-tabs__header-item--library, &.znpb-tabs__header-item--elements, &.znpb-tabs__header-item--columns {
		&:after {
			border-color: $secondary;
		}
	}
}
@keyframes scaleInfinit {
	0% {
		transform: scale(1);
	}
	50% {
		transform: scale(1.3);
	}
	100% {
		transform: scale(1);
	}
}
</style>
