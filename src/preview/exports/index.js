import ScriptsLoader from '@/preview/utils/ScriptsLoader'

window.zb = window.zb || {}

window.zb.preview = {
	scripts: new ScriptsLoader()
}

window.zb.editor = window.parent.zb.editor
