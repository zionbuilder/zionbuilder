class Cache {
	static CACHE_KEY = 'znpb_page_cache'

	getCacheKey(postId: number) {
		const cacheKey = Cache.CACHE_KEY
		return `${cacheKey}-${postId}`
	}

	saveItem(postId: number, data: Object) {
		const key = this.getCacheKey(postId)
		localStorage.setItem(key, JSON.stringify(data))
	}

	getItem(postId: number) {
		const key = this.getCacheKey(postId)
		const storedValue = localStorage.getItem(key)

		if (storedValue !== null) {
			return JSON.parse(storedValue)
		}

		return false
	}

	deleteItem(postId: number) {
		const key = this.getCacheKey(postId)
		localStorage.removeItem(key)
	}
}

export default new Cache()
