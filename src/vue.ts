import * as Vue from '/@zb/vue';

if (!window.zb?.vue) {
	window.zb = window.zb || {};
	window.zb.vue = Vue;
}

export default window.zb.vue;
