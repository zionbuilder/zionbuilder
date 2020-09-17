import * as utils from '@/utils'
import { getDefaultGradientConfig } from '@/common/components/gradient'

window.zb = window.zb || {}

window.zb.utils = {
	...utils,
	getDefaultGradientConfig
}

// Export as module
export default window.zb.utils
