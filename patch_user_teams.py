with open('frontend/src/pages/GroupDetail.jsx', 'r', encoding='utf-8') as f: content = f.read()
content = content.replace('<p className="text-sm font-bold text-slate-800 leading-none">{sub.user?.name}</p>', '<p className="text-sm font-bold text-slate-800 leading-none">{sub.user?.name} <span className="text-xs text-blue-600 ml-1">{sub.user?.teams?.length > 0 ? (\'\' + sub.user.teams[0].name + \'\') : \'\'}</span></p>')
with open('frontend/src/pages/GroupDetail.jsx', 'w', encoding='utf-8') as f: f.write(content)
