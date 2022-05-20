<template>
	<Tooltip
		v-if="activeElementMenu"
		:key="activeElementMenu.key"
		tooltip-class="hg-popper--big-arrows znpb-rightClickMenu__Tooltip"
		placement="auto"
		:show="true"
		append-to="body"
		trigger="click"
		:close-on-outside-click="true"
		:close-on-escape="true"
		:popper-ref="activeElementMenu.selector"
		@hide="hideElementMenu"
	>
		<template #content>
			<Menu :actions="elementActions" @action="hideElementMenu" />
		</template>
	</Tooltip>
</template>

<script>
import { ref, watch, computed } from 'vue';
import { get } from 'lodash-es';

import { Environment } from '@common/utils';
import {
	useElementMenu,
	useWindows,
	useEditElement,
	useElementActions,
	useLocalStorage,
	useSaveTemplate,
} from '../../composables';
import { translate } from '@common/modules/i18n';

export default {
	name: 'ElementMenu',
	props: {
		elementUid: String,
		parentUid: String,
		isActive: {
			type: Boolean,
			required: false,
		},
	},
	setup(props) {
		const showOptions = ref(false);
		const { activeElementMenu, hideElementMenu } = useElementMenu();
		const { addEventListener, removeEventListener } = useWindows();
		const { getData } = useLocalStorage();

		const controllKey = Environment.isMac ? '⌘' : '⌃';

		// TODO: deprecate useEditElement and move code to useElementActions
		const { editElement } = useEditElement();
		const { copyElement, pasteElement, copiedElement, pasteElementStyles, pasteElementClasses, wrapInContainer } =
			useElementActions();

		// Computed
		const elementActions = computed(() => {
			const element = activeElementMenu.value.element;
			return [
				{
					title: `${translate('action_edit')} ${element.name}`,
					icon: 'edit',
					action: () => {
						editElement(element);
					},
					cssClasses: 'znpb-menu-item--separator-bottom',
				},
				{
					title: `${translate('duplicate_element')}`,
					icon: 'copy',
					action: () => {
						element.duplicate();
					},
					append: `${controllKey}+D`,
				},
				{
					title: `${translate('copy_element')}`,
					icon: 'copy',
					action: () => {
						copyElement(element);
					},
					append: `${controllKey}+C`,
				},
				{
					title: `${translate('cut_element')}`,
					icon: 'close',
					action: () => {
						copyElement(element, 'cut');
					},
					append: `${controllKey}+X`,
				},
				{
					title: `${translate('paste_element')}`,
					icon: 'paste',
					action: () => {
						pasteElement(element);
					},
					append: `${controllKey}+V`,
					show: hasCopiedElement.value,
				},
				{
					title: translate('paste_element_styles'),
					icon: 'drop',
					action: () => {
						pasteElementStyles(element);
					},
					append: `${controllKey}+⇧+V`,
					show: hasCopiedElementStyles.value,
				},
				{
					title: translate('paste_classes'),
					icon: 'braces',
					action: () => {
						pasteElementClasses(element);
					},
					show: hasCopiedElementClasses.value,
				},
				{
					title: translate('save_element'),
					icon: 'check',
					action: () => {
						saveElement(element);
					},
				},
				{
					title: element.isVisible ? translate('visible_element') : translate('show_element'),
					icon: 'eye',
					action: () => {
						element.toggleVisibility();
					},
					append: `${controllKey}+H`,
					cssClasses: 'znpb-menu-item--separator-bottom',
				},
				{
					title: translate('wrap_with_container'),
					icon: 'eye',
					action: () => {
						wrapInContainer(element);
					},
					append: `${controllKey}+H`,
					cssClasses: 'znpb-menu-item--separator-bottom',
				},
				{
					title: translate('discard_element_styles'),
					icon: 'drop',
					action: () => {
						discardElementStyles(element);
					},
					show: element && Object.keys(get(element.options, '_styles', {})).length > 0,
				},
				{
					title: translate('delete_element'),
					icon: 'delete',
					action: () => element.delete(),
					append: `⌦`,
				},
			];
		});

		watch(activeElementMenu, newValue => {
			if (newValue) {
				addEventListener('scroll', hideElementMenu);
			} else {
				removeEventListener('scroll', hideElementMenu);
			}
		});

		function discardElementStyles(element) {
			element.options = {
				...element.options,
				_styles: {},
			};
		}

		function saveElement(element) {
			const { showSaveElement } = useSaveTemplate();

			showSaveElement(element, 'block');
		}

		const hasCopiedElement = computed(() => {
			return !!(copiedElement.value.element || getData('copiedElement'));
		});

		const hasCopiedElementClasses = computed(() => {
			return !!getData('copiedElementClasses');
		});

		const hasCopiedElementStyles = computed(() => {
			return !!getData('copiedElementStyles');
		});

		return {
			showOptions,
			activeElementMenu,
			hideElementMenu,
			elementActions,
			hasCopiedElement,
			hasCopiedElementClasses,
			hasCopiedElementStyles,
		};
	},
};
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
		box-shadow: 0 0 16px 0 rgba(0, 0, 0, 0.08);
		border-radius: 3px;
		transition: all 0.5s;
		user-select: none;

		li {
			min-width: 172px;
			padding: 12px 29px;
			color: var(--zb-surface-text-color);
			font-size: 12px;
			line-height: 14px;
			transition: color 0.2s ease;
			&:hover {
				color: var(--zb-surface-text-active-color);
				cursor: pointer;
			}
		}
	}
}

.list-enter-to,
.list-leave-from {
	transition: all 0.2s;
}

.list-enter-from,
.list-leave-to {
	transform: translateY(10%);
	opacity: 0;
}

.znpb-rightClickMenu__Tooltip {
	padding: 0;
}
</style>
