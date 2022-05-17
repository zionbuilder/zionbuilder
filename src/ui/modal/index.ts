import { App } from 'vue'
import Modal from './components/Modal.vue'
import ModalConfirm from './components/ModalConfirm.vue'
import ModalTemplateSaveButton from './components/ModalTemplateSaveButton.vue'
const install = (app: App) => {
	app.component('Modal', Modal)
	app.component('ModalConfirm', ModalConfirm)
	app.component('ModalTemplateSaveButton', ModalTemplateSaveButton)
}

export {
	Modal,
	ModalConfirm,
	ModalTemplateSaveButton,
	install
}