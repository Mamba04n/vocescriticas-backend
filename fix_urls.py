import re

path = r'C:\Users\Miguel\Desktop\VocesCriticas\frontend\src\pages\GroupDetail.jsx'

with open(path, 'r', encoding='utf-8') as f:
    text = f.read()

# Replace the first `href={http://localhost:8000/storage/\}` (which is in the `unit` context)
text = text.replace(
    r"""<a href={http://localhost:8000/storage/\} target="_blank" rel="noreferrer" className="w-full md:w-auto text-center px-4 py-2 bg-white border border-blue-200 text-blue-700 text-sm font-bold rounded-xl shadow-sm hover:bg-blue-50 transition">""",
    """<a href={`http://localhost:8000/storage/${unit.file_path}`} target="_blank" rel="noreferrer" className="w-full md:w-auto text-center px-4 py-2 bg-white border border-blue-200 text-blue-700 text-sm font-bold rounded-xl shadow-sm hover:bg-blue-50 transition">"""
)

# Replace the second one (which is in the `sub` context, probably `sub.file_path`)
text = text.replace(
    r"""<a href={http://localhost:8000/storage/\} target="_blank" rel="noreferrer" className="text-xs text-blue-600 font-bold hover:underline mt-1 inline-block">Ver Documento Entregado</a>""",
    """<a href={`http://localhost:8000/storage/${sub.file_path}`} target="_blank" rel="noreferrer" className="text-xs text-blue-600 font-bold hover:underline mt-1 inline-block">Ver Documento Entregado</a>"""
)

with open(path, 'w', encoding='utf-8', newline='') as f:
    f.write(text)

print("Replaced storage strings!")