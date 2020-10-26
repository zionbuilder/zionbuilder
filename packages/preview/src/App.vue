<template>
	<div class="zb">
		<SortableContent
			v-if="element"
			class="znpb-preview-page-wrapper"
			:element="element"
		/>
<!--
		<PageStyles
			:css-classes="getClasses"
			:page-settings-model="getPageSettings"
			:page-settings-schema="getSchema('pageSettingsSchema')"
		/>

		<ElementStyles :styles="getPageSettings._custom_css" /> -->
	</div>

</template>
<script>
import { computed, ref } from 'vue'
import { mapGetters, mapActions } from 'vuex'
import PageStyles from './components/PageStyles.vue'
import ElementStyles from './components/ElementStyles.vue'
import { on } from '@zb/hooks'
import SortableContent from './components/SortableContent.vue'
import { useElements } from '@zb/editor'
import { useOptionsSchemas } from '@zb/components'

export default {
	name: 'PreviewApp',
	components: {
		SortableContent,
		PageStyles,
		ElementStyles
	},
	setup () {
		const { getElement } = useElements()
		const element = computed(() => getElement('content'))
		const showExportModal = ref(false)
		const { getSchema } = useOptionsSchemas()

		return {
			element,
			showExportModal,
			getSchema
		}
	},

	created () {
		this.setStylesLoading(false)
	},

	computed: {
		...mapGetters([
			'isPreviewMode',
			'getClasses',
			'getPageSettings'
		])
	},
	methods: {
		...mapActions([
			'setStylesLoading'
		])
	},

	beforeUnmount () {
		on('beforeunload', this.setStylesLoading(true))
	},
	watch: {
		isPreviewMode (newValue) {
			if (newValue) {
				window.document.body.classList.add('znpb-editor-preview--active')
			} else {
				window.document.body.classList.remove('znpb-editor-preview--active')
			}
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
