const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const erdDir = path.resolve('Blueprint/erd');
const mmdFiles = fs.readdirSync(erdDir).filter(f => f.endsWith('.mmd'));

console.log(`Found ${mmdFiles.length} Mermaid files to compile in ${erdDir}`);

mmdFiles.forEach(file => {
    const baseName = path.basename(file, '.mmd');
    const inputMmd = path.join(erdDir, file);
    const outputPng = path.join(erdDir, `${baseName}.png`);

    console.log(`\nCompiling ${file} -> ${baseName}.png...`);
    const cmd = `cmd /c npx -y @mermaid-js/mermaid-cli -i "${inputMmd}" -o "${outputPng}" -b "#ffffff" -s 3`;
    try {
        execSync(cmd, { stdio: 'inherit' });
        console.log(`[SUCCESS] Generated ${baseName}.png (${fs.statSync(outputPng).size} bytes)`);
    } catch (err) {
        console.error(`[ERROR] Failed to compile ${file}:`, err.message);
    }
});

console.log('\nAll ERD diagrams compilation finished!');
