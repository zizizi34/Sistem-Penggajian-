import os
import subprocess

def lint_php_files(directory):
    error_count = 0
    for root, dirs, files in os.walk(directory):
        for file in files:
            if file.endswith('.php') and not file.endswith('.blade.php'):
                path = os.path.join(root, file)
                result = subprocess.run(['php', '-l', path], capture_output=True, text=True)
                if result.returncode != 0:
                    print(result.stdout.strip())
                    print(result.stderr.strip())
                    error_count += 1
    print(f"Total PHP syntax errors: {error_count}")

if __name__ == "__main__":
    lint_php_files('app')
    lint_php_files('database')
    lint_php_files('routes')
