import { execFileSync } from 'node:child_process';
import path from 'node:path';

const ignoredFiles = new Set(['composer.lock', 'package-lock.json']);

const prettierExtensions = new Set([
    '.css',
    '.js',
    '.json',
    '.md',
    '.mjs',
    '.webmanifest',
    '.yaml',
    '.yml',
]);

const prettierFilenames = new Set(['.prettierrc']);

const allFiles = execFileSync('git', ['ls-files', '--cached', '--others', '--exclude-standard'], {
    encoding: 'utf8',
})
    .split('\n')
    .filter((file) => file !== '' && !ignoredFiles.has(file));

const prettierFiles = allFiles
    .filter(
        (file) =>
            prettierExtensions.has(path.extname(file)) ||
            prettierFilenames.has(path.basename(file)),
    )
    .sort();

const phpFiles = allFiles
    .filter((file) => file.endsWith('.php') || file.endsWith('.blade.php'))
    .sort();

console.log(
    `Format audit: Prettier will cover ${prettierFiles.length} supported asset/config/doc files.`,
);
for (const file of prettierFiles) {
    console.log(`  prettier  ${file}`);
}

console.log(`Format audit: Laravel Pint will cover ${phpFiles.length} PHP/Blade files.`);
for (const file of phpFiles) {
    console.log(`  pint      ${file}`);
}
