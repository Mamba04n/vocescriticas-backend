import re

path = r"C:\Users\Miguel\Desktop\VocesCriticas\frontend\src\pages\GroupDetail.jsx"
with open(path, 'r', encoding='utf-8') as f:
    text = f.read()

# Add max grade state
text = re.sub(
    r"const \[unitDesc, setUnitDesc\] = useState\(''\);",
    "const [unitDesc, setUnitDesc] = useState('');\n  const [unitMaxGrade, setUnitMaxGrade] = useState(100);",
    text
)

# Append max grade to formdata
text = re.sub(
    r"fd\.append\('title', unitTitle\);",
    "fd.append('title', unitTitle);\n      fd.append('max_grade', unitMaxGrade);",
    text
)

# Clear max grade state after submit
text = re.sub(
    r"setUnitTitle\(''\);\n\s*setUnitDesc\(''\);",
    "setUnitTitle('');\n        setUnitDesc('');\n        setUnitMaxGrade(100);",
    text
)

# Add input for max grade in the UI form
text = re.sub(
    r"(<div>\s*<label className=\"block text-xs font-bold \n?text-gray-700 uppercase mb-1\">Instrucciones y DescripciÃ³n</label>)",
    """<div className="flex gap-4">
                                <div className="flex-1">
                                    <label className="block text-xs font-bold text-gray-700 uppercase mb-1">Calificación Máxima</label>
                                    <input type="number" min="0" step="0.01" value={unitMaxGrade} onChange={e=>setUnitMaxGrade(e.target.value)} required className="w-full border border-gray-300 rounded-xl px-4 py-2.5 outline-none focus:border-blue-500 text-sm shadow-sm" placeholder="Ej: 10 o 100" />
                                </div>
                             </div>
                             \\1""",
    text
)

# Adjust grading input to use the variable and max_grade validation
text = re.sub(
    r"(<input type=\"number\" min=\"0\"\s*max=\"100\" value=\{gradeInput\} onChange=\{e=>setGradeInput\(e\.target\.value\)\}\s*placeholder=\"\/100\" className=\"w-20 px-3 py-1 border border-slate-300 \n?rounded-lg text-sm font-bold\" \/>)",
    r"""<input type="number" min="0" step="0.01" max={unit.max_grade || 100} value={gradeInput} onChange={e=>setGradeInput(e.target.value)} placeholder={`/${unit.max_grade || 100}`} className="w-20 px-3 py-1 border border-slate-300 rounded-lg text-sm font-bold" />""",
    text,
    flags=re.MULTILINE
)

# Update validation alert inside handleGrade if out of bounds
text = re.sub(
    r"const handleGrade = async \(subId\) => \{\n\s*if\(!gradeInput\) return;\n\s*try \{",
    r"""const handleGrade = async (subId, maxGrade = 100) => {
      if(!gradeInput) return;
      if(parseFloat(gradeInput) < 0 || parseFloat(gradeInput) > maxGrade) {
          alert(`La calificación debe estar entre 0 y ${maxGrade}`);
          return;
      }
      try {""",
    text
)

# Update handleGrade invocation
text = re.sub(
    r"onClick=\{\(\)=>handleGrade\(sub\.id\)\}",
    r"onClick={()=>handleGrade(sub.id, unit.max_grade || 100)}",
    text
)

with open(path, 'w', encoding='utf-8') as f:
    f.write(text)

print("Updated frontend!")