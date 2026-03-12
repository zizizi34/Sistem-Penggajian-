import os
import re

def fix_blade_errors(directory):
    pattern = re.compile(r"@error\(\s*(['\"][^'\"]*['\"])\s*,\s*['\"][^'\"]*['\"]\s*\)")
    for root, dirs, files in os.walk(directory):
        for file in files:
            if file.endswith('.blade.php'):
                path = os.path.join(root, file)
                with open(path, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                
                new_content = pattern.sub(r"@error(\1)", content)
                
                if content != new_content:
                    with open(path, 'w', encoding='utf-8') as f:
                        f.write(new_content)
                    print(f"Fixed: {path}")

if __name__ == "__main__":
    fix_blade_errors('resources/views')
