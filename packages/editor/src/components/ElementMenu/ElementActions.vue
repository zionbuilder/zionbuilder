<template>
	<ul
		class="znpb-right-click__menu"
		@click.stop="close"
	>
		<li
			class="znpb-right-click__menu-item"
			@click="editElement(element)"
		>
			<Icon
				icon="edit"
				:bgSize="14"
			></Icon>
			{{$translate('action_edit')}}
		</li>
		<li class="znpb-right-click__menu-separator"></li>
		<li
			class="znpb-right-click__menu-item"
			@click="element.duplicate"
		>
			<Icon
				icon="copy"
				:bgSize="14"
			></Icon>
			{{$translate('duplicate_element')}}
		</li>

		<li
			class="znpb-right-click__menu-item"
			@click="copyElement(element)"
		>
			<Icon
				icon="copy"
				:bgSize="14"
			></Icon>
			{{$translate('copy_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="copyElement(element, 'cut')"
		>
			<Icon
				icon="close"
				:bgSize="14"
			></Icon>
			{{$translate('cut_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="pasteElement(element)"
			v-if="copiedElement"
		>
			<Icon
				icon="copy"
				:bgSize="14"
			></Icon>
			{{$translate('paste_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="element.rename()"
			v-if="actions.rename"
		>
			<Icon
				icon="edit"
				:bgSize="14"
			></Icon>
			{{$translate('rename_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="element.toggleVisibility"
		>
			<Icon
				icon="eye"
				:bgSize="14"
			></Icon>
			{{element.isVisible ? $translate('visible_element') : $translate('show_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="copyElementStyles(element)"
			v-if="element && element.options._styles"
		>
			<Icon
				icon="drop"
				:bgSize="14"
			></Icon>
			{{$translate('copy_element_styles')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="discardElementStyles"
			v-if="element && element.options._styles"
		>
			<Icon
				icon="drop"
				:bgSize="14"
			></Icon>
			{{$translate('discard_element_styles')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="pasteElementStyles(this.element)"
			v-if="copiedElementStyles"
		>
			<Icon
				icon="drop"
				:bgSize="14"
			></Icon>
			{{$translate('paste_element_styles')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="copyElementClasses(this.element)"
			v-if="computedHasElementClasses"
		>
			<Icon
				icon="braces"
				:bgSize="14"
			></Icon>
			{{$translate('copy_classes')}}
		</li>
		<li
			v-if="copiedElementClasses"
			class="znpb-right-click__menu-item"
			@click="pasteElementClasses(this.element)"
		>
			<Icon
				icon="braces"
				:bgSize="14"
			></Icon>
			{{$translate('paste_classes')}}
		</li>
		<li class="znpb-right-click__menu-separator">

		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="saveElement"
		>
			<Icon
				icon="check"
				:bgSize="14"
			></Icon>
			{{$translate('save_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="savePage"
		>
			<Icon
				icon="check"
				:bgSize="14"
			></Icon>
			{{$translate('save_page')}}
		</li>
		<li class="znpb-right-click__menu-separator"></li>
		<li
			class="znpb-right-click__menu-item"
			@click="element.delete"
		>
			<Icon
				icon="delete"
				:bgSize="14"
			></Icon>
			{{$translate('delete_element')}}
		</li>
	</ul>
</template>

<script>
import { computed } from 'vue'
import { get } from 'lodash-es'
import { trigger } from '@zb/hooks'
import {
	useSavePage,
	usePanels,
	useEditElement,
	useElementActions,
	useSaveTemplate
} from '@composables'

export default {
	name: 'ElementActions',
	props: {
		actions: {
			type: Object,
			required: false
		},
		element: {
			type: Object,
			required: true
		}
	},
	setup (props) {
		const { openPanel } = usePanels()

		const { savePage } = useSavePage()
		const { editElement } = useEditElement()
		const {
			focusedElement,
			copyElement,
			pasteElement,
			copiedElement,
			resetCopiedElement,
			copyElementStyles,
			pasteElementStyles,
			copiedElementStyles,
			copyElementClasses,
			pasteElementClasses,
			copiedElementClasses
		} = useElementActions()

		const actions = {
			rename: true,
			...props.actions
		}

		const computedHasElementClasses = computed(() => {
			return get(props.element.options, '_styles.wrapper.classes')
		})

		return {
			copyElementStyles,
			pasteElementStyles,
			copiedElementStyles,
			savePage,
			openPanel,
			editElement,
			focusedElement,
			copyElement,
			pasteElement,
			copiedElement,
			resetCopiedElement,
			actions,
			copyElementClasses,
			computedHasElementClasses,
			copiedElementClasses,
			pasteElementClasses
		}
	},
	methods: {
		discardElementStyles () {
			this.element.options = {
				...this.element.options,
				_styles: {}
			}
			this.close()
		},
		saveElement () {
			const { showSaveElement } = useSaveTemplate()

			showSaveElement(this.element, 'block')

			this.close()
		},
		close () {
			this.$emit('close', false)
		}

	}
}
</script>

<style lang="scss">
.znpb-right-click__menu {
	min-width: 200px;
	padding: 0;
	margin: 0;
	color: $font-color;
	font-size: 14px;
	list-style-type: none;
	background-color: $surface;
	box-shadow: 0 5px 10px 0 rgba(164, 164, 164, .1);
	border-radius: 3px;
	outline: 1px solid $surface-variant;
}
.znpb-right-click__menu-item {
	display: flex;
	align-items: center;
	padding: 8px 16px;
	font-family: $font-stack;
	font-size: 13px;
	font-weight: 500;
	line-height: 20px;
	transition: all .1s ease;
	cursor: pointer;

	&:first-child {
		padding-top: 13px;
	}
	&:last-child {
		padding-bottom: 13px;
	}

	&:hover {
		color: $surface-active-color;
	}

	& > span {
		margin-right: 5px;
	}
}
.znpb-right-click__menu-separator {
	height: 1px;
	margin: 5px 0;
	border-top: 1px solid $surface-variant;
}
</style>
