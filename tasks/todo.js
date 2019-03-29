#!/usr/bin/env node
/*eslint no-console: "off"*/

const {spawnSync} = require('child_process');

spawnSync(
  'leasot',
  ['-x', './**/*.{ts,js,vue,php}', '--ignore', './node_modules,./vendor', '--tags', 'review', '>', 'TODO.md'],
  {stdio: 'inherit'}
);
