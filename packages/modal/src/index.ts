import { App } from 'vue'
import Modal from './components/Modal.vue'
import ModalConfirm from './components/ModalConfirm.vue'

const install = (app: App) => {
	app.component('modal', Modal)
	app.component('modal', ModalConfirm)
}

export {
	Modal,
	ModalConfirm,
	install
}