const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const targetDirs = [
    path.resolve('Blueprint/Skenario'),
    path.resolve('Blueprint/UjiCoba')
];

let totalSuccess = 0;
let totalFailed = 0;

targetDirs.forEach(dir => {
    if (!fs.existsSync(dir)) {
        console.warn(`Directory does not exist: ${dir}`);
        return;
    }

    const mmdFiles = fs.readdirSync(dir).filter(f => f.endsWith('.mmd'));
    console.log(`\n========================================`);
    console.log(`Processing directory: ${path.relative(process.cwd(), dir)} (${mmdFiles.length} files)`);
    console.log(`========================================`);

    mmdFiles.forEach(file => {
        const baseName = path.basename(file, '.mmd');
        const inputMmd = path.join(dir, file);
        const outputPng = path.join(dir, `${baseName}.png`);

        console.log(`\n[COMPILING] ${file} -> ${baseName}.png...`);
        const cmd = `cmd /c npx -y @mermaid-js/mermaid-cli -i "${inputMmd}" -o "${outputPng}" -b "#ffffff" -s 2`;
        try {
            execSync(cmd, { stdio: 'inherit' });
            if (fs.existsSync(outputPng)) {
                const stat = fs.statSync(outputPng);
                console.log(`[SUCCESS] Generated ${baseName}.png (${(stat.size / 1024).toFixed(1)} KB)`);
                totalSuccess++;
            } else {
                console.error(`[ERROR] Output file not found for ${file}`);
                totalFailed++;
            }
        } catch (err) {
            console.error(`[ERROR] Failed to compile ${file}:`, err.message);
            totalFailed++;
        }
    });
});

console.log(`\n========================================`);
console.log(`SUMMARY: ${totalSuccess} succeeded, ${totalFailed} failed.`);
console.log(`========================================\n`);

if (totalFailed > 0) {
    process.exit(1);
}
