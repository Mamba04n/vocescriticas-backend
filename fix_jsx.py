import sys
import re

path = r'C:\Users\Miguel\Desktop\VocesCriticas\frontend\src\pages\GroupDetail.jsx'

with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Using regex to match from "Pestañas Navegación" comment down to the Avatar closing div
pattern = r"\{\/\*\s*Pestañas Navegación\s*\*\/\}.*?alt=\"Avatar\" className=\"w-full h-full object-cover\" \/>\s*<\/div>"

replacement = """{/* Pestañas Navegación */}
         <div className="flex border-t border-gray-100 mt-2">
            <button onClick={() => setActiveTab('feed')} className={`flex-1 py-3 text-sm font-bold text-center border-b-2 transition-colors ${activeTab === 'feed' ? 'border-blue-600 text-blue-700 bg-blue-50/50' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:text-gray-800'}`}>Muro de Publicaciones</button>
            <button onClick={() => setActiveTab('units')} className={`flex-1 py-3 text-sm font-bold text-center border-b-2 transition-colors ${activeTab === 'units' ? 'border-blue-600 text-blue-700 bg-blue-50/50' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:text-gray-800'}`}>Unidades y Calificaciones</button>
         </div>
      </div>

      {activeTab === 'feed' && (
        <div className="animate-in fade-in duration-300">
          <div className="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 shadow-sm mb-6">
            <div className="flex gap-3 mb-3">
              <div className="w-10 h-10 rounded-full overflow-hidden bg-gray-100 border border-gray-200 shrink-0">
                <img src={user?.avatar_url ? `http://localhost:8000/storage/${user.avatar_url}` : `https://ui-avatars.com/api/?name=${user?.name}&background=f3f4f6`} alt="Avatar" className="w-full h-full object-cover" />
              </div>"""

new_content = re.sub(pattern, replacement, content, flags=re.DOTALL)

with open(path, 'w', encoding='utf-8', newline='') as f:
    f.write(new_content)

print("Done")