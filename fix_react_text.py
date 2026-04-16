with open('frontend/src/pages/GroupDetail.jsx', 'r', encoding='utf-8') as f: content = f.read()
content = content.replace('<div><p className="text-sm font-bold text-amber-800">Trabajo Entregado.', '<div><p className="text-sm font-bold text-amber-800">{mySubmission.user_id !== user.id ? "Subido por tu equipo (${mySubmission.user?.name || "Companero"}). " : "Trabajo Entregado. "}')
with open('frontend/src/pages/GroupDetail.jsx', 'w', encoding='utf-8') as f: f.write(content)
