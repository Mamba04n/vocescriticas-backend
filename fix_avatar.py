path = r'C:\Users\Miguel\Desktop\VocesCriticas\frontend\src\pages\GroupDetail.jsx'

with open(path, 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace(
    'src={https://ui-avatars.com/api/?name=&background=f3f4f6} className="w-10 h-10 rounded-full"',
    'src={sub.user?.avatar_url ? `http://localhost:8000/storage/${sub.user.avatar_url}` : `https://ui-avatars.com/api/?name=${sub.user?.name}&background=f3f4f6`} className="w-10 h-10 rounded-full object-cover"'
)

with open(path, 'w', encoding='utf-8') as f:
    f.write(text)

print("Replaced avatars!")