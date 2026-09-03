const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const flowchartDir = path.resolve('Blueprint/flowchart');
const mmdFiles = fs.readdirSync(flowchartDir).filter(f => f.endsWith('.mmd'));

console.log(`Found ${mmdFiles.length} Mermaid Flowchart files to compile in ${flowchartDir}`);

mmdFiles.forEach(file => {
    const baseName = path.basename(file, '.mmd');
    const inputMmd = path.join(flowchartDir, file);
    const outputPng = path.join(flowchartDir, `${baseName}.png`);

    console.log(`\nCompiling ${file} -> ${baseName}.png...`);
    const cmd = `cmd /c npx -y @mermaid-js/mermaid-cli -i "${inputMmd}" -o "${outputPng}" -b "#ffffff" -s 2`;
    try {
        execSync(cmd, { stdio: 'inherit' });
        console.log(`[SUCCESS] Generated ${baseName}.png (${fs.statSync(outputPng).size} bytes)`);
    } catch (err) {
        console.error(`[ERROR] Failed to compile ${file}:`, err.message);
    }
});

console.log('\nAll Flowchart diagrams compilation finished!');
