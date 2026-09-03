import fs from 'fs';
import path from 'path';

const mdPath = path.resolve('Blueprint/Activity_Diagram_Kost.md');
let content = fs.readFileSync(mdPath, 'utf8');

// The styling block to inject
const styleBlock = `%%{init: {
  'theme': 'base',
  'themeVariables': {
    'primaryColor': '#FFFBEB',
    'primaryTextColor': '#000000',
    'primaryBorderColor': '#000000',
    'lineColor': '#000000',
    'secondaryColor': '#FDE047',
    'tertiaryColor': '#FFFFFF',
    'edgeLabelBackground': '#FFFFFF',
    'fontSize': '12px',
    'fontFamily': 'Inter, system-ui, sans-serif'
  }
}}%%
`;

// Clean up existing styling blocks if they exist (to avoid duplication if run multiple times)
content = content.replace(/```mermaid\s*%%\{init:[\s\S]*?\}%%\s*/g, '```mermaid\n');

// Inject the styling block before 'flowchart TD'
content = content.replace(/```mermaid\s*[\r\n]+\s*flowchart TD/g, '```mermaid\n' + styleBlock + 'flowchart TD');

fs.writeFileSync(mdPath, content, 'utf8');
console.log('Successfully injected premium styling blocks into all Activity Diagrams in Activity_Diagram_Kost.md!');
