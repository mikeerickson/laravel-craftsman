#!/usr/bin/env node
/* global require */

const fs = require('fs')
const colors = require('colors')

const pkgInfo = require('../package.json')

let currBuild = parseInt(pkgInfo.build)

currBuild++

pkgInfo.build = currBuild.toString()

fs.writeFileSync('./package.json', JSON.stringify(pkgInfo, null, 2))

// important, do not add anything other than build number as it supplies
// return value which is used in the calling script (unless --verbose flag is supplied)

if (process.argv.includes('--verbose')) {
  let versionStr = `v${pkgInfo.version} build ${currBuild}`
  console.log('\nUpdated To: ' + colors.cyan(versionStr))
} else {
  console.log(currBuild)
}
