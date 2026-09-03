import fs from 'fs';
import path from 'path';

const mdPath = path.resolve('Blueprint/Sequence_Diagram_Kost.md');
let content = fs.readFileSync(mdPath, 'utf8');

// The styling block for sequence diagrams
const seqStyleBlock = `%%{init: {
  'theme': 'base',
  'themeVariables': {
    'fontFamily': 'Inter, system-ui, sans-serif',
    'fontSize': '12px',
    'actorBkg': '#FFFBEB',
    'actorBorder': '#000000',
    'actorTextColor': '#000000',
    'actorLineColor': '#000000',
    'noteBkgColor': '#FFFBEB',
    'noteBorderColor': '#000000',
    'noteTextColor': '#000000',
    'signalColor': '#000000',
    'signalTextColor': '#000000',
    'activationBkgColor': '#FDE047',
    'activationBorderColor': '#000000',
    'loopLimitBorderColor': '#000000',
    'loopLimitBkgColor': '#FDE047',
    'labelBoxBorderColor': '#000000',
    'labelBoxBkgColor': '#FFFFFF',
    'labelTextTextColor': '#000000',
    'primaryColor': '#FFFBEB',
    'primaryTextColor': '#000000',
    'primaryBorderColor': '#000000',
    'lineColor': '#000000'
  }
}}%%
`;

// The styling block for flowcharts
const flowStyleBlock = `%%{init: {
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
content = content.replace(/```mermaid\s*[\r\n]*%%\{init:[\s\S]*?\}%%[\s\r\n]*/g, '```mermaid\n');

// Inject the sequence styling block before 'sequenceDiagram'
content = content.replace(/```mermaid\s*[\r\n]+\s*sequenceDiagram/g, '```mermaid\n' + seqStyleBlock + 'sequenceDiagram');

// Inject the flowchart styling block before 'flowchart TD'
content = content.replace(/```mermaid\s*[\r\n]+\s*flowchart TD/g, '```mermaid\n' + flowStyleBlock + 'flowchart TD');

fs.writeFileSync(mdPath, content, 'utf8');
console.log('Successfully injected Neo-Brutalist premium styling blocks into all diagrams in Sequence_Diagram_Kost.md!');
