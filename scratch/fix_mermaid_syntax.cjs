const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const flowchartDir = path.resolve('Blueprint/flowchart');
const mmdFiles = fs.readdirSync(flowchartDir).filter(f => f.endsWith('.mmd'));

console.log(`Fixing and compiling ${mmdFiles.length} Mermaid Flowchart files...`);

mmdFiles.forEach(file => {
    const inputMmd = path.join(flowchartDir, file);
    let content = fs.readFileSync(inputMmd, 'utf-8');

    // Safe replacements for Mermaid tokens inside node labels
    content = content.replace(/\{id\}/g, ':id');
    content = content.replace(/NOW\(\)/g, 'NOW');
    content = content.replace(/lockForUpdate\(\)/g, 'lockForUpdate');
    content = content.replace(/firstOrCreate\(\)/g, 'firstOrCreate');
    content = content.replace(/Auth::login\([^)]+\)/g, 'Auth::login(user)');
    content = content.replace(/Cache::forget\([^)]+\)/g, 'Cache::forget fasilitas_all');
    content = content.replace(/hash\([^)]+\)/g, "hash sha512 payload");

    // Remove stray single quotes inside node content (preserve init block)
    const lines = content.split('\n');
    let inTheme = false;
    const fixedLines = lines.map(line => {
        if (line.includes('%%{init:')) inTheme = true;
        if (inTheme) {
            if (line.includes('}}%%')) inTheme = false;
            return line;
        }
        return line.replace(/'/g, '');
    });

    content = fixedLines.join('\n');
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

console.log('\nAll Flowchart compilation finished!');
