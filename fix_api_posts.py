import re

path = r'C:\Users\Miguel\Desktop\VocesCriticas\frontend\src\pages\GroupDetail.jsx'

with open(path, 'r', encoding='utf-8') as f:
    text = f.read()

# Replace first error
text = text.replace(
    r"await api.post(/evaluations/\/submissions, fd,",
    """await api.post(`/evaluations/${unit.id}/submissions`, fd,"""
)

# Replace second error
text = text.replace(
    r"await api.post(/submissions/\/grade, {",
    """await api.post(`/submissions/${subId}/grade`, {"""
)

with open(path, 'w', encoding='utf-8', newline='') as f:
    f.write(text)

print("Replaced!")