<template>
	<ul
		class="znpb-right-click__menu"
		@click.stop=""
	>
		<li
			class="znpb-right-click__menu-item"
			@click="editElement"
		>
			<Icon icon="edit"></Icon>
			{{$translate('action_edit')}}
		</li>
		<li class="znpb-right-click__menu-separator"></li>
		<li
			class="znpb-right-click__menu-item"
			@click="element.duplicate"
		>
			<Icon icon="copy"></Icon>
			{{$translate('duplicate_element')}}
		</li>

		<li
			class="znpb-right-click__menu-item"
			@click="copyElementAction"
		>
			<Icon icon="copy"></Icon>
			{{$translate('copy_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="cutElementAction"
		>
			<Icon icon="close"></Icon>
			{{$translate('cut_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="pasteElementAction"
			v-if="getCopiedElement || getCuttedElement"
		>
			<Icon icon="copy"></Icon>
			{{$translate('paste_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="element.activeElementRename = true"
			v-if="showRename"
		>
			<Icon icon="edit"></Icon>
			{{$translate('rename_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="element.toggleVisibility"
		>
			<Icon icon="eye"></Icon>
			{{element.isVisible ? $translate('visible_element') : $translate('show_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="copyElementStyles"
			v-if="copiedElementStyles"
		>
			<Icon icon="drop"></Icon>
			{{$translate('copy_element_styles')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="discardElementStyles"
			v-if="data && data.options._styles"
		>
			<Icon icon="drop"></Icon>
			{{$translate('discard_element_styles')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="pasteElementStyles(this.element)"
			v-if="copiedElementStyles"
		>
			<Icon icon="drop"></Icon>
			{{$translate('paste_element_styles')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="copyElementClasses"
			v-if="data && data.options._classes"
		>
			<Icon icon="braces"></Icon>
			{{$translate('copy_classes')}}
		</li>
		<li
			v-if="getCopiedClasses"
			class="znpb-right-click__menu-item"
			@click="pasteElementClasses"
		>
			<Icon icon="braces"></Icon>
			{{$translate('paste_classes')}}
		</li>
		<li class="znpb-right-click__menu-separator">

		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="saveElement"
		>
			<Icon icon="check"></Icon>
			{{$translate('save_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="savePage"
		>
			<Icon icon="check"></Icon>
			{{$translate('save_page')}}
		</li>
		<li class="znpb-right-click__menu-separator"></li>
		<li
			class="znpb-right-click__menu-item"
			@click="element.delete"
		>
			<Icon icon="delete"></Icon>
			{{$translate('delete_element')}}
		</li>
	</ul>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { trigger } from '@zb/hooks'
import { useCopyElementStyles, useSavePage } from '@data'
export default {
	name: 'ElementActions',
	props: {
		showRename: {
			type: Boolean,
			required: false,
			default: true
		},
		element: {
			type: Object,
			required: true
		}
	},
	setup () {
		const { copyElementStyles, pasteElementStyles, copiedElementStyles } = useCopyElementStyles()
		const { savePage } = useSavePage()

		return {
			copyElementStyles,
			pasteElementStyles,
			copiedElementStyles,
			savePage
		}
	},
	computed: {
		...mapGetters([
			'getPageContent',
			'getCopiedElement',
			'getCopiedElementStyles',
			'getCopiedClasses',
			'getElementFocus',
			'getElementOptionValue',
			'getCuttedElement',
			'getElementData',
			'getElementById'
		]),
		data () {
			return this.getPageContent[this.getElementFocus.uid]
		}
	},
	methods: {
		...mapActions([
			'copyElement',
			'updateElementOptions',
			'setCopiedClasses',
			'setCopiedElement',
			'setCopiedElementStyles',
			'setActiveElement',
			'updateElementOptionValue',
			'setCuttedElement',
			'savePage',
			'moveElement'
		]),
		triggerRename () {
			this.setActiveElement(this.getElementFocus.uid)
			this.$emit('changename', true)
		},
		copyElementClasses () {
			const elementClasses = this.data.options._classes
			this.setCopiedClasses(elementClasses)
			this.copy()
		},
		pasteElementClasses () {
			if (this.getCopiedClasses) {
				this.updateElementOptions({
					elementUid: this.getElementFocus.uid,
					values: {
						...this.data.options,
						_classes: this.getCopiedClasses
					}
				})
			}
			this.close()
		},
		copyElementAction () {
			this.setCopiedElement(this.getElementFocus.uid)
			this.setCuttedElement(null)
			this.close()
		},
		cutElementAction () {
			const elementId = this.getElementData(this.getElementFocus.uid).element_type
			this.setCuttedElement({
				uid: this.getElementFocus.uid,
				parentUid: this.getElementFocus.parentUid,
				insertParent: this.getElementFocus.insertParent,
				isWrapper: this.getElementById(elementId).wrapper
			})
			this.setCopiedElement(null)
			this.close()
		},
		pasteElementAction () {
			if (this.getCopiedElement) {
				this.copyElement({
					elementUid: this.getCopiedElement,
					parentUid: this.getElementFocus.parentUid,
					pasteElementUid: this.getElementFocus.uid,
					insertParent: this.getElementFocus.insertParent
				})
			}
			if (this.getCuttedElement) {
				const cuttedElement = this.getCuttedElement

				const newParent = this.getElementFocus.insertParent
				const parentContent = this.getElementData(newParent).content
				const newIndex = parentContent.indexOf(cuttedElement.uid) + 1

				this.moveElement({
					elementUid: cuttedElement.uid,
					oldParentUid: cuttedElement.parentUid,
					newParentUid: newParent,
					newIndex
				})

				this.setCuttedElement(null)
			}
			this.close()
		},
		editElement () {
			this.setActiveElement(this.getElementFocus.uid)
			this.$zb.panels.openPanel('PanelElementOptions')
			this.close()
		},
		copyElementStyles () {
			const copiedElementStyles = this.data.options._styles
			this.setCopiedElementStyles(copiedElementStyles)
			this.close()
		},
		discardElementStyles () {
			this.updateElementOptions({
				elementUid: this.getElementFocus.uid,
				values: {
					...this.data.options,
					_styles: {}
				}
			})
			this.close()
		},
		pasteElementStyles () {
			const copiedElementStyles = this.getCopiedElementStyles
			if (this.getCopiedElementStyles) {
				this.updateElementOptions({
					elementUid: this.getElementFocus.uid,
					values: {
						...this.data.options,
						_styles: copiedElementStyles
					}
				})
				this.close()
			}
		},
		saveElement () {
			this.close()
			trigger('save-element', {
				elementUid: this.data.uid,
				parentUid: this.getElementFocus.parentUid
			})
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
