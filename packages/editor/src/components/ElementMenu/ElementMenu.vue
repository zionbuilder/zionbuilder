<template>
	<Tooltip
		v-if="activeElementMenu"
		tooltip-class="hg-popper--big-arrows znpb-rightClickMenu__Tooltip"
		placement='auto'
		:show="true"
		append-to="body"
		trigger="click"
		:close-on-outside-click="true"
		:close-on-escape="true"
		:popperRef="activeElementMenu.selector"
		@hide="hideElementMenu"
		:key="activeElementMenu.key"
	>
		<template #content>
			<Menu
				:actions="elementActions"
				@action="hideElementMenu"
			/>
		</template>
	</Tooltip>
</template>

<script>
import { ref, watch, computed } from 'vue'

import { Environment } from '@zb/utils'
import { useElementMenu, useWindows, useEditElement, useElementActions, useLocalStorage } from '@composables'
import { translate } from '@zb/i18n'

export default {
	name: 'ElementMenu',
	props: {
		elementUid: String,
		parentUid: String,
		isActive: {
			type: Boolean,
			required: false
		}
	},
	setup () {
		const showOptions = ref(false)
		const { activeElementMenu, hideElementMenu } = useElementMenu()
		const { addEventListener, removeEventListener } = useWindows()
		const { getData } = useLocalStorage()

		const controllKey = Environment.isMac ? '⌘' : '⌃'

		// TODO: deprecate useEditElement and move code to useElementActions
		const { editElement } = useEditElement()
		const {
			copyElement,
			pasteElement,
			copiedElement,
			copyElementStyles,
			pasteElementStyles,
			copiedElementStyles,
			copyElementClasses,
			pasteElementClasses,
		} = useElementActions()

		// Computed
		const elementActions = computed(() => {
			const element = activeElementMenu.value.element
			return [
				{
					title: `${translate('action_edit')} ${element.name}`,
					icon: 'edit',
					action: () => {
						editElement(element)
					},
					cssClasses: 'znpb-menu-item--separator-bottom'
				},
				{
					title: `${translate('duplicate_element')}`,
					icon: 'copy',
					action: () => {
						element.duplicate()
					},
					append: `${controllKey}+D`
				},
				{
					title: `${translate('copy_element')}`,
					icon: 'copy',
					action: () => {
						copyElement(element)
					},
					append: `${controllKey}+C`
				},
				{
					title: `${translate('cut_element')}`,
					icon: 'close',
					action: () => {
						copyElement(element, 'cut')
					},
					append: `${controllKey}+X`
				},
				// TODO: add disabled for inactive actions
				{
					title: `${translate('paste_element')}`,
					icon: 'paste',
					action: () => {
						pasteElement(element)
					},
					append: `${controllKey}+V`,
					show: hasCopiedElement.value
				},
				{
					title: translate('paste_classes'),
					icon: 'braces',
					action: () => {
						pasteElementClasses(element)
					},
				},
				{

					title: translate('paste_element_styles'),
					icon: 'drop',
					action: () => {
						pasteElementStyles(element)
					},
					append: `${controllKey}+⇧+V`
				},
				{
					title: translate('discard_element_styles'),
					icon: 'drop',
					action: discardElementStyles
				},
				// {
				// 	title: translate('copy_element_styles'),
				// 	icon: 'drop',
				// 	action: () => {
				// 		copyElementStyles(element)
				// 	},
				// 	append: `${controllKey}+⇧+C`
				// },


				// {
				// 	title: translate('copy_classes'),
				// 	icon: 'braces',
				// 	action: () => {
				// 		copyElementClasses(element)
				// 	}
				// },

				{
					title: translate('save_element'),
					icon: 'check',
					action: saveElement
				},
				{
					title: element.isVisible ? translate('visible_element') : translate('show_element'),
					icon: 'eye',
					action: () => {
						element.toggleVisibility()
					},
					append: `${controllKey}+H`,
					cssClasses: 'znpb-menu-item--separator-bottom'
				},
				{
					title: translate('delete_element'),
					icon: 'delete',
					action: element.delete,
					append: `⌦`
				},
			]
		})

		watch(activeElementMenu, (newValue) => {
			if (newValue) {
				addEventListener('scroll', hideElementMenu)
			} else {
				removeEventListener('scroll', hideElementMenu)
			}
		})

		function discardElementStyles () {
			this.element.options = {
				...this.element.options,
				_styles: {}
			}
			this.close()
		}

		function saveElement () {
			const { showSaveElement } = useSaveTemplate()

			showSaveElement(this.element, 'block')

			this.close()
		}

		const hasCopiedElement = computed(() => {
			return !!(copiedElement.value.element || getData('copiedElement'))
		})

		return {
			showOptions,
			activeElementMenu,
			hideElementMenu,
			elementActions,
			hasCopiedElement
		}
	}
}
</script>
<style lang="scss">
.znpb-element-options {
	&__dropdown-icon {
		padding: 15px;
	}

	&__options-container {
		position: relative;
		position: absolute;
		top: 100%;
		right: 0;
		z-index: 9999;
		width: 172px;
		padding: 12px 0;
		margin-top: -1px;
		text-align: left;
		list-style-type: none;
		background: var(--zb-surface-color);
		box-shadow: 0 0 16px 0 rgba(0, 0, 0, .08);
		border-radius: 3px;
		transition: all .5s;
		user-select: none;

		li {
			min-width: 172px;
			padding: 12px 29px;
			color: var(--zb-surface-text-color);
			font-size: 12px;
			line-height: 14px;
			transition: color .2s ease;
			&:hover {
				color: var(--zb-surface-text-active-color);
				cursor: pointer;
			}
		}
	}
}

.list-enter-to, .list-leave-from {
	transition: all .2s;
}

.list-enter-from, .list-leave-to {
	transform: translateY(10%);
	opacity: 0;
}

.znpb-rightClickMenu__Tooltip {
	padding: 0;
}
</style>
