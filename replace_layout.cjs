const fs = require('fs');
const path = require('path');

const directoryPath = path.join(__dirname, 'resources', 'js', 'Pages');

function searchFiles(dir) {
    let files = [];
    const items = fs.readdirSync(dir);
    for (const item of items) {
        const fullPath = path.join(dir, item);
        const stat = fs.statSync(fullPath);
        // Exclude Public folder as it uses PublicLayout, Admin folder, Auth folder
        if (stat.isDirectory()) {
            if (!['Public', 'Admin', 'Auth', 'Company'].includes(item)) {
                files = files.concat(searchFiles(fullPath));
            }
        } else if (fullPath.endsWith('.vue')) {
            files.push(fullPath);
        }
    }
    return files;
}

const vueFiles = searchFiles(directoryPath);

for (const file of vueFiles) {
    let content = fs.readFileSync(file, 'utf8');
    
    // Check if it imports TopNavbar
    if (content.includes("import TopNavbar from '@/Components/TopNavbar.vue';")) {
        
        // 1. Replace import
        content = content.replace(
            "import TopNavbar from '@/Components/TopNavbar.vue';",
            "import AppLayout from '@/Layouts/AppLayout.vue';"
        );

        // 2. Replace the template opening
        // Match <div class="min-h-screen ..."> followed by <TopNavbar />
        const openingRegex = /<div\s+class="[^"]*min-h-screen[^"]*"\s*>[\s\S]*?<TopNavbar\s*(?:class="[^"]*")?\s*\/>/g;
        if (openingRegex.test(content)) {
            content = content.replace(openingRegex, '<AppLayout>');
            
            // 3. Replace the very last </div> before </template> at the END of the file
            // We find the last index of </div> and replace it
            const lastTemplateIndex = content.lastIndexOf('</template>');
            const contentBeforeTemplate = content.substring(0, lastTemplateIndex);
            const lastDivIndex = contentBeforeTemplate.lastIndexOf('</div>');
            
            if (lastDivIndex !== -1) {
                const firstPart = content.substring(0, lastDivIndex);
                const afterDiv = content.substring(lastDivIndex + 6);
                content = firstPart + '</AppLayout>' + afterDiv;
            }
            
            fs.writeFileSync(file, content, 'utf8');
            console.log('Updated:', file);
        } else {
            console.log('Could not find opening div in:', file);
        }
    }
}
