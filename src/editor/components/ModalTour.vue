<template>
	<Modal
		:show-close="false"
		:show-maximize="false"
		:width="400"
		:show.sync="showTour"
		:show-backdrop="false"
		class="zion-tour--modal-backdrop"
		:position="modalPosition"
		append-to="#znpb-main-wrapper"
		:enable-drag="false"
	>

		<div class="znpb-about-modal znpb-fancy-scrollbar">

			<ModalStep
				:step="getActiveStep"
				:is-last-step="(steps.length-1) === activeIndex"
				@next-step="onNextStep"
				@end-tour="endTour"
			/>

		</div>
	</Modal>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { generateElements } from '@/utils/utils.js'
import ModalStep from './ModalStep'
import { Modal } from '@zb/components'

export default {
	name: 'ModalTour',
	components: {
		ModalStep,
		Modal
	},
	data () {
		return {
			modalPosition: null,
			showTour: true,
			activeIndex: 0,
			isInsideIframe: false,
			item: { 'element_type': 'zion_text', 'name': 'Text', 'category': 'content' },
			config: [{
				element_type: 'zion_column'
			}],
			steps: [
				{
					title: this.$translate('welcome_to_zion'),
					description: this.$translate('welcome_to_zion_description')
				},
				{
					title: this.$translate('adding_elements'),
					description: this.$translate('adding_elements_description'),
					selector: '.znpb-empty-placeholder__tour-icon',
					inIframe: true,
					delay: 300,
					actions: {
						click: ({ nextStep }) => {
							nextStep()
						}
					},
					onNext: () => {
						this.setActiveShowElementsPopup(this.lastElem)
					},
					beforeActive: () => {
						this.setActiveShowElementsPopup(this.lastElem)
					}
				},
				{

					title: this.$translate('elements_popup'),
					description: this.$translate('elements_popup_description'),
					selector: '.znpb-tabs__header-item--elements',
					inIframe: true,
					actions: {
						click ({ nextStep }) {
							nextStep()
						}
					},
					onNext: () => {
						this.setActiveShowElementsPopup(this.lastElem)
						window.ZionBuilderApi.trigger('change-tab-pop', 'elements')
					}
				},
				{
					title: this.$translate('elements_tab'),
					description: this.$translate('elements_tab_description'),
					selector: '.znpb-tabs__header-item--library',
					inIframe: true,
					actions: {
						click ({ nextStep }) {
							nextStep()
						}
					},
					onNext: () => {
						this.setActiveShowElementsPopup(this.lastElem)
						window.ZionBuilderApi.trigger('change-tab-pop', 'library')
					}
				},
				{
					title: this.$translate('columns_tab'),
					description: this.$translate('columns_tab_description'),
					selector: '.znpb-tabs__header-item--columns',
					inIframe: true,
					delay: 300,
					actions: {
						click ({ nextStep }) {
							nextStep()
						}
					},
					onNext: () => {
						this.setActiveShowElementsPopup(this.lastElem)
						window.ZionBuilderApi.trigger('change-tab-pop', 'columns')
					}
				},
				{
					title: this.$translate('add_template_to_page'),
					description: this.$translate('add_template_to_page_description'),
					selector: '.znpb-columns-templates__icons--full',
					inIframe: true,
					delay: 300,
					actions: {
						click ({ nextStep }) {
							nextStep()
						}
					},
					onNext: () => {
						this.setActiveShowElementsPopup(this.lastElem)
						this.insertElements({
							parentUid: 'contentRoot',
							index: 0,
							childElements: this.generateElements.childElements,
							parentElements: this.generateElements.parentElements
						})
						this.setActiveShowElementsPopup(null)
					}
				},
				{
					title: this.$translate('edit_element_options'),
					description: this.$translate('edit_element_options_description'),
					selector: '.znpb-empty-placeholder',
					inIframe: true,
					actions: {
						dblclick ({ nextStep }) {
							nextStep()
						}
					},
					onNext: () => {
						this.setActiveShowElementsPopup(null)
						this.setActiveElement(this.lastElem)
						this.openPanel('PanelElementOptions')
					}
				},
				{
					title: this.$translate('panel_element_options'),
					description: this.$translate('panel_element_options_description'),
					selector: '.znpb-tabs__header-item--styling',
					delay: 300,
					actions: {
						click ({ nextStep }) {
							nextStep()
						}
					},
					onNext: () => {
						window.ZionBuilderApi.trigger('change-tab-styling', 'styling')
					}
				},
				{
					title: this.$translate('styling_tab'),
					description: this.$translate('styling_tab_description'),
					selector: '.znpb-tabs__header-item--advanced',
					actions: {
						click ({ nextStep }) {
							nextStep()
						}
					},
					onNext: () => {
						window.ZionBuilderApi.trigger('change-tab-styling', 'advanced')
					}
				},

				{
					title: this.$translate('advanced_tab'),
					description: this.$translate('advanced_tab_description'),
					selector: '.znpb-panel__header-icon-close',
					actions: {
						click ({ nextStep, selectStep }) {
							nextStep()
						}
					},
					onNext: () => {
						this.closePanel('PanelElementOptions')
					}
				},
				{
					title: this.$translate('choose_device'),
					description: this.$translate('choose_device_description'),
					selector: '.znpb-editor-header-flyout',
					actions: {
						click ({ nextStep }) {
							nextStep()
						}
					}
				},
				{
					title: this.$translate('actions_history'),
					description: this.$translate('actions_history_description'),
					selector: '.znpb-editor-header__menu_button--history',
					actions: {
						click ({ nextStep, selectStep }) {
							nextStep()
						}
					},
					onNext: () => {
						this.openPanel('panel-history')
					}
				},
				{
					title: this.$translate('close_from_mainbar'),
					description: this.$translate('close_from_mainbar_description'),
					selector: '.znpb-editor-header__menu_button--history',
					actions: {
						click ({ nextStep, selectStep }) {
							nextStep()
						}
					},
					onNext: () => {
						this.closePanel('panel-history')
					}
				},
				{
					title: this.$translate('open_treeview_panel'),
					description: this.$translate('open_treeview_panel_description'),
					selector: '.znpb-editor-header__menu_button--treeview',
					actions: {
						click ({ nextStep, selectStep }) {
							nextStep()
						}
					},
					onNext: () => {
						this.openPanel('panel-tree')
					}
				},

				{
					title: this.$translate('save_your_work'),
					description: this.$translate('save_your_work_description'),
					selector: '.znpb-editor-header__page-save-wrapper--save',
					actions: {
						click ({ nextStep, selectStep }) {
							nextStep()
						}
					},
					onNext: () => {
						this.savePage({
							status: 'publish'
						})
					}
				},
				{
					title: this.$translate('tour_was_ended'),
					description: this.$translate('tour_was_ended_description')
				}
			]
		}
	},

	watch: {
		getActiveStep (step) {
			if (step !== undefined) {
				if (step.actions) {
					Object.keys(step.actions).forEach(actionTrigger => {
						const action = step.actions[actionTrigger]
						let stepSelector
						if (step.inIframe) {
							stepSelector = document.getElementById('znpb-editor-iframe').contentWindow.document.body.querySelectorAll(step.selector)
						} else {
							stepSelector = document.querySelectorAll(step.selector)
						}

						// Attach event
						const event = this.listenTo.bind(this, {
							actionTrigger,
							action,
							step,
							stepSelector,
							completeCallback () {
								stepSelector[0].removeEventListener(actionTrigger, event)
							}
						})

						if (stepSelector[0] !== undefined) {
							stepSelector[0].addEventListener(actionTrigger, event)
						}
					})
				}
			}
		}
	},
	created () {
		this.showTour = false
	},
	mounted () {
		const finishedTour = localStorage.getItem('zion_builder_guided_tour_done')

		this.showTour = !finishedTour
		if (this.showTour) {
			if (this.getPageContent['contentRoot'] !== undefined && this.getPageContent['contentRoot'].content) {
				this.showTour = false
			}
		}
	},
	computed: {
		...mapGetters([
			'getPageContent',
			'getOpenedPanels',
			'getIframePointerEvents'
		]),

		generateElements () {
			const config = [
				{
					element_type: 'zion_section',
					content: this.config
				}
			]
			const elements = generateElements(config)
			return elements
		},
		getUidParent () {
			return this.generateElements.parentElements[0]
		},
		getActiveStep () {
			return this.steps[this.activeIndex]
		},
		pageCont () {
			return this.getPageContent
		},
		lastElem () {
			let a = this.pageArray

			let i = a.findIndex(k => k === 'contentRoot')
			a.splice(i, 1)
			return (a[0] !== undefined) ? a[0] : 'contentRoot'
		},
		pageArray () {
			return Object.keys(this.pageCont)
		}
	},
	methods: {
		...mapActions([
			'setIframePointerEvents',
			'setMainBarPointerEvents',
			'setShouldOpenAddElementsPopup',
			'setActiveShowElementsPopup',
			'openPanel',
			'setActiveElement',
			'closePanel',
			'savePage',
			'insertElements',
			'deleteElement'
		]),

		show () {
			this.showTour = true
		},

		listenTo ({ actionTrigger, action, step, stepSelector, completeCallback }) {
			action({
				nextStep: this.nextStep,
				endTour: this.endTour
			})

			// remove the event listener
			completeCallback()
		},

		onNextStep () {
			if (this.getActiveStep.onNext) {
				this.getActiveStep.onNext()
			}

			this.nextStep()
		},

		nextStep () {
			const delay = this.getActiveStep.delay || 0
			setTimeout(() => {
				// this method is started only when the user wants next step
				if (this.getActiveStep.afterClose) {
					this.getActiveStep.afterClose()
				}

				this.activeIndex = this.activeIndex + 1
				if (this.activeIndex <= (this.steps.length - 1)) {
					if (this.getActiveStep.beforeActive) {
						this.getActiveStep.beforeActive()
					}

					this.removeClasses()
					this.removePointerEvents()

					if (this.isInsideIframe || this.StepInIframe(this.getActiveStep)) {
						this.$nextTick(() => this.initStep(this.getActiveStep))
					} else {
						this.initStep(this.getActiveStep)
					}
				} else {
					this.TourEnded()
				}
			}, delay)
		},
		StepInIframe (step) {
			let a = document.querySelectorAll(step['selector'])
			if (a.length === 0) {
				return true
			} else return false
		},
		positionModal (element, step) {
			if (element !== undefined) {
				let bound = element.getBoundingClientRect()

				let newLeft = (step['selector'] === '.znpb-editor-header-flyout' || step['selector'] === '.znpb-editor-header__page-save-wrapper--save' || step['selector'] === '.znpb-tabs__header-item--elements' || step['selector'] === '.znpb-tabs__header-item--columns') ? (bound.right + 180) : (bound.right + 60)
				let newTop = (step['selector'] === '.znpb-editor-header__page-save-wrapper--save') ? (bound.top - 240) : bound.top

				let newPosition = {
					left: newLeft,
					top: newTop
				}
				if (step['selector'] === '.znpb-empty-placeholder') {
					newPosition = null
				}
				this.modalPosition = newPosition
				// add class on the element
				element.classList.add('zion-tour')

				// set pointer events to none to constraint the user's actions
				element.style.pointerEvents = 'all'
			}
		},
		initStep (step) {
			let stepSelector
			if (step.inIframe) {
				this.isInsideIframe = true
				stepSelector = document.getElementById('znpb-editor-iframe').contentWindow.document.body.querySelectorAll(step.selector)
			} else {
				this.isInsideIframe = false
				stepSelector = document.querySelectorAll(step.selector)
			}

			// move the modal
			this.positionModal(stepSelector[0], step)

			this.setPointerEvents()
		},

		setPointerEvents () {
			if (this.isInsideIframe) {
				document.getElementById('znpb-editor-iframe').contentWindow.document.body.style.pointerEvents = 'none'
				// let popEl = document.getElementById('znpb-editor-iframe').contentWindow.document.getElementsByClassName('hg-popper')
				// if (popEl.length > 0) {
				// 	popEl[0].style.pointerEvents = 'none'
				// }
			} else this.setIframePointerEvents(true)

			this.setMainBarPointerEvents(true)
			if (this.getOpenedPanels.length > 0) {
				setTimeout(() => {
					// let panel = document.getElementsByClassName('znpb-editor-panel__container')
					// panel[0].style.pointerEvents = 'none'
				}, 300)
			}
		},
		removePointerEvents () {
			if (this.isInsideIframe) {
				document.getElementById('znpb-editor-iframe').contentWindow.document.body.style.pointerEvents = null
			}

			if (this.getIframePointerEvents) {
				this.setIframePointerEvents(false)
			}

			this.setMainBarPointerEvents(false)
			if (this.getOpenedPanels.length > 0) {
				// document.getElementsByClassName('znpb-editor-panel__container')[0].style.pointerEvents = null
			}
		},

		removeClasses () {
			let a = document.getElementsByClassName('zion-tour')
			if (a.length === 0) {
				a = document.getElementById('znpb-editor-iframe').contentWindow.document.body.getElementsByClassName('zion-tour')
			}
			if (a[0] !== undefined) {
				a[0].style.pointerEvents = null
				a[0].classList.remove('zion-tour')
			}
		},

		endTour () {
			this.TourEnded()
		},

		TourEnded () {
			this.modalPosition = null

			this.removePointerEvents()
			this.removeClasses()
			this.setActiveShowElementsPopup(null)
			this.closePanel('panel-history')
			this.closePanel('panel-tree')
			this.closePanel('PanelElementOptions')
			this.deleteAddedElement()
			this.showTour = false
			localStorage.setItem('zion_builder_guided_tour_done', true)
		},
		deleteAddedElement () {
			// delete added section

			if (this.pageCont.contentRoot.content.length > 0 && this.pageArray.includes(this.getUidParent)) {
				this.deleteElement({
					elementUid: this.getUidParent,
					parentUid: 'contentRoot'
				})
			}
		}
	}

}
</script>

<style lang="scss">
.zion-tour {
	position: relative;

	&--modal-backdrop {
		top: 50%;
		left: 50%;
		width: auto;
		height: auto;
		background: transparent;
		transform: translate(-50%, -50%);

// bug fix for Chrome blurry text

		-webkit-font-smoothing: subpixel-antialiased;
	}

	&:after {
		content: "" !important;
		position: absolute;
		top: 0;
		left: 0;
		z-index: 99999;
		display: block !important;
		width: 100%;
		height: 100%;
		border: 3px solid white;
		animation-duration: 1.5s;
		animation-iteration-count: infinite;
		animation-name: scaleInfinit;
		animation-timing-function: ease;
		pointer-events: none;
	}
	&.znpb-editor-tour-button {
		&:after {
			top: -2px;
			left: -3px;
			border-radius: 50%;
		}
	}

	&.znpb-tabs__header-item {
		&:after {
			border-color: $secondary;
		}
	}
	&.znpb-panel__header-icon-close {
		&:after {
			top: 14px;
			left: 8px;
			width: 24px;
			height: 24px;
			border-color: $secondary;
			border-radius: 50%;
		}
	}
	&.znpb-empty-placeholder__tour-icon {
		&:after {
			border-color: $secondary;
			border-radius: 50%;
		}
	}
	&.znpb-element-box.znpb-element-box--zion_text {
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
