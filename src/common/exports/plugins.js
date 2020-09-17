/**
 * Plugins
 */
import { Forms } from '@/common/components/forms'
import L10N from '@/common/plugins/l10n'

window.zb = window.zb || {}

// Instantiate the class
window.zb.plugins = {
	Forms,
	L10N
}

// Export as module
export default window.zb.plugins
