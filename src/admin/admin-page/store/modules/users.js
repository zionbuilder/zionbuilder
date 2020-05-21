import * as types from '../mutation-types'
import Vue from 'vue'
import { getUsersById } from '@/api/Users'

const state = {
	users: [],
	loaded: false
}

const getters = {
	getUsers: state => state.users,
	getUserById: state => (id) => {
		return state.users.find((user) => {
			return user.id === id
		})
	}
}

const actions = {
	fetchUsersById: ({ commit, state }, payload) => {
		if (!state.loaded) {
			return getUsersById(payload).then((response) => {
				commit(types.SET_USERS, response.data)
				commit(types.SET_USERS_LOADED_STATE, true)
			})
		}
	},
	addUserData: ({ commit }, payload) => {
		commit(types.ADD_USER_DATA, payload)
	}
}

const mutations = {
	[types.SET_USERS] (state, payload) {
		Vue.set(state, 'users', payload)
	},
	[types.SET_USERS_LOADED_STATE] (state, payload) {
		Vue.set(state, 'loaded', payload)
	},
	[types.ADD_USER_DATA] (state, payload) {
		state.users.push(payload)
	}
}

export default {
	state,
	getters,
	actions,
	mutations
}
