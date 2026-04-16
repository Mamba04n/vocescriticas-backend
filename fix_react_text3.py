with open('frontend/src/pages/GroupDetail.jsx', 'r', encoding='utf-8') as f: content = f.read()
bad_str = 'mySubmission.user_id !== user.id ? "Subido por tu equipo (${mySubmission.user?.name || "Companero"}). " : "Trabajo Entregado. "'
good_str = 'mySubmission.user_id !== user.id ? \'Subido por tu equipo (${mySubmission.user?.name || "Companero"}). \' : \'Trabajo Entregado. \''
content = content.replace(bad_str, good_str)
with open('frontend/src/pages/GroupDetail.jsx', 'w', encoding='utf-8') as f: f.write(content)
