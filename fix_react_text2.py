with open('src/pages/GroupDetail.jsx', 'r', encoding='utf-8') as f: content = f.read()
content = content.replace('{mySubmission.user_id !== user.id ? "Subido por tu equipo (${mySubmission.user?.name || "Companero"}). " : "Trabajo Entregado. "}', '{(mySubmission.user_id !== user.id) ? '\'Subido por tu equipo () \'' : '\'Trabajo Entregado. \''}')
with open('src/pages/GroupDetail.jsx', 'w', encoding='utf-8') as f: f.write(content)
