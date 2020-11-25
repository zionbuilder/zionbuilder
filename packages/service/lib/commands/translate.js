module.exports = (options, args) => {
    const service = process.ZIONBUILDER_SERVICE
    const { name } = service.pkg
    const wpPot = require('wp-pot')

    const config = {
        slug: name,
        packageName: name.replace("-", " "),
        reportBugsUrl: '',
        team: '',
        ...service.options.getOption('l10n')
    }

    return new Promise((resolve, reject) => {
        try {
            wpPot({
                destFile: `./languages/${config.packageName}.pot`,
                domain: config.domain,
                package: config.package,
                team: config.team,
				bugReport: config.bugReport,
				headers: {
                    "X-Domain": config.domain
				}
            });

            resolve()
        } catch (err) {
            reject(err)
        }
    })
}
