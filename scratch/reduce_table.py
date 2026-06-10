import re
import os

filepath = 'resources/views/admin/data-siswa.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. table class
content = content.replace('min-w-[1200px]', '')

# 2. thead text size
content = content.replace('text-[11px] text-gray-400', 'text-[10px] text-gray-400')

# 3. tbody text size
content = content.replace('tbody class="text-sm', 'tbody class="text-xs')

# 4. table paddings inside the table container
start_idx = content.find('<table')
end_idx = content.find('</table>') + 8

table_html = content[start_idx:end_idx]

# Reduce paddings
table_html = table_html.replace('px-6 py-4', 'px-3 py-2.5')
table_html = table_html.replace('px-4 py-4', 'px-2 py-2.5')
table_html = table_html.replace('px-3 py-4', 'px-1 py-2.5')
table_html = table_html.replace('px-3 py-1.5', 'px-2 py-1') # for buttons
table_html = table_html.replace('w-3.5 h-3.5', 'w-3 h-3')
table_html = table_html.replace('text-base', 'text-sm') # diff score size

content = content[:start_idx] + table_html + content[end_idx:]

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)

print("Done")
