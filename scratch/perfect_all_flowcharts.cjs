const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const flowchartDir = path.resolve('Blueprint/flowchart');
const mmdFiles = fs.readdirSync(flowchartDir).filter(f => f.endsWith('.mmd'));

console.log(`Processing and compiling ${mmdFiles.length} Mermaid Flowchart files...`);

mmdFiles.forEach(file => {
    const inputMmd = path.join(flowchartDir, file);
    let content = fs.readFileSync(inputMmd, 'utf-8');

    // Replace problematic internal characters
    const lines = content.split('\n');
    let inTheme = false;
    const cleanedLines = lines.map(line => {
        if (line.includes('%%{init:')) inTheme = true;
        if (inTheme) {
            if (line.includes('}}%%')) inTheme = false;
            return line;
        }

        // Replace internal parenthesis inside node brackets/diamonds without touching Terminator ([...])
        // e.g., [[Auth::login(user)]] -> [[Auth::login user]]
        // e.g., -- "(Ya / Sukses)" --> -> -- "Ya - Sukses" -->
        let l = line;
        l = l.replace(/\{id\}/g, ':id');
        l = l.replace(/'/g, '');
        l = l.replace(/Auth::login\([^)]*\)/g, 'Auth::login user');
        l = l.replace(/Cache::forget\([^)]*\)/g, 'Cache::forget fasilitas_all');
        l = l.replace(/NOW\(\)/g, 'NOW');
        l = l.replace(/lockForUpdate\(\)/g, 'lockForUpdate');
        l = l.replace(/firstOrCreate\(\)/g, 'firstOrCreate');
        l = l.replace(/\(Batal \/ Denied\)/g, '- Batal / Denied');
        l = l.replace(/\(Sukses\)/g, '- Sukses');
        l = l.replace(/\(0 Result\)/g, '- 0 Result');
        l = l.replace(/\(user\)/g, 'user');
        l = l.replace(/\(Cash \/ Transfer Bank\)/g, '- Cash / Transfer Bank');
        l = l.replace(/\(Online - Midtrans Snap\)/g, '- Online Midtrans Snap');
        l = l.replace(/\(Bulan Berjalan\)/g, '- Bulan Berjalan');
        l = l.replace(/\(Bulan Berikutnya\)/g, '- Bulan Berikutnya');
        l = l.replace(/\(Biaya <= Deposit\)/g, '- Biaya <= Deposit');
        l = l.replace(/\(Biaya > Deposit\)/g, '- Biaya > Deposit');
        l = l.replace(/\(Login Pertama\)/g, '- Login Pertama');
        l = l.replace(/\(Login Selanjutnya\)/g, '- Login Selanjutnya');
        l = l.replace(/\(Menunggu Pembayaran\)/g, '- Menunggu Pembayaran');

        return l;
    });

    content = cleanedLines.join('\n');
    fs.writeFileSync(inputMmd, content, 'utf-8');

    const baseName = path.basename(file, '.mmd');
    const outputPng = path.join(flowchartDir, `${baseName}.png`);

    console.log(`Compiling ${file} -> ${baseName}.png...`);
    const cmd = `cmd /c npx -y @mermaid-js/mermaid-cli -i "${inputMmd}" -o "${outputPng}" -b "#ffffff" -s 2`;
    try {
        execSync(cmd, { stdio: 'inherit' });
        console.log(`[SUCCESS] Generated ${baseName}.png (${fs.statSync(outputPng).size} bytes)`);
    } catch (err) {
        console.error(`[ERROR] Failed to compile ${file}:`, err.message);
    }
});

console.log('\nAll 23 Flowchart diagrams compilation finalized!');
