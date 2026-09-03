const fs = require('fs');
const path = require('path');

const filePath = path.join(__dirname, '..', 'Blueprint', 'Sequence_Diagram_Kost.md');
const content = fs.readFileSync(filePath, 'utf-8');
const lines = content.split('\n');

console.log('--- Image links in Sequence_Diagram_Kost.md ---');
lines.forEach((line, idx) => {
    if (line.includes('![')) {
        console.log(`Line ${idx + 1}: ${line}`);
    }
    if (line.startsWith('### 2.')) {
        console.log(`Heading Line ${idx + 1}: ${line}`);
    }
});
