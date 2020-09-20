
import { InjectionComponentsManager } from '@/common/components/injections'
import OptionsManager from '@/editor/manager/options'
import ServerRequest from '../serverRequest'

window.zb.injections = new InjectionComponentsManager()
window.zb.options = new OptionsManager()
window.zb.ajax = new ServerRequest()
