<template>
	<ul
		class="znpb-right-click__menu"
		@click.stop=""
	>
		<li
			class="znpb-right-click__menu-item"
			@click="editElement"
		>
			<BaseIcon icon="edit"></BaseIcon>
			{{$translate('action_edit')}}
		</li>
		<li class="znpb-right-click__menu-separator"></li>
		<li
			class="znpb-right-click__menu-item"
			@click="duplicateElement"
		>
			<BaseIcon icon="copy"></BaseIcon>
			{{$translate('duplicate_element')}}
		</li>

		<li
			class="znpb-right-click__menu-item"
			@click="copyElementAction"
		>
			<BaseIcon icon="copy"></BaseIcon>
			{{$translate('copy_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="cutElementAction"
		>
			<BaseIcon icon="close"></BaseIcon>
			{{$translate('cut_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="pasteElementAction"
			v-if="getCopiedElement || getCuttedElement"
		>
			<BaseIcon icon="copy"></BaseIcon>
			{{$translate('paste_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="triggerRename"
			v-if="showRename"
		>
			<BaseIcon icon="edit"></BaseIcon>
			{{$translate('rename_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="toggleVisibility"
		>
			<BaseIcon icon="eye"></BaseIcon>
			{{this.getElementOptionValue(this.getElementFocus.uid, '_isVisible', true) ? $translate('visible_element') : $translate('show_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="copyElementStyles"
			v-if="data && data.options._styles"
		>
			<BaseIcon icon="drop"></BaseIcon>
			{{$translate('copy_element_styles')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="discardElementStyles"
			v-if="data && data.options._styles"
		>
			<BaseIcon icon="drop"></BaseIcon>
			{{$translate('discard_element_styles')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="pasteElementStyles"
			v-if="getCopiedElementStyles"
		>
			<BaseIcon icon="drop"></BaseIcon>
			{{$translate('paste_element_styles')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="copyElementClasses"
			v-if="data && data.options._classes"
		>
			<BaseIcon icon="braces"></BaseIcon>
			{{$translate('copy_classes')}}
		</li>
		<li
			v-if="getCopiedClasses"
			class="znpb-right-click__menu-item"
			@click="pasteElementClasses"
		>
			<BaseIcon icon="braces"></BaseIcon>
			{{$translate('paste_classes')}}
		</li>
		<li class="znpb-right-click__menu-separator">

		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="saveElement"
		>
			<BaseIcon icon="check"></BaseIcon>
			{{$translate('save_element')}}
		</li>
		<li
			class="znpb-right-click__menu-item"
			@click="savePageAction"
		>
			<BaseIcon icon="check"></BaseIcon>
			{{$translate('save_draft')}}
		</li>
		<li class="znpb-right-click__menu-separator"></li>
		<li
			class="znpb-right-click__menu-item"
			@click="deleteElementMenu"
		>
			<BaseIcon icon="delete"></BaseIcon>
			{{$translate('delete_element')}}
		</li>
	</ul>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { SET_LOCKED_USERINFO } from '../store/mutation-types'
import { trigger } from '@zb/hooks'

export default {
	name: 'ElementActions',
	props: {
		showRename: {
			type: Boolean,
			required: false,
			default: true
		}
	},
	data () {
		return {}
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
			'deleteElement',
			'updateElementOptionValue',
			'openPanel',
			'setCuttedElement',
			'savePage',
			'addNotice',
			'moveElement'
		]),
		triggerRename () {
			this.setActiveElement(this.getElementFocus.uid)
			this.$emit('changename', true)
		},
		toggleVisibility () {
			this.updateElementOptionValue({
				elementUid: this.getElementFocus.uid,
				path: '_isVisible',
				newValue: !this.getElementOptionValue(this.getElementFocus.uid, '_isVisible', true),
				type: 'visibility'
			})
			this.close()
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
			this.openPanel('PanelElementOptions')
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
		duplicateElement () {
			this.copyElement({
				elementUid: this.getElementFocus.uid,
				parentUid: this.getElementFocus.parentUid,
				insertParent: this.getElementFocus.parentUid
			})
			this.close()
		},
		deleteElementMenu () {
			this.deleteElement({
				elementUid: this.getElementFocus.uid,
				parentUid: this.getElementFocus.parentUid
			})

			this.close()
		},
		saveElement () {
			this.close()
			trigger('save-element', {
				elementUid: this.data.uid,
				parentUid: this.getElementFocus.parentUid
			})
		},
		savePageAction () {
			this.savePage({
				status: 'autosave'
			}).catch(error => {
				this.addNotice({
					message: error.message,
					type: 'error',
					delayClose: 5000
				})
			}).finally(() => {
				this.addNotice({
					message: this.$translate('page_saved'),
					delayClose: 5000
				})
				this.close()
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
	position: fixed;
	z-index: 9999;
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
