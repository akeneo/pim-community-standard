const read = require('read-pkg').sync;
const yarn = require('yarn-api');

const package = read('./vendor/akeneo/pim-community-dev');
const packages = [];

for(dep in package.dependencies) {
    packages.push(`${dep}@${package.dependencies[dep]}`)
}

yarn(['add'].concat(packages).concat(['--ignore-scripts']), err => {
    if (err) throw err;
});
