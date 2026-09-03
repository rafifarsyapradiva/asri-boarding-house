const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const flowchartDir = path.resolve('Blueprint/flowchart');
const mmdFiles = fs.readdirSync(flowchartDir).filter(f => f.endsWith('.mmd'));

console.log(`Auditing and compiling ${mmdFiles.length} Mermaid files in ${flowchartDir}`);

mmdFiles.forEach(file => {
    const inputMmd = path.join(flowchartDir, file);
    let content = fs.readFileSync(inputMmd, 'utf-8');

    // Replace single quotes inside node labels (but preserve init theme block)
    const lines = content.split('\n');
    let inTheme = false;
    const newLines = lines.map(line => {
        if (line.includes('%%{init:')) inTheme = true;
        if (inTheme) {
            if (line.includes('}}%%')) inTheme = false;
            return line;
        }
        // Remove single quotes inside labels, e.g. 'terisi' -> terisi, 'maintenance' -> maintenance
        return line.replace(/'([^']+)'/g, '$1');
    });

    content = newLines.join('\n');
    fs.writeFileSync(inputMmd, content, 'utf-8');

    const baseName = path.basename(file, '.mmd');
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

console.log('\nAll compilation attempts finished.');
