#!/usr/bin/env node

const fs = require('fs');

let pkgInfo = require('../package.json');

let currBuild = parseInt(pkgInfo.build);

currBuild++;

pkgInfo.build = currBuild.toString();

fs.writeFileSync('./package.json', JSON.stringify(pkgInfo, null, 2));

console.log(currBuild);
