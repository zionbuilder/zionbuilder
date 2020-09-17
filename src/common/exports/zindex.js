/**
 * ZIndexManager
 */
import ZindexManager from '@/common/ZindexManager'

// Instantiate the class
const ZindexManagerInstance = new ZindexManager()

window.zb = window.zb || {}

// Export to global object
window.zb.zindex = ZindexManagerInstance

// Export as module
export default ZindexManagerInstance
