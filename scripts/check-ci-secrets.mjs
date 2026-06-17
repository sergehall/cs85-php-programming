import { readdir, readFile } from 'node:fs/promises';
import { join } from 'node:path';

const workflowDirectory = '.github/workflows';
const forbiddenPatterns = [
    {
        label: 'APP_KEY',
        pattern: /^\s*APP_KEY\s*[:=]/m,
    },
];

async function workflowFiles(directory) {
    const entries = await readdir(directory, { withFileTypes: true });

    return entries
        .filter((entry) => entry.isFile() && /\.(ya?ml)$/u.test(entry.name))
        .map((entry) => join(directory, entry.name));
}

const files = await workflowFiles(workflowDirectory);
const violations = [];

for (const file of files) {
    const content = await readFile(file, 'utf8');

    for (const forbiddenPattern of forbiddenPatterns) {
        if (forbiddenPattern.pattern.test(content)) {
            violations.push(`${file}: hardcoded ${forbiddenPattern.label} is not allowed`);
        }
    }
}

if (violations.length > 0) {
    console.error(violations.join('\n'));
    process.exit(1);
}

console.log('CI workflow secret guard passed.');
