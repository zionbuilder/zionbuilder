
import { InjectionComponentsManager } from '@/common/components/injections'
import ServerRequest from '../serverRequest'

window.zb.injections = new InjectionComponentsManager()
window.zb.ajax = new ServerRequest()
