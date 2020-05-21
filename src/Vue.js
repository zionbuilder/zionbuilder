// import Vue from '../node_modules/vue/dist/vue.common.js'
let Vue

if (typeof window.parent.ZionBuilderApi !== 'undefined') {
	Vue = window.parent.ZionBuilderApi.Vue
} else {
	Vue = require('../node_modules/vue/dist/vue.runtime.esm.js')
}

module.exports = Vue
