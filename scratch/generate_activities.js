import fs from 'fs';
import path from 'path';
import { execSync } from 'child_process';

const mdPath = path.resolve('Blueprint/Activity_Diagram_Kost.md');
const baseDir = path.resolve('Blueprint');

const content = fs.readFileSync(mdPath, 'utf8');

// Regex to find image links followed by mermaid blocks
const regex = /!\[.*?\]\((activity\/.*?\.png)\)\s*[\r\n]+\s*```mermaid\s*([\s\S]*?)\s*```/g;

let match;
while ((match = regex.exec(content)) !== null) {
    const relativeImgPath = match[1];
    const mermaidCode = match[2];
    const targetImgPath = path.join(baseDir, relativeImgPath);
    
    // Check if the image file already exists. If it does, we can skip it to save time, or compile it anyway.
    // Let's compile it anyway or check if it's one of the 3 new ones to compile.
    // Let's compile all of them to make sure they are fully synchronized and updated!
    console.log(`Found diagram target: ${targetImgPath}`);
    
    // Create temp mmd file
    const tempMmdPath = path.resolve(`scratch/temp_${path.basename(relativeImgPath, '.png')}.mmd`);
    fs.writeFileSync(tempMmdPath, mermaidCode, 'utf8');
    
    console.log(`Writing temporary file: ${tempMmdPath}`);
    
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
console.log('Finished compiling activity diagrams!');
