path = r'C:\Users\Miguel\Desktop\VocesCriticas\frontend\src\pages\GroupDetail.jsx'

with open(path, 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace(
    'className={w-4 h-4 transition-transform }',
    'className={`w-4 h-4 transition-transform ${showTeacherSub ? \'-rotate-90\' : \'\'}`}'
)

with open(path, 'w', encoding='utf-8') as f:
    f.write(text)

print("Replaced chevron!")