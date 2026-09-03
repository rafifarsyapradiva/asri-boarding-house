const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const classDir = path.resolve('Blueprint/class');
const mmdFiles = fs.readdirSync(classDir).filter(f => f.endsWith('.mmd'));

console.log(`Found ${mmdFiles.length} Mermaid files to compile in ${classDir}`);

mmdFiles.forEach(file => {
    const baseName = path.basename(file, '.mmd');
    const inputMmd = path.join(classDir, file);
    const outputPng = path.join(classDir, `${baseName}.png`);

    console.log(`\nCompiling ${file} -> ${baseName}.png...`);
    const cmd = `cmd /c npx -y @mermaid-js/mermaid-cli -i "${inputMmd}" -o "${outputPng}" -b "#ffffff" -s 3`;
    try {
        execSync(cmd, { stdio: 'inherit' });
        console.log(`[SUCCESS] Generated ${baseName}.png (${fs.statSync(outputPng).size} bytes)`);
    } catch (err) {
        console.error(`[ERROR] Failed to compile ${file}:`, err.message);
    }
});

console.log('\nAll Class diagrams compilation finished!');
