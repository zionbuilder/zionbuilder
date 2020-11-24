module.exports = class Options {
    constructor (options = {}) {
        this.options = options
    }

    setOptions (options) {
        this.options = options
    }

    setOption (optionId, optionValue) {
        this.options[optionId] = optionValue
    }


    getOptions() {
        return this.options
    }

    getOption (optionPath, defaultValue) {
        const option = this.options
        const pathArray = optionPath.split('.')
        const pathLength = pathArray.length
    
        let returnValue = defaultValue
        let activeValue = option
    
        for (let [index, pathItem] of pathArray.entries()) {
            if (typeof activeValue[pathItem] === 'undefined') {
                break
            }
    
            if (index === pathLength - 1) {
                returnValue = activeValue[pathItem]
            }
    
            activeValue = activeValue[pathItem]
        }
    
        return returnValue
    }
}