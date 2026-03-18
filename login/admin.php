<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin — Smart Maching</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
  <style>
    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    :root {
      --orange:#e67e22; --bg:#090c10; --bg2:#0d1117; --bg3:#0a0d12;
      --border:#161b22; --border2:#1e2530; --text:#f0f2f5; --dim:#2a3545;
    }
    body { background:var(--bg); color:var(--text); font-family:'Syne',sans-serif; overflow-x:hidden; display:flex; flex-direction:column; min-height:100vh; }
    ::-webkit-scrollbar{width:4px;} ::-webkit-scrollbar-track{background:var(--bg);} ::-webkit-scrollbar-thumb{background:#2a2f38;border-radius:2px;}
    input:focus,textarea:focus,select:focus { border-color:var(--orange)!important; outline:none; }
    @keyframes fadeIn{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}
    @keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

    /* ── HEADER ── */
    .adm-header { background:var(--bg3); border-bottom:1px solid var(--border); padding:0 40px; height:64px; display:flex; align-items:center; gap:16px; flex-shrink:0; }
    .adm-accent { width:4px; height:28px; background:var(--orange); border-radius:1px; }
    .adm-title-label { color:var(--orange); font-size:9px; font-weight:700; letter-spacing:4px; font-family:'Orbitron',sans-serif; }
    .adm-title { color:var(--text); font-size:20px; font-weight:900; font-family:'Barlow Condensed',sans-serif; letter-spacing:-.5px; line-height:1; }
    .adm-stats { display:flex; margin-left:auto; gap:0; }
    .adm-stat { text-align:center; padding:0 20px; border-left:1px solid var(--border); }
    .adm-stat-val { color:var(--orange); font-family:'Orbitron',sans-serif; font-size:20px; font-weight:700; }
    .adm-stat-key { color:var(--dim); font-size:9px; font-family:'Orbitron',sans-serif; letter-spacing:2px; }
    .adm-user { display:flex; align-items:center; gap:10px; padding-left:20px; border-left:1px solid var(--border); }
    .adm-user-name { color:var(--dim); font-size:11px; font-family:'Orbitron',sans-serif; letter-spacing:1px; }
    .btn-logout { background:transparent; border:1px solid var(--border2); color:#445870; padding:6px 14px; cursor:pointer; font-size:9px; font-family:'Orbitron',sans-serif; border-radius:2px; letter-spacing:1px; transition:all .15s; }
    .btn-logout:hover { border-color:#c0392b; color:#c0392b; }

    /* ── BODY ── */
    .adm-body { display:grid; grid-template-columns:260px 1fr; flex:1; min-height:0; }

    /* ── SIDEBAR ── */
    .adm-sidebar { background:var(--bg3); border-right:1px solid var(--border); padding:28px 20px; display:flex; flex-direction:column; gap:4px; overflow-y:auto; }
    .sidebar-label { color:#1e2530; font-size:9px; font-family:'Orbitron',sans-serif; letter-spacing:3px; margin:16px 0 8px; }
    .sidebar-btn { background:transparent; border:1px solid transparent; border-left:3px solid transparent; color:#445870; padding:11px 14px; cursor:pointer; border-radius:2px; font-family:'Orbitron',sans-serif; font-size:11px; font-weight:700; letter-spacing:1.5px; text-align:left; display:flex; align-items:center; gap:10px; transition:all .15s; width:100%; }
    .sidebar-btn:hover { background:#111520; color:#c0d0e0; }
    .sidebar-btn.active { background:#13171c; border-color:#1e2530; border-left-color:var(--orange); color:var(--text); }
    .sidebar-cat-row { display:flex; justify-content:space-between; align-items:center; padding:7px 4px; border-bottom:1px solid #0f1318; }
    .sidebar-cat-row span:first-child { color:#334455; font-size:11px; font-family:'Orbitron',sans-serif; }
    .sidebar-cat-count { color:var(--orange); font-size:11px; font-family:'Orbitron',sans-serif; font-weight:700; }
    .sidebar-db { background:var(--bg2); border:1px solid var(--border); border-radius:3px; padding:14px 16px; margin-top:auto; }
    .sidebar-db-title { color:#2a3545; font-size:9px; font-family:'Orbitron',sans-serif; letter-spacing:2px; margin-bottom:8px; }
    .db-row { display:flex; justify-content:space-between; padding:4px 0; }
    .db-key { color:#1e2530; font-size:9px; font-family:'Orbitron',sans-serif; }
    .db-val { color:#334455; font-size:9px; font-family:'Orbitron',sans-serif; }
    .db-online { display:flex; align-items:center; gap:6px; margin-bottom:8px; }
    .dot-green { width:6px; height:6px; border-radius:50%; background:#27ae60; flex-shrink:0; }
    .db-online span { color:#27ae60; font-size:10px; font-family:'Orbitron',sans-serif; }

    /* ── MAIN ── */
    .adm-main { padding:36px 40px; overflow-y:auto; }
    .adm-page { display:none; animation:slideUp .2s ease; }
    .adm-page.active { display:block; }
    .page-label { color:var(--orange); font-size:9px; font-family:'Orbitron',sans-serif; letter-spacing:3px; }
    .page-title { color:var(--text); font-family:'Barlow Condensed',sans-serif; font-size:32px; font-weight:900; letter-spacing:-1px; margin:4px 0 28px; }

    /* ── TABLE ── */
    .inv-toolbar { display:flex; align-items:center; gap:12px; margin-bottom:20px; }
    .inv-search { display:flex; align-items:center; gap:8px; background:var(--bg2); border:1px solid var(--border2); border-radius:2px; padding:8px 14px; flex:1; }
    .inv-search input { background:transparent; border:none; color:#c0d0e0; font-size:12px; font-family:'Orbitron',sans-serif; outline:none; width:100%; }
    .inv-search input::placeholder { color:var(--dim); }
    .btn-add { background:var(--orange); color:#fff; border:none; padding:11px 22px; font-size:11px; font-weight:700; letter-spacing:2px; cursor:pointer; font-family:'Orbitron',sans-serif; border-radius:2px; white-space:nowrap; }
    .btn-add:hover { opacity:.9; }
    .table-wrap { border:1px solid var(--border); border-radius:3px; overflow:hidden; }
    .table-head { display:grid; grid-template-columns:56px 1fr 120px 160px 70px 130px; background:var(--bg3); padding:10px 20px; border-bottom:1px solid var(--border); gap:12px; }
    .table-head span { color:#1e2530; font-size:9px; font-family:'Orbitron',sans-serif; letter-spacing:2px; font-weight:700; }
    .table-row { display:grid; grid-template-columns:56px 1fr 120px 160px 70px 130px; padding:12px 20px; gap:12px; align-items:center; background:var(--bg2); border-bottom:1px solid #0f1318; transition:background .15s; }
    .table-row:last-child { border-bottom:none; }
    .table-row:hover { background:#111520; }
    .row-thumb { width:50px; height:50px; border-radius:2px; overflow:hidden; border:1px solid var(--border); flex-shrink:0; position:relative; }
    .row-thumb canvas { width:100%!important; height:100%!important; display:block; }
    .thumb-loader { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:7px; font-family:'Orbitron',sans-serif; color:var(--dim); background:var(--bg3); }
    .row-name { color:#d0d8e4; font-size:14px; font-family:'Barlow Condensed',sans-serif; font-weight:700; }
    .row-id { color:#2a3040; font-size:10px; font-family:'Orbitron',sans-serif; margin-top:2px; }
    .row-cat-badge { display:inline-block; font-size:9px; font-weight:700; letter-spacing:2px; padding:3px 8px; border-radius:2px; font-family:'Orbitron',sans-serif; }
    .row-model { color:#334455; font-size:10px; font-family:'Orbitron',sans-serif; word-break:break-all; }
    .row-featured { text-align:center; font-size:16px; }
    .row-actions { display:flex; gap:6px; }
    .btn-edit { background:#141a24; border:1px solid #1e2530; color:#6a8ab0; padding:5px 10px; cursor:pointer; font-size:10px; border-radius:2px; font-family:'Orbitron',sans-serif; }
    .btn-edit:hover { border-color:#2a3f5f; }
    .btn-del { background:#1a0d0d; border:1px solid #2a1515; color:#c0392b; padding:5px 8px; cursor:pointer; font-size:10px; border-radius:2px; font-family:'Orbitron',sans-serif; }
    .empty-row { padding:60px; text-align:center; color:#1e2530; font-family:'Orbitron',sans-serif; font-size:13px; letter-spacing:2px; background:var(--bg2); }

    /* ── FORM ── */
    .form-grid { display:grid; grid-template-columns:1fr 300px; gap:32px; }
    .form-fields { display:flex; flex-direction:column; gap:18px; }
    .field { display:flex; flex-direction:column; gap:6px; }
    .field label { color:#334455; font-size:9px; letter-spacing:3px; font-family:'Orbitron',sans-serif; font-weight:700; text-transform:uppercase; }
    .field input, .field textarea, .field select { background:var(--bg2); border:1px solid var(--border2); border-radius:3px; color:#c8d4e0; padding:10px 14px; font-size:13px; font-family:'Syne',sans-serif; transition:border-color .15s; width:100%; }
    .field textarea { resize:vertical; }
    .field-note { color:#1e2530; font-size:9px; font-family:'Orbitron',sans-serif; letter-spacing:1px; margin-top:4px; line-height:1.6; }
    .upload-zone { border:2px dashed var(--border2); border-radius:3px; padding:24px 16px; text-align:center; cursor:pointer; transition:all .2s; background:var(--bg2); position:relative; min-height:100px; }
    .upload-zone:hover, .upload-zone.dragover { border-color:var(--orange); background:rgba(230,126,34,.05); }
    .upload-zone input[type="file"] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
    .upload-icon { font-size:28px; margin-bottom:8px; }
    .upload-title { color:#445870; font-family:'Orbitron',sans-serif; font-size:11px; font-weight:700; letter-spacing:2px; margin-bottom:4px; }
    .upload-hint { color:#1e2530; font-size:10px; font-family:'Orbitron',sans-serif; letter-spacing:1px; }
    .upload-progress { display:none; margin-top:12px; }
    .upload-bar-wrap { background:#0d1117; border-radius:2px; height:4px; overflow:hidden; }
    .upload-bar { height:100%; background:var(--orange); width:0%; transition:width .3s; border-radius:2px; }
    .upload-status { color:var(--dim); font-size:10px; font-family:'Orbitron',sans-serif; letter-spacing:1px; margin-top:6px; }
    .upload-result { display:none; margin-top:10px; background:rgba(39,174,96,.08); border:1px solid rgba(39,174,96,.2); border-radius:3px; padding:10px 14px; }
    .upload-result-name { color:#27ae60; font-size:11px; font-family:'Orbitron',sans-serif; letter-spacing:1px; }
    .upload-result-path { color:#2a3545; font-size:10px; font-family:'Orbitron',sans-serif; margin-top:3px; }
    .field-row-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .field-row-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }
    .color-pick { display:flex; align-items:center; gap:10px; background:var(--bg2); border:1px solid var(--border2); border-radius:3px; padding:6px 14px; }
    .color-pick input[type="color"] { width:36px; height:36px; background:transparent; border:none; cursor:pointer; }
    .color-pick span { color:#556878; font-family:'Orbitron',sans-serif; font-size:11px; }
    .featured-toggle { display:flex; align-items:center; gap:10px; background:var(--bg2); border:1px solid var(--border2); border-radius:3px; padding:10px 14px; cursor:pointer; transition:border-color .15s; user-select:none; }
    .featured-toggle.on { border-color:var(--orange); }
    .toggle-box { width:18px; height:18px; border-radius:3px; border:2px solid #2a3545; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:all .15s; }
    .featured-toggle.on .toggle-box { background:var(--orange); border-color:var(--orange); }
    .toggle-box span { color:#fff; font-size:11px; display:none; }
    .featured-toggle.on .toggle-box span { display:block; }
    .toggle-label { color:#334455; font-size:11px; font-family:'Orbitron',sans-serif; }
    .featured-toggle.on .toggle-label { color:var(--orange); }
    .form-actions { display:flex; gap:12px; padding-top:8px; border-top:1px solid var(--border); margin-top:8px; }
    .btn-save { background:var(--orange); color:#fff; border:none; padding:13px 28px; font-size:11px; font-weight:700; letter-spacing:2px; cursor:pointer; font-family:'Orbitron',sans-serif; border-radius:2px; }
    .btn-cancel { background:transparent; color:#445870; border:1px solid var(--border2); padding:13px 22px; font-size:11px; cursor:pointer; font-family:'Orbitron',sans-serif; border-radius:2px; }

    /* ── PREVIEW PANEL ── */
    .preview-panel { display:flex; flex-direction:column; gap:16px; }
    .preview-label { color:#1e2530; font-size:9px; font-family:'Orbitron',sans-serif; letter-spacing:3px; }
    .preview-3d {
      height:260px;
      border:1px solid var(--border);
      border-radius:3px;
      overflow:hidden;
      position:relative;
      background: var(--bg3);
    }
    .preview-3d canvas { position:absolute; inset:0; width:100%!important; height:100%!important; display:block; }
    .preview-loader {
      position:absolute; inset:0;
      display:flex; align-items:center; justify-content:center;
      font-family:'Orbitron',sans-serif; font-size:9px; letter-spacing:3px;
      color:var(--dim); background:var(--bg3);
      z-index: 10;
      pointer-events: none;
    }
    .preview-loader.hidden { display:none; }
    .preview-summary { background:var(--bg3); border:1px solid var(--border); border-radius:3px; padding:14px 16px; }
    .preview-summary-title { color:#2a3545; font-size:9px; font-family:'Orbitron',sans-serif; letter-spacing:2px; margin-bottom:10px; }
    .preview-row { display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px solid #0f1318; }
    .preview-row:last-child { border-bottom:none; }
    .preview-key { color:#334455; font-size:10px; font-family:'Orbitron',sans-serif; }
    .preview-val { color:#8a9ab0; font-size:10px; font-family:'Orbitron',sans-serif; }

    /* ── CONFIRM MODAL ── */
    .confirm-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.8); backdrop-filter:blur(6px); z-index:3000; align-items:center; justify-content:center; }
    .confirm-overlay.open { display:flex; animation:fadeIn .2s ease; }
    .confirm-box { background:#13171c; border:1px solid #2a2f38; border-radius:4px; padding:32px; width:min(400px,90vw); box-shadow:0 30px 80px rgba(0,0,0,.8); }
    .confirm-label { color:#c0392b; font-size:11px; font-family:'Orbitron',sans-serif; letter-spacing:3px; margin-bottom:10px; }
    .confirm-msg { color:#d0d8e4; font-family:'Syne',sans-serif; font-size:14px; line-height:1.6; margin-bottom:24px; }
    .confirm-actions { display:flex; gap:10px; }
    .btn-confirm-del { background:#c0392b; color:#fff; border:none; padding:10px 20px; font-size:11px; font-weight:700; letter-spacing:2px; cursor:pointer; font-family:'Orbitron',sans-serif; border-radius:2px; }
    .btn-confirm-cancel { background:transparent; color:#556878; border:1px solid #1e2530; padding:10px 20px; font-size:11px; cursor:pointer; font-family:'Orbitron',sans-serif; border-radius:2px; }

    /* ── TOAST ── */
    .toast { position:fixed; bottom:28px; right:28px; padding:14px 20px; border-radius:3px; font-family:'Orbitron',sans-serif; font-size:12px; box-shadow:0 10px 40px rgba(0,0,0,.6); z-index:4000; display:none; align-items:center; gap:10px; letter-spacing:1px; animation:fadeIn .2s ease; }
    .toast.success { background:#0d1a0d; border:1px solid #27ae60; color:#55c878; }
    .toast.error   { background:#1a0d0d; border:1px solid #c0392b; color:#e05555; }
  </style>
</head>
<body>

<!-- HEADER -->
<div class="adm-header">
  <div class="adm-accent"></div>
  <div>
    <div class="adm-title-label">PANEL DE ADMINISTRACIÓN</div>
    <div class="adm-title">SMART MACHING CONTROL</div>
  </div>
  <div class="adm-stats">
    <div class="adm-stat"><div class="adm-stat-val" id="statTotal">—</div><div class="adm-stat-key">TOTAL</div></div>
    <div class="adm-stat"><div class="adm-stat-val" id="statFeat">—</div><div class="adm-stat-key">DESTACADOS</div></div>
    <div class="adm-stat"><div class="adm-stat-val" id="statCats">—</div><div class="adm-stat-key">CATEGORÍAS</div></div>
  </div>
  <div class="adm-user">
    <span class="adm-user-name" id="adminName">—</span>
    <button class="btn-logout" onclick="doLogout()">SALIR ↗</button>
  </div>
</div>

<!-- BODY -->
<div class="adm-body">

  <!-- SIDEBAR -->
  <aside class="adm-sidebar">
    <div class="sidebar-label">NAVEGACIÓN</div>
    <button class="sidebar-btn active" id="btnNavList" onclick="showPage('list')">▤ &nbsp;INVENTARIO</button>
    <button class="sidebar-btn" id="btnNavForm" onclick="newTool()">＋ &nbsp;NUEVA HERRAMIENTA</button>

    <div class="sidebar-label">CATEGORÍAS</div>
    <div id="sidebarCats"></div>

    <div style="flex:1"></div>
    <div class="sidebar-db">
      <div class="sidebar-db-title">BASE DE DATOS MySQL</div>
      <div class="db-online"><div class="dot-green"></div><span>CONECTADA</span></div>
      <div class="db-row"><span class="db-key">HOST</span><span class="db-val">localhost</span></div>
      <div class="db-row"><span class="db-key">DB</span><span class="db-val">smartmaching</span></div>
      <div class="db-row"><span class="db-key">REGISTROS</span><span class="db-val" id="sidebarCount">—</span></div>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="adm-main">

    <!-- PAGE LIST -->
    <div class="adm-page active" id="page-list">
      <div class="page-label">GESTIÓN DE HERRAMIENTAS</div>
      <div style="display:flex;align-items:flex-end;gap:16px;margin-bottom:28px">
        <div class="page-title" style="margin:4px 0 0">INVENTARIO</div>
        <div style="flex:1"></div>
        <div class="inv-search">
          <span style="color:var(--dim)">⌕</span>
          <input type="text" id="invSearch" placeholder="Buscar herramienta..."/>
        </div>
        <button class="btn-add" onclick="newTool()">+ AGREGAR</button>
      </div>
      <div class="table-wrap">
        <div class="table-head">
          <span>3D</span><span>HERRAMIENTA</span><span>CATEGORÍA</span>
          <span>ARCHIVO .GLB</span><span>DEST.</span><span>ACCIONES</span>
        </div>
        <div id="tableBody"></div>
      </div>
    </div>

    <!-- PAGE FORM -->
    <div class="adm-page" id="page-form">
      <div class="page-label" id="formLabel">NUEVA HERRAMIENTA</div>
      <div class="page-title" id="formTitle">AGREGAR AL CATÁLOGO</div>
      <div class="form-grid">
        <div class="form-fields">
          <div class="field-row-2">
            <div class="field">
              <label>Nombre *</label>
              <input type="text" id="fName" placeholder="Ej: Llave de Torque Digital"/>
            </div>
            <div class="field">
              <label>Categoría</label>
              <select id="fCat"></select>
            </div>
          </div>
          <div class="field">
            <label>Descripción técnica</label>
            <textarea id="fDesc" rows="5" placeholder="Especificaciones, materiales, capacidades..."></textarea>
          </div>
          <div class="field">
            <label>Archivo 3D (.glb) *</label>
            <div class="upload-zone" id="uploadZone">
              <input type="file" id="fileInput" accept=".glb,.gltf" onchange="handleFileSelect(this)"/>
              <div class="upload-icon">📦</div>
              <div class="upload-title">ARRASTRA TU ARCHIVO O HAZ CLIC</div>
              <div class="upload-hint">Formatos soportados: .glb · .gltf</div>
            </div>
            <div class="upload-progress" id="uploadProgress">
              <div class="upload-bar-wrap"><div class="upload-bar" id="uploadBar"></div></div>
              <div class="upload-status" id="uploadStatus">SUBIENDO...</div>
            </div>
            <div class="upload-result" id="uploadResult">
              <div class="upload-result-name" id="uploadResultName"></div>
              <div class="upload-result-path" id="uploadResultPath"></div>
            </div>
            <input type="hidden" id="fModel"/>
          </div>
          <div class="field-row-3">
            <div class="field">
              <label>Color</label>
              <div class="color-pick">
                <input type="color" id="fColor" value="#e67e22" oninput="document.getElementById('fColorHex').textContent=this.value;updateSummary()"/>
                <span id="fColorHex">#e67e22</span>
              </div>
            </div>
            <div class="field">
              <label>ID (auto)</label>
              <input type="text" id="fId" readonly style="color:var(--dim)"/>
            </div>
            <div class="field">
              <label>Destacar</label>
              <div class="featured-toggle" id="featuredToggle" onclick="toggleFeatured()">
                <div class="toggle-box"><span>✓</span></div>
                <div class="toggle-label">★ DESTACADO</div>
              </div>
            </div>
          </div>
          <div class="form-actions">
            <button class="btn-save" id="btnSave" onclick="saveTool()">✓ GUARDAR</button>
            <button class="btn-cancel" onclick="cancelForm()">CANCELAR</button>
          </div>
        </div>

        <!-- Preview -->
        <div class="preview-panel">
          <div class="preview-label">PREVISUALIZACIÓN 3D</div>
          <div class="preview-3d" id="previewBox">
            <div class="preview-loader" id="previewLoader">INGRESA RUTA .GLB</div>
          </div>
          <div class="preview-summary">
            <div class="preview-summary-title">RESUMEN</div>
            <div class="preview-row"><span class="preview-key">NOMBRE</span><span class="preview-val" id="prvName">—</span></div>
            <div class="preview-row"><span class="preview-key">CATEGORÍA</span><span class="preview-val" id="prvCat">—</span></div>
            <div class="preview-row"><span class="preview-key">ARCHIVO</span><span class="preview-val" id="prvModel">—</span></div>
            <div class="preview-row"><span class="preview-key">DESTACADO</span><span class="preview-val" id="prvFeat">No</span></div>
          </div>
        </div>
      </div>
    </div>

  </main>
</div>

<!-- CONFIRM MODAL -->
<div class="confirm-overlay" id="confirmOverlay">
  <div class="confirm-box">
    <div class="confirm-label">⚠ CONFIRMAR ELIMINACIÓN</div>
    <div class="confirm-msg" id="confirmMsg"></div>
    <div class="confirm-actions">
      <button class="btn-confirm-del" onclick="confirmDelete()">ELIMINAR</button>
      <button class="btn-confirm-cancel" onclick="closeConfirm()">CANCELAR</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
const CATEGORIES_ALL = ['Medición','Sujeción','Neumática','Diagnóstico','Corte','Otra'];
let tools = [], invFilter = '', editingId = null, deleteId = null, isFeatured = false;

/* ══════════════ AUTH ══════════════ */
async function checkAuth() {
  try {
    const res  = await fetch('api.php?action=me');
    const data = await res.json();
    if (!data.ok) { window.location.href = 'login.html'; return; }
    document.getElementById('adminName').textContent = data.data.nombre;
  } catch { window.location.href = 'login.html'; }
}
async function doLogout() {
  await fetch('api.php?action=logout', { method:'POST' });
  window.location.href = 'login.html';
}

/* ══════════════ API ══════════════ */
async function apiGet(params) {
  const r = await fetch('api.php?' + new URLSearchParams(params));
  return r.json();
}
async function apiPost(action, body) {
  const r = await fetch('api.php?action='+action, {
    method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(body)
  });
  return r.json();
}
async function apiDelete(id) {
  const r = await fetch('api.php?action=delete&id='+id, { method:'DELETE' });
  return r.json();
}
async function loadTools() {
  const res = await apiGet({action:'tools'});
  if (res.ok) tools = res.data;
  updateStats(); renderSidebarCats(); renderTable();
}

/* ══════════════ THREE.JS VIEWER ══════════════ */
const viewers = new Map();

function createViewer(container, modelPath) {
  // Destruir visor previo en este container si lo hay
  const existing = container._vid;
  if (existing) destroyViewer(existing);

  const vid = '_v' + Math.random().toString(36).slice(2);

  // Eliminar canvas huérfano
  container.querySelectorAll('canvas').forEach(c => c.remove());

  const w = container.clientWidth  || container.offsetWidth  || 280;
  const h = container.clientHeight || container.offsetHeight || 260;

  const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
  renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
  renderer.setSize(w, h);
  renderer.outputEncoding = THREE.sRGBEncoding;
  renderer.shadowMap.enabled = true;
  container.appendChild(renderer.domElement);

  const scene = new THREE.Scene();
  scene.background = new THREE.Color(0x0a0d12);

  const camera = new THREE.PerspectiveCamera(45, w / h, 0.01, 1000);
  camera.position.set(3, 2, 4);

  scene.add(new THREE.AmbientLight(0xffffff, 0.5));
  const key = new THREE.DirectionalLight(0xffffff, 1.2);
  key.position.set(5, 8, 5); key.castShadow = true; scene.add(key);
  const fill = new THREE.DirectionalLight(0xff8c00, 0.3);
  fill.position.set(-5, 2, -5); scene.add(fill);
  const rim = new THREE.PointLight(0x4fc3f7, 0.6, 20);
  rim.position.set(0, 5, -3); scene.add(rim);
  const grid = new THREE.GridHelper(10, 20, 0x1a1a1a, 0x141414);
  grid.position.y = -1.2; scene.add(grid);

  const controls = new THREE.OrbitControls(camera, renderer.domElement);
  controls.enableDamping = true; controls.dampingFactor = 0.07;
  controls.enablePan = false; controls.minDistance = 0.5; controls.maxDistance = 20;
  controls.autoRotate = true; controls.autoRotateSpeed = 1.5;
  controls.target.set(0, 0, 0);

  // Loader element — sólo el del container actual
  const loaderEl = container.querySelector('.thumb-loader, .preview-loader');

  if (modelPath) {
    if (loaderEl) { loaderEl.textContent = 'CARGANDO...'; loaderEl.classList.remove('hidden'); }
    new THREE.GLTFLoader().load(
      modelPath,
      (gltf) => {
        // ← AQUÍ estaba el bug: el loader no se ocultaba correctamente
        if (loaderEl) loaderEl.classList.add('hidden');
        const m = gltf.scene;
        const box = new THREE.Box3().setFromObject(m);
        const center = box.getCenter(new THREE.Vector3());
        const size   = box.getSize(new THREE.Vector3());
        const scale  = 2.5 / Math.max(size.x, size.y, size.z);
        m.scale.setScalar(scale);
        m.position.sub(center.multiplyScalar(scale));
        m.traverse(n => { if (n.isMesh) { n.castShadow = n.receiveShadow = true; } });
        scene.add(m);
        controls.update();
      },
      (xhr) => {
        if (loaderEl && xhr.total)
          loaderEl.textContent = 'CARGANDO ' + Math.round(xhr.loaded / xhr.total * 100) + '%';
      },
      (err) => {
        console.warn('GLB error:', err);
        if (loaderEl) { loaderEl.textContent = 'SIN MODELO'; loaderEl.classList.remove('hidden'); }
      }
    );
  } else {
    if (loaderEl) { loaderEl.textContent = 'INGRESA RUTA .GLB'; loaderEl.classList.remove('hidden'); }
  }

  let frame;
  const loop = () => {
    frame = requestAnimationFrame(loop);
    controls.update();
    renderer.render(scene, camera);
  };
  loop();

  viewers.set(vid, { renderer, controls, frame });
  container._vid = vid;
  return vid;
}

function destroyViewer(vid) {
  const v = viewers.get(vid);
  if (!v) return;
  cancelAnimationFrame(v.frame);
  v.controls.dispose();
  v.renderer.dispose();
  viewers.delete(vid);
}

/* ══════════════ PREVIEW 3D (formulario) ══════════════ */
let previewDebounce = null;

function loadPreview(path) {
  clearTimeout(previewDebounce);
  if (!path) return;

  const loader = document.getElementById('previewLoader');
  if (loader) { loader.textContent = 'CARGANDO...'; loader.classList.remove('hidden'); }

  // Esperar al siguiente frame para que el DOM tenga dimensiones reales
  previewDebounce = setTimeout(() => {
    requestAnimationFrame(() => {
      const box = document.getElementById('previewBox');
      createViewer(box, path);
    });
  }, 400);
}

function updateSummary() {
  document.getElementById('prvName').textContent  = document.getElementById('fName').value  || '—';
  document.getElementById('prvCat').textContent   = document.getElementById('fCat').value   || '—';
  document.getElementById('prvModel').textContent = document.getElementById('fModel').value || '—';
}

// Actualizar resumen en tiempo real al escribir en los campos
['fName','fCat'].forEach(id =>
  document.getElementById(id).addEventListener('input', updateSummary)
);

/* ══════════════ STATS & SIDEBAR ══════════════ */
function updateStats() {
  document.getElementById('statTotal').textContent = tools.length;
  document.getElementById('statFeat').textContent  = tools.filter(t => t.destacado == 1).length;
  document.getElementById('statCats').textContent  = [...new Set(tools.map(t => t.categoria))].length;
  document.getElementById('sidebarCount').textContent = tools.length + ' registros';
}
function renderSidebarCats() {
  document.getElementById('sidebarCats').innerHTML = CATEGORIES_ALL.map(cat => {
    const c = tools.filter(t => t.categoria === cat).length;
    return `<div class="sidebar-cat-row"><span>${cat}</span><span class="sidebar-cat-count">${c}</span></div>`;
  }).join('');
}

/* ══════════════ TABLE ══════════════ */
const rowViewers = new Map();
function destroyRowViewers() { rowViewers.forEach(v => destroyViewer(v)); rowViewers.clear(); }

function renderTable() {
  destroyRowViewers();
  const body = document.getElementById('tableBody');
  const list = tools.filter(t =>
    !invFilter ||
    t.nombre.toLowerCase().includes(invFilter) ||
    t.categoria.toLowerCase().includes(invFilter)
  );
  if (!list.length) {
    body.innerHTML = `<div class="empty-row">${tools.length === 0 ? 'SIN HERRAMIENTAS — AGREGA LA PRIMERA' : 'SIN RESULTADOS'}</div>`;
    return;
  }
  body.innerHTML = '';
  list.forEach(t => {
    const row = document.createElement('div'); row.className = 'table-row';
    row.innerHTML = `
      <div class="row-thumb" id="rthumb_${t.id}"><div class="thumb-loader">...</div></div>
      <div><div class="row-name">${t.nombre}</div><div class="row-id">${t.id}</div></div>
      <span class="row-cat-badge" style="background:${t.color}22;color:${t.color};border:1px solid ${t.color}44">${t.categoria}</span>
      <div class="row-model">${t.model_path}</div>
      <div class="row-featured">${t.destacado == 1 ? '<span style="color:var(--orange)">★</span>' : '<span style="color:var(--dim)">☆</span>'}</div>
      <div class="row-actions">
        <button class="btn-edit" onclick="editTool('${t.id}')">EDITAR</button>
        <button class="btn-del"  onclick="askDelete('${t.id}')">✕</button>
      </div>`;
    body.appendChild(row);
    setTimeout(() => {
      const el = document.getElementById('rthumb_' + t.id);
      if (el) rowViewers.set(t.id, createViewer(el, t.model_path));
    }, 0);
  });
}

/* ══════════════ PAGES ══════════════ */
function showPage(name) {
  document.querySelectorAll('.adm-page').forEach(p => p.classList.remove('active'));
  document.getElementById('page-' + name).classList.add('active');
  document.querySelectorAll('.sidebar-btn').forEach(b => b.classList.remove('active'));
  document.getElementById(name === 'list' ? 'btnNavList' : 'btnNavForm').classList.add('active');
  if (name === 'list') renderTable();
}

/* ══════════════ FORM ══════════════ */
function populateCatSelect() {
  const s = document.getElementById('fCat'); s.innerHTML = '';
  CATEGORIES_ALL.forEach(c => { const o = document.createElement('option'); o.value = o.textContent = c; s.appendChild(o); });
}

function toggleFeatured() {
  isFeatured = !isFeatured;
  document.getElementById('featuredToggle').classList.toggle('on', isFeatured);
  document.getElementById('prvFeat').textContent = isFeatured ? 'Sí' : 'No';
}

function clearPreviewBox() {
  clearTimeout(previewDebounce);
  const box = document.getElementById('previewBox');
  if (box._vid) { destroyViewer(box._vid); box._vid = null; }
  box.querySelectorAll('canvas').forEach(c => c.remove());
  const l = document.getElementById('previewLoader');
  if (l) { l.textContent = 'INGRESA RUTA .GLB'; l.classList.remove('hidden'); }
}

function genId() { return 't' + Date.now(); }

function resetForm() {
  editingId = null; isFeatured = false;
  document.getElementById('fId').value  = genId();
  ['fName','fDesc','fModel'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('fColor').value = '#e67e22';
  document.getElementById('fColorHex').textContent = '#e67e22';
  document.getElementById('fCat').value = CATEGORIES_ALL[0];
  document.getElementById('featuredToggle').classList.remove('on');
  document.getElementById('btnSave').textContent = '✓ GUARDAR';
  document.getElementById('formLabel').textContent = 'NUEVA HERRAMIENTA';
  document.getElementById('formTitle').textContent = 'AGREGAR AL CATÁLOGO';
  ['prvName','prvCat','prvModel'].forEach(id => document.getElementById(id).textContent = '—');
  document.getElementById('prvFeat').textContent = 'No';
  clearPreviewBox();
  // Reset upload zone
  document.getElementById('uploadProgress').style.display = 'none';
  document.getElementById('uploadResult').style.display   = 'none';
  document.getElementById('uploadBar').style.width = '0%';
  document.getElementById('uploadBar').style.background = 'var(--orange)';
  const uz = document.getElementById('uploadZone');
  uz.querySelector('.upload-icon').textContent  = '📦';
  uz.querySelector('.upload-title').textContent = 'ARRASTRA TU ARCHIVO O HAZ CLIC';
  uz.querySelector('.upload-hint').textContent  = 'Formatos soportados: .glb · .gltf';
  uz.style.borderColor = '';
  document.getElementById('fileInput').value = '';
}

function newTool()    { resetForm(); showPage('form'); }
function cancelForm() { resetForm(); showPage('list'); }

function editTool(id) {
  const t = tools.find(x => x.id === id); if (!t) return;
  editingId = id; isFeatured = t.destacado == 1;
  document.getElementById('fId').value    = t.id;
  document.getElementById('fName').value  = t.nombre;
  document.getElementById('fDesc').value  = t.descripcion || '';
  document.getElementById('fModel').value = t.model_path;
  document.getElementById('fColor').value = t.color || '#e67e22';
  document.getElementById('fColorHex').textContent = t.color || '#e67e22';
  document.getElementById('fCat').value   = t.categoria;
  document.getElementById('featuredToggle').classList.toggle('on', isFeatured);
  document.getElementById('btnSave').textContent   = '✓ ACTUALIZAR';
  document.getElementById('formLabel').textContent = 'EDITANDO HERRAMIENTA';
  document.getElementById('formTitle').textContent = 'EDITAR REGISTRO';
  updateSummary();
  document.getElementById('prvFeat').textContent = isFeatured ? 'Sí' : 'No';
  // Mostrar modelo actual en zona de upload
  if (t.model_path) {
    const fname = t.model_path.split('/').pop();
    document.getElementById('uploadResult').style.display     = 'block';
    document.getElementById('uploadResultName').textContent   = '✓ ' + fname;
    document.getElementById('uploadResultPath').textContent   = 'Ruta: ' + t.model_path;
    const uz = document.getElementById('uploadZone');
    uz.querySelector('.upload-title').textContent = fname;
    uz.querySelector('.upload-icon').textContent  = '✅';
    uz.querySelector('.upload-hint').textContent  = 'Haz clic para cambiar el archivo';
    uz.style.borderColor = '#27ae60';
  }
  showPage('form');
  // Cargar preview después de que el panel sea visible
  requestAnimationFrame(() => loadPreview(t.model_path));
}

async function saveTool() {
  const nombre     = document.getElementById('fName').value.trim();
  const model_path = document.getElementById('fModel').value.trim();
  if (!nombre)     { showToast('El nombre es obligatorio.', 'error'); return; }
  if (!model_path) { showToast('La ruta del modelo .glb es obligatoria.', 'error'); return; }

  const payload = {
    id:          document.getElementById('fId').value,
    nombre,
    categoria:   document.getElementById('fCat').value,
    descripcion: document.getElementById('fDesc').value.trim(),
    model_path,
    color:       document.getElementById('fColor').value,
    destacado:   isFeatured
  };

  const btn = document.getElementById('btnSave');
  btn.disabled = true; btn.textContent = 'GUARDANDO...';
  const res = await apiPost('save', payload);
  btn.disabled = false; btn.textContent = editingId ? '✓ ACTUALIZAR' : '✓ GUARDAR';

  if (!res.ok) { showToast(res.error || 'Error al guardar.', 'error'); return; }
  showToast(res.data.action === 'updated' ? 'Herramienta actualizada.' : 'Herramienta guardada.');
  await loadTools();
  resetForm();
  showPage('list');
}

/* ══════════════ DELETE ══════════════ */
function askDelete(id) {
  const t = tools.find(x => x.id === id); if (!t) return;
  deleteId = id;
  document.getElementById('confirmMsg').textContent = `¿Eliminar "${t.nombre}" del catálogo? Esta acción no se puede deshacer.`;
  document.getElementById('confirmOverlay').classList.add('open');
}
function closeConfirm() { document.getElementById('confirmOverlay').classList.remove('open'); deleteId = null; }
async function confirmDelete() {
  const res = await apiDelete(deleteId);
  if (!res.ok) { showToast(res.error || 'Error al eliminar.', 'error'); return; }
  showToast('Herramienta eliminada.', 'error');
  closeConfirm(); await loadTools();
}

/* ══════════════ TOAST ══════════════ */
let toastTimer;
function showToast(msg, type = 'success') {
  const el = document.getElementById('toast');
  el.textContent = (type === 'success' ? '✓ ' : '✕ ') + msg;
  el.className = 'toast ' + type; el.style.display = 'flex';
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => el.style.display = 'none', 2800);
}

document.getElementById('invSearch').addEventListener('input', e => {
  invFilter = e.target.value.toLowerCase().trim(); renderTable();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeConfirm(); });

/* ══════════════ FILE UPLOAD ══════════════ */
const uploadZone = document.getElementById('uploadZone');
uploadZone.addEventListener('dragover',  e => { e.preventDefault(); uploadZone.classList.add('dragover'); });
uploadZone.addEventListener('dragleave', ()  => uploadZone.classList.remove('dragover'));
uploadZone.addEventListener('drop', e => {
  e.preventDefault(); uploadZone.classList.remove('dragover');
  const file = e.dataTransfer.files[0];
  if (file) uploadFile(file);
});
function handleFileSelect(input) {
  if (input.files[0]) uploadFile(input.files[0]);
}

async function uploadFile(file) {
  const ext = file.name.split('.').pop().toLowerCase();
  if (!['glb','gltf'].includes(ext)) {
    showToast('Solo se permiten archivos .glb o .gltf', 'error'); return;
  }

  document.getElementById('uploadProgress').style.display = 'block';
  document.getElementById('uploadResult').style.display   = 'none';
  document.getElementById('uploadBar').style.width = '0%';
  document.getElementById('uploadBar').style.background = 'var(--orange)';
  document.getElementById('uploadStatus').textContent = 'SUBIENDO...';

  // Limpiar preview mientras sube
  clearPreviewBox();
  const loader = document.getElementById('previewLoader');
  if (loader) { loader.textContent = 'SUBIENDO ARCHIVO...'; loader.classList.remove('hidden'); }

  const formData = new FormData();
  formData.append('model', file);

  try {
    const data = await new Promise((resolve, reject) => {
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'api.php?action=upload');
      xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
          const pct = Math.round(e.loaded / e.total * 100);
          document.getElementById('uploadBar').style.width    = pct + '%';
          document.getElementById('uploadStatus').textContent = 'SUBIENDO ' + pct + '%';
        }
      };
      xhr.onload = () => {
        try {
          const d = JSON.parse(xhr.responseText);
          if (d.ok) resolve(d);
          else reject(new Error(d.error || 'Error al subir'));
        } catch { reject(new Error('Respuesta inválida del servidor')); }
      };
      xhr.onerror = () => reject(new Error('Error de red'));
      xhr.send(formData);
    });

    // Upload exitoso
    document.getElementById('uploadBar').style.width    = '100%';
    document.getElementById('uploadStatus').textContent = '✓ SUBIDO CORRECTAMENTE';

    // Guardar ruta en campo oculto
    document.getElementById('fModel').value = data.data.path;
    updateSummary();

    // Mostrar resultado
    document.getElementById('uploadResult').style.display     = 'block';
    document.getElementById('uploadResultName').textContent   = '✓ ' + data.data.filename;
    document.getElementById('uploadResultPath').textContent   = 'Ruta: ' + data.data.path;

    // Actualizar zona de upload
    uploadZone.querySelector('.upload-title').textContent = data.data.filename;
    uploadZone.querySelector('.upload-icon').textContent  = '✅';
    uploadZone.querySelector('.upload-hint').textContent  = 'Archivo listo';
    uploadZone.style.borderColor = '#27ae60';

    // ← CORRECCIÓN PRINCIPAL:
    // Esperar 2 frames para que el panel tenga dimensiones reales antes de crear el renderer
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        loadPreview(data.data.path);
      });
    });

  } catch(err) {
    document.getElementById('uploadStatus').textContent = '✕ ERROR: ' + err.message;
    document.getElementById('uploadBar').style.background = '#c0392b';
    if (loader) { loader.textContent = 'ERROR AL SUBIR'; loader.classList.remove('hidden'); }
    showToast(err.message, 'error');
  }
}

/* ══════════════ INIT ══════════════ */
(async () => {
  await checkAuth();
  populateCatSelect();
  await loadTools();
})();
</script>
</body>
</html>