import wpPot from 'wp-pot';

function translate() {
  const name = 'zionbuilder';

  const config = {
    slug: name,
    packageName: name.replace('-', ' '),
    reportBugsUrl: 'https://feedback.zionbuilder.io',
    locations: ['zionbuilder.php', 'includes'],
    domain: 'zionbuilder',
    package: 'Zionbuilder',
    bugReport: 'https://feedback.zionbuilder.io',
    team: 'ZionBuilder <hello@zionbuilder.io>',
  };

  return new Promise((resolve, reject) => {
    try {
      const aaa = wpPot({
        src: '**/edit-page.js',
        parser: 'js',
        parserOptions: {
          ecmaVersion: 14,
        },
        destFile: `./languages/${config.packageName}.pot`,
        // domain: config.domain,
        // package: config.package,
        // team: config.team,
        // bugReport: config.bugReport,
        // headers: {
        //   'X-Domain': config.domain,
        // },
      });

      console.log(aaa);

      resolve();
    } catch (err) {
      reject(err);
    }
  });
}

translate();
