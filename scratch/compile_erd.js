const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const erdMdPath = path.join(__dirname, '../Blueprint/Entity_Relationship_Diagram_Kost.md');
const erdOutputDir = path.join(__dirname, '../Blueprint/erd');

if (!fs.existsSync(erdOutputDir)) {
    fs.mkdirSync(erdOutputDir, { recursive: true });
}

const content = fs.readFileSync(erdMdPath, 'utf8');

// We want to extract the main ERD diagram (first erDiagram in section 1)
const mainMermaidMatch = content.match(/```mermaid\s*\n([\s\S]*?)\n```/);

if (mainMermaidMatch) {
    const mainMermaid = mainMermaidMatch[1];
    const tmpMmd = path.join(__dirname, 'tmp_diagram_erd.mmd');
    const outPng = path.join(erdOutputDir, 'diagram_erd.png');
    
    fs.writeFileSync(tmpMmd, mainMermaid, 'utf8');
    console.log('Rendering diagram_erd.png...');
    try {
        execSync(`npx -y @mermaid-js/mermaid-cli -i "${tmpMmd}" -o "${outPng}" -s 3 -b "#ffffff"`, { stdio: 'inherit' });
        console.log('Successfully rendered diagram_erd.png!');
    } catch (err) {
        console.error('Error rendering diagram_erd.png:', err);
    } finally {
        if (fs.existsSync(tmpMmd)) fs.unlinkSync(tmpMmd);
    }
}

// Extract flowcharts in section 4
const flowcharts = [
    { name: 'alur_reservasi_pembayaran.png', regex: /### 4\.1[\s\S]*?```mermaid\s*\n([\s\S]*?)\n```/ },
    { name: 'alur_siklus_billing.png', regex: /### 4\.2[\s\S]*?```mermaid\s*\n([\s\S]*?)\n```/ },
    { name: 'alur_pengaduan_keluhan.png', regex: /### 4\.3[\s\S]*?```mermaid\s*\n([\s\S]*?)\n```/ },
    { name: 'alur_integrasi_arus_kas.png', regex: /### 4\.4[\s\S]*?```mermaid\s*\n([\s\S]*?)\n```/ }
];

flowcharts.forEach(({ name, regex }) => {
    const match = content.match(regex);
    if (match) {
        const mmdContent = match[1];
        const tmpMmd = path.join(__dirname, `tmp_${name}.mmd`);
        const outPng = path.join(erdOutputDir, name);

        fs.writeFileSync(tmpMmd, mmdContent, 'utf8');
        console.log(`Rendering ${name}...`);
        try {
            execSync(`npx -y @mermaid-js/mermaid-cli -i "${tmpMmd}" -o "${outPng}" -s 3 -b "#ffffff"`, { stdio: 'inherit' });
            console.log(`Successfully rendered ${name}!`);
        } catch (err) {
            console.error(`Error rendering ${name}:`, err);
        } finally {
            if (fs.existsSync(tmpMmd)) fs.unlinkSync(tmpMmd);
        }
    }
});
