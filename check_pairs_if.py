import os

def check_blade_pairs(directory):
    for root, dirs, files in os.walk(directory):
        for file in files:
            if file.endswith('.blade.php'):
                path = os.path.join(root, file)
                with open(path, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                
                ifs = content.count('@if') - content.count('@elseif')
                endifs = content.count('@endif')
                
                if ifs != endifs:
                    print(f"Mismatch in {path}: @if={ifs}, @endif={endifs}")

if __name__ == "__main__":
    check_blade_pairs('resources/views')
