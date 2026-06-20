const fs = require('fs');
const path = require('path');

const pagesDir = path.join(__dirname, '..', '..', 'resources', 'js', 'Pages');

function walkDir(dir, callback) {
    fs.readdirSync(dir).forEach(f => {
        let dirPath = path.join(dir, f);
        let isDirectory = fs.statSync(dirPath).isDirectory();
        isDirectory ? walkDir(dirPath, callback) : callback(path.join(dir, f));
    });
}

const navRegex = /<nav class="bg-white border-b border-slate-200">[\s\S]*?<\/nav>/;
const importRegex = /import TopNavbar from '@\/Components\/TopNavbar\.vue';/;
const scriptSetupRegex = /<script\s+setup[^>]*>/;

let updatedFiles = 0;

walkDir(pagesDir, (filePath) => {
    if (!filePath.endsWith('.vue')) return;
    
    // Skip TopNavbar itself just in case
    if (filePath.includes('TopNavbar.vue')) return;

    let content = fs.readFileSync(filePath, 'utf8');
    
    // Skip files that don't have the hardcoded nav
    if (!navRegex.test(content)) return;
    
    // Replace nav with TopNavbar
    content = content.replace(navRegex, '<TopNavbar />');
    
    // Also remove the "Navigation Header omitted for brevity..." comment if it exists
    content = content.replace(/<!-- Navigation Header omitted for brevity, you'd extract it to a Layout component in a real app -->\s*/g, '');
    
    // Insert import if needed
    if (!importRegex.test(content)) {
        content = content.replace(scriptSetupRegex, '$&\nimport TopNavbar from \'@/Components/TopNavbar.vue\';');
    }
    
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`Updated: ${filePath}`);
    updatedFiles++;
});

console.log(`Successfully updated ${updatedFiles} files.`);
