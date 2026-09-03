import fs from 'fs';
import path from 'path';
import { execSync } from 'child_process';

const mdPath = path.resolve('Blueprint/Sequence_Diagram_Kost.md');
const baseDir = path.resolve('Blueprint');

const content = fs.readFileSync(mdPath, 'utf8');

// Regex to find image links followed by mermaid blocks
// e.g. ![Visual Sequence Diagram 1](sequence/seq_diagram_1.png)
// ```mermaid
// sequenceDiagram
// ...
// ```
const regex = /!\[.*?\]\((sequence\/.*?\.png)\)\s*[\r\n]+\s*```mermaid\s*([\s\S]*?)\s*```/g;

let match;
const matches = [];
while ((match = regex.exec(content)) !== null) {
    matches.push({
        relativeImgPath: match[1],
        mermaidCode: match[2]
    });
}

console.log(`Found ${matches.length} sequence diagrams to compile.`);

for (const item of matches) {
    const { relativeImgPath, mermaidCode } = item;
    const targetImgPath = path.join(baseDir, relativeImgPath);
    
    // Ensure parent directory exists
    const dir = path.dirname(targetImgPath);
    if (!fs.existsSync(dir)) {
        fs.mkdirSync(dir, { recursive: true });
    }

    console.log(`Compiling: ${relativeImgPath} -> ${targetImgPath}`);
    
    // Create temp mmd file
    const tempMmdPath = path.resolve(`scratch/temp_${path.basename(relativeImgPath, '.png')}.mmd`);
    fs.writeFileSync(tempMmdPath, mermaidCode, 'utf8');
    
    try {
        // Compile using mmdc (solid white background for high contrast, and 3x scale for crisp/high-res text)
        const cmd = `cmd /c npx mmdc -i "${tempMmdPath}" -o "${targetImgPath}" -b "#ffffff" -s 3`;
        console.log(`Running: ${cmd}`);
        execSync(cmd, { stdio: 'inherit' });
        console.log(`Successfully generated ${targetImgPath}`);
    } catch (err) {
        console.error(`Error generating diagram for ${relativeImgPath}:`, err);
    } finally {
        // Cleanup temp file
        if (fs.existsSync(tempMmdPath)) {
            fs.unlinkSync(tempMmdPath);
        }
    }
}

console.log('All sequence diagrams compiled successfully!');
