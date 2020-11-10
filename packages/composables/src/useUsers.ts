import { ref } from 'vue'
import { getUsersById } from '@zionbuilder/rest'

const loadedUsers = ref([])

export const useUsers = () => {
	function fetchUsersData (userIDs) {
		return getUsersById(userIDs).then((response) => {
			if (Array.isArray(response.data)) {
				response.data.forEach(user => loadedUsers.value.push(user))
			}
		})
	}

	function addUser (user) {
		loadedUsers.value.push(user)
	}

	return {
		loadedUsers,
		fetchUsersData,
		addUser
	}
}