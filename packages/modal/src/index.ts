import { App } from 'vue'
import Modal from './components/Modal.vue'
import ModalConfirm from './components/ModalConfirm.vue'
import ModalTemplateSaveButton from './components/ModalTemplateSaveButton.vue'
const install = (app: App) => {
	app.component('modal', Modal)
	app.component('modal', ModalConfirm)
	app.component('modal', ModalTemplateSaveButton)
}

export {
	Modal,
	ModalConfirm,
	ModalTemplateSaveButton,
	install
}