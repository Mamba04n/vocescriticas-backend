with open('frontend/src/pages/GroupDetail.jsx', 'r', encoding='utf-8') as f: content = f.read()
repl = '{mySubmission.user_id !== user.id ? 'Trabajo entregado por tu equipo (${mySubmission.user?.name || 'Companero'}). ' : 'Trabajo Entregado. '}'
content = content.replace('Trabajo Entregado. Pendiente de calificación.', repl + 'Pendiente de calificación.')
with open('frontend/src/pages/GroupDetail.jsx', 'w', encoding='utf-8') as f: f.write(content)
