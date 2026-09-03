const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const prdDir = path.resolve('Blueprint/prd');
const mmdFiles = fs.readdirSync(prdDir).filter(f => f.endsWith('.mmd'));

console.log(`Found ${mmdFiles.length} Mermaid PRD files to compile in ${prdDir}`);

mmdFiles.forEach(file => {
    const baseName = path.basename(file, '.mmd');
    const inputMmd = path.join(prdDir, file);
    const outputPng = path.join(prdDir, `${baseName}.png`);

    console.log(`\nCompiling ${file} -> ${baseName}.png...`);
    const cmd = `cmd /c npx -y @mermaid-js/mermaid-cli -i "${inputMmd}" -o "${outputPng}" -b "#ffffff" -s 2`;
    try {
        execSync(cmd, { stdio: 'inherit' });
        console.log(`[SUCCESS] Generated ${baseName}.png (${fs.statSync(outputPng).size} bytes)`);
    } catch (err) {
        console.error(`[ERROR] Failed to compile ${file}:`, err.message);
    }
});

console.log('\nAll PRD diagrams compilation finished!');
