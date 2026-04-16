with open('frontend/src/pages/GroupDetail.jsx', 'r', encoding='utf-8') as f: content = f.read()
repl = '''<h1 className="text-2xl font-black text-gray-900 leading-tight">{group?.name}</h1>\n                        {userTeam && (\n                           <div className="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">\n                              <Users className="w-3.5 h-3.5 mr-1" />\n                              Equipo: {userTeam.name}\n                           </div>\n                        )}'''
content = content.replace('<h1 className="text-2xl font-black text-gray-900 leading-tight">{group?.name}</h1>', repl)
with open('frontend/src/pages/GroupDetail.jsx', 'w', encoding='utf-8') as f: f.write(content)
