#!/usr/bin/env node

/* global require */

const fs = require('fs');

let pkgInfo = require('../package.json');

let currBuild = parseInt(pkgInfo.build);

currBuild++;

pkgInfo.build = currBuild.toString();

fs.writeFileSync('./package.json', JSON.stringify(pkgInfo, null, 2));

// important, do not add anything other than build number as it supplies
// return value which is used in the calling script
console.log(currBuild);
