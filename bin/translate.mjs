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
    bugReport: 'https://github.com/zionbuilder/zionbuilder/issues/new/choose',
    team: 'ZionBuilder <hello@zionbuilder.io>',
  };

  return new Promise((resolve, reject) => {
    try {
      wpPot({
        destFile: `./languages/${config.packageName}.pot`,
        domain: config.domain,
        package: config.package,
        team: config.team,
        bugReport: config.bugReport,
        headers: {
          'X-Domain': config.domain,
        },
      });

      resolve();
    } catch (err) {
      reject(err);
    }
  });
}

translate();
