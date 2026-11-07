      localStorage.setItem(notesKey, JSON.stringify(notes));
      byId('asyncNotesSaveStatus').textContent = 'All changes saved';
    } catch (_) {
      byId('asyncNotesSaveStatus').textContent = 'Storage is full or unavailable; copy your note to keep it.';
    }
  }
  function refreshNoteList() {
    const select = byId('asyncNotesList');
    select.innerHTML = '';
    notes.forEach(note => {
      const option = document.createElement('option');
      option.value = note.id; option.textContent = note.title || 'Untitled note';
      select.appendChild(option);
    });
    if (activeNoteId) select.value = activeNoteId;
  }
  function saveActiveNote() {
    if (!quill || !activeNoteId) return;
    const note = notes.find(item => item.id === activeNoteId);
    if (!note) return;
    note.title = byId('asyncNoteTitle').value.trim() || 'Untitled note';
    note.html = quill.root.innerHTML;
    note.delta = quill.getContents();
    note.updatedAt = Date.now();
    persistNotes(); refreshNoteList();
  }
  function selectNote(id) {
    activeNoteId = id;
    const note = notes.find(item => item.id === id);
    if (!note) return;
    byId('asyncNoteTitle').value = note.title === 'Untitled note' ? '' : note.title;
    if (note.delta) quill.setContents(note.delta);
    else quill.root.innerHTML = note.html || '';
    refreshNoteList();
  }
  function createNote() {
    const note = { id: (window.crypto && crypto.randomUUID) ? crypto.randomUUID() : String(Date.now()) + Math.random(), title: 'Untitled note', html: '', delta: null, updatedAt: Date.now() };
    notes.unshift(note); activeNoteId = note.id; persistNotes(); refreshNoteList();
    byId('asyncNoteTitle').value = ''; quill.setText('');
    byId('asyncNoteTitle').focus();
  }
  function initializeNotes() {
    quill = new Quill('#asyncQuillEditor', {
      theme: 'snow',
      placeholder: 'Write your class notes here…',
      modules: { toolbar: [['bold','italic','underline','strike'], [{header:[1,2,3,false]}], [{list:'ordered'},{list:'bullet'}], ['blockquote','code-block'], [{color:[]},{background:[]}], ['link'], ['clean']] }
    });
    notes = readNotes();
    if (!notes.length) createNote();
    else { notes.sort((a,b) => (b.updatedAt || 0) - (a.updatedAt || 0)); selectNote(notes[0].id); }
    quill.on('text-change', () => {
      byId('asyncNotesSaveStatus').textContent = 'Saving…';
      clearTimeout(saveTimer); saveTimer = setTimeout(saveActiveNote, 350);
    });
    byId('asyncNoteTitle').addEventListener('input', () => {
      clearTimeout(saveTimer); saveTimer = setTimeout(saveActiveNote, 350);
    });
    byId('asyncNotesList').addEventListener('change', e => { const selectedId = e.target.value; saveActiveNote(); selectNote(selectedId); });
    byId('asyncNoteNew').addEventListener('click', () => { saveActiveNote(); createNote(); });
    byId('asyncNoteDelete').addEventListener('click', () => {
      if (!activeNoteId || !confirm('Delete this saved note? This cannot be undone.')) return;
      notes = notes.filter(note => note.id !== activeNoteId); activeNoteId = null;
      if (!notes.length) createNote(); else selectNote(notes[0].id);
      persistNotes();
    });
    window.addEventListener('beforeunload', saveActiveNote);
  }

  // Scientific calculator powered by math.js (safe expression parser; no eval).
  const display = byId('asyncCalcDisplay'), angle = byId('asyncCalcAngle'), calcStatus = byId('asyncCalcStatus');
  let expression = '';
  function showExpression() { display.value = expression || '0'; }
  function insert(value) { expression += value; showExpression(); display.focus(); }
  function calculate() {
    if (!expression.trim()) return;
    if (!window.math) { calcStatus.textContent = 'Calculator engine did not load. Check your connection.'; return; }
    try {
      const toRad = x => angle.value === 'deg' ? x * Math.PI / 180 : x;
      const fromRad = x => angle.value === 'deg' ? x * 180 / Math.PI : x;
      const scope = {
        sin: x => Math.sin(toRad(x)), cos: x => Math.cos(toRad(x)), tan: x => Math.tan(toRad(x)),
        asin: x => fromRad(Math.asin(x)), acos: x => fromRad(Math.acos(x)), atan: x => fromRad(Math.atan(x)),
        sqrt: Math.sqrt, log: Math.log10, ln: Math.log, pi: Math.PI, e: Math.E
      };
      const result = math.evaluate(expression, scope);
      if (typeof result !== 'number' || !Number.isFinite(result)) throw new Error('Result is not a finite real number.');
      expression = String(Number(result.toPrecision(12)));
      showExpression(); calcStatus.textContent = 'Calculated successfully.';
    } catch (error) { calcStatus.textContent = error.message || 'Invalid expression.'; }
  }
  byId('asyncCalcKeys').addEventListener('click', event => {
    const button = event.target.closest('button'); if (!button) return;
    const action = button.dataset.action, value = button.dataset.insert;
    if (action === 'clear') { expression = ''; calcStatus.textContent = 'Cleared.'; }
    else if (action === 'back') expression = expression.slice(0, -1);
    else if (action === 'equals') { calculate(); return; }
    else if (value) expression += value;
    showExpression();
  });
  display.addEventListener('input', () => { expression = display.value; });
  display.addEventListener('keydown', event => {
    if (event.key === 'Enter') { event.preventDefault(); calculate(); }
    if (event.key === 'Escape') { expression = ''; showExpression(); }
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape') { notesPanel.classList.remove('is-open'); calcPanel.classList.remove('is-open'); }
  });
})();
</script>
