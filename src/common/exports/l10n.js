/**
 * Localization
 */
import L10N from '@/common/plugins/l10n'

window.zb = window.zb || {}

// Instantiate the class
const LocalizationInstance = new L10N()

// Export to global object
window.zb.l10n = LocalizationInstance

// Export as module
export default LocalizationInstance
