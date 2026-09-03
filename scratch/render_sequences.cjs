const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const sequenceDir = path.join(__dirname, '..', 'Blueprint', 'sequence');
const files = fs.readdirSync(sequenceDir).filter(file => file.endsWith('.mmd'));

console.log(`Found ${files.length} .mmd files in ${sequenceDir}`);

let successCount = 0;
let failCount = 0;

for (const file of files) {
    const inputFile = path.join(sequenceDir, file);
    const outputFile = path.join(sequenceDir, file.replace('.mmd', '.png'));
    process.stdout.write(`Rendering: ${file} -> ${path.basename(outputFile)}... `);
    try {
        execSync(`npx --yes @mermaid-js/mermaid-cli -i "${inputFile}" -o "${outputFile}" -b "#FFFFFF" -s 2`, {
            stdio: 'pipe',
            shell: 'cmd.exe'
        });
        console.log(`✓ OK`);
        successCount++;
    } catch (err) {
        console.log(`✗ FAILED`);
        console.error(err.message);
        if (err.stdout) console.error('stdout:', err.stdout.toString());
        if (err.stderr) console.error('stderr:', err.stderr.toString());
        failCount++;
    }
}

console.log(`\n========================================`);
console.log(`Rendering Complete: ${successCount} succeeded, ${failCount} failed.`);
console.log(`========================================`);
