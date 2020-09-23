import {createApp} from 'vue'

window.zb = window.zb || {}

window.zb.vue = window.parent.zb.vue || createApp()
