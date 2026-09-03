const fs = require('fs');
const path = require('path');

const filePath = path.join(__dirname, '..', 'Blueprint', 'Blueprint_Projek_Web_Asri_Boarding_House.md');
const content = fs.readFileSync(filePath, 'utf-8');
const lines = content.split('\n');

console.log('Total lines:', lines.length);

const headings = [];
lines.forEach((line, idx) => {
    if (line.startsWith('# ') || line.startsWith('## ') || line.startsWith('### ')) {
        headings.push(`Line ${idx + 1}: ${line}`);
    }
});

console.log('Headings found:', headings.length);
console.log(headings.slice(0, 40).join('\n'));
