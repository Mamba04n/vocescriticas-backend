with open('frontend/src/pages/GroupDetail.jsx', 'r', encoding='utf-8') as f: content = f.read()
content = content.replace('const mySubmission = unit.submissions?.find(s => s.user_id === user.id);', 'const myTeamIds = userTeam ? userTeam.members.map(m => m.id) : [user.id];\n   const mySubmission = unit.submissions?.find(s => myTeamIds.includes(s.user_id));')
with open('frontend/src/pages/GroupDetail.jsx', 'w', encoding='utf-8') as f: f.write(content)
