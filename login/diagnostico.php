<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"/>
<title>Diagnóstico — Smart Maching</title>
<style>
  body{background:#090c10;color:#f0f2f5;font-family:monospace;padding:40px;max-width:700px;margin:0 auto;}
  h2{color:#e67e22;letter-spacing:3px;font-size:13px;margin-bottom:24px;}
  .row{display:flex;justify-content:space-between;padding:10px 14px;margin-bottom:4px;border-radius:3px;font-size:13px;background:#0d1117;border:1px solid #161b22;}
  .ok{color:#27ae60;} .err{color:#c0392b;} .warn{color:#f39c12;}
  .label{color:#445870;}
  .section{color:#e67e22;font-size:10px;letter-spacing:3px;margin:24px 0 10px;border-bottom:1px solid #1e2530;padding-bottom:6px;}
  .code{background:#0a0d12;border:1px solid #1e2530;border-radius:3px;padding:14px;font-size:12px;color:#556878;margin-top:16px;line-height:1.8;}
  a{color:#e67e22;}
  .test-upload{margin-top:20px;}
  .test-upload input{background:#0d1117;border:1px solid #1e2530;color:#c0d0e0;padding:8px;border-radius:3px;}
  .test-upload button{background:#e67e22;color:#fff;border:none;padding:8px 18px;cursor:pointer;border-radius:3px;margin-left:8px;font-family:monospace;}
  #uploadTestResult{margin-top:12px;font-size:12px;color:#556878;}
</style>
</head>
<body>
<h2>⚙ DIAGNÓSTICO DEL SISTEMA</h2>

<?php
require_once 'db.php';

// ── 1. PHP INFO ──
echo '<div class="section">PHP</div>';
$phpv = phpversion();
$ok = version_compare($phpv, '7.4', '>=');
echo '<div class="row"><span class="label">Versión PHP</span><span class="'.($ok?'ok':'err').'">'.$phpv.'</span></div>';

$extPDO = extension_loaded('pdo_mysql');
echo '<div class="row"><span class="label">PDO MySQL</span><span class="'.($extPDO?'ok':'err').'">' .($extPDO?'✓ Disponible':'✗ No disponible').'</span></div>';

$uploadEnabled = ini_get('file_uploads');
echo '<div class="row"><span class="label">file_uploads</span><span class="'.($uploadEnabled?'ok':'err').'">'.($uploadEnabled?'✓ ON':'✗ OFF — Habilítalo en php.ini').'</span></div>';

$maxSize = ini_get('upload_max_filesize');
echo '<div class="row"><span class="label">upload_max_filesize</span><span class="warn">'.$maxSize.'</span></div>';

$postMax = ini_get('post_max_size');
echo '<div class="row"><span class="label">post_max_size</span><span class="warn">'.$postMax.'</span></div>';

// ── 2. RUTAS ──
echo '<div class="section">RUTAS</div>';
$dir = __DIR__;
echo '<div class="row"><span class="label">Directorio actual (__DIR__)</span><span class="warn">'.htmlspecialchars($dir).'</span></div>';

$modelsDir = __DIR__ . '/models/';
$modelsExists = is_dir($modelsDir);
echo '<div class="row"><span class="label">Carpeta models/</span><span class="'.($modelsExists?'ok':'err').'">'.($modelsExists?'✓ Existe':'✗ No existe').'</span></div>';

if ($modelsExists) {
    $writable = is_writable($modelsDir);
    echo '<div class="row"><span class="label">models/ escribible</span><span class="'.($writable?'ok':'err').'">'.($writable?'✓ Sí':'✗ No — chmod 755 models/').'</span></div>';
    
    $files = glob($modelsDir . '*.{glb,gltf}', GLOB_BRACE);
    echo '<div class="row"><span class="label">Archivos .glb en models/</span><span class="'.( count($files)>0?'ok':'warn').'">'.count($files).' archivo(s)</span></div>';
    foreach ($files as $f) {
        echo '<div class="row"><span class="label">&nbsp;&nbsp;→</span><span class="ok">'.basename($f).'</span></div>';
    }
}

// ── 3. BASE DE DATOS ──
echo '<div class="section">BASE DE DATOS</div>';
try {
    $db = getDB();
    echo '<div class="row"><span class="label">Conexión MySQL</span><span class="ok">✓ Conectado</span></div>';
    
    // Check tables
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach (['admins','herramientas'] as $t) {
        $exists = in_array($t, $tables);
        echo '<div class="row"><span class="label">Tabla: '.$t.'</span><span class="'.($exists?'ok':'err').'">'.($exists?'✓ Existe':'✗ No existe — ejecuta setup.sql').'</span></div>';
    }
    
    if (in_array('admins', $tables)) {
        $admins = $db->query("SELECT id, username, nombre, LEFT(password,6) as pass_preview FROM admins")->fetchAll();
        foreach ($admins as $a) {
            $isHashed = strpos($a['pass_preview'], '$2y$') === 0 || strpos($a['pass_preview'], '$2a$') === 0;
            echo '<div class="row"><span class="label">Admin: '.$a['username'].'</span><span class="'.($isHashed?'warn':'ok').'">'.($isHashed?'⚠ Contraseña hasheada (bcrypt)':'✓ Contraseña en texto plano').'</span></div>';
        }
    }
    
    if (in_array('herramientas', $tables)) {
        $count = $db->query("SELECT COUNT(*) FROM herramientas")->fetchColumn();
        echo '<div class="row"><span class="label">Herramientas en DB</span><span class="ok">'.$count.' registros</span></div>';
    }
    
} catch (Exception $e) {
    echo '<div class="row"><span class="label">Conexión MySQL</span><span class="err">✗ '.$e->getMessage().'</span></div>';
}

// ── 4. SESSION ──
echo '<div class="section">SESIÓN</div>';
session_start();
echo '<div class="row"><span class="label">Session activa</span><span class="'.(!empty($_SESSION['admin_id'])?'ok':'warn').'">'.(!empty($_SESSION['admin_id'])?'✓ Logueado como: '.$_SESSION['admin_user']:'⚠ Sin sesión').'</span></div>';

// ── 5. FIX ──
echo '<div class="section">ACCIONES</div>';

// Crear carpeta models si no existe
if (!$modelsExists) {
    if (mkdir($modelsDir, 0755, true)) {
        echo '<div class="row"><span class="label">Crear models/</span><span class="ok">✓ Carpeta creada</span></div>';
    } else {
        echo '<div class="row"><span class="label">Crear models/</span><span class="err">✗ No se pudo crear. Créala manualmente.</span></div>';
    }
}

// Fix hashed passwords if needed
?>

<div class="section">TEST DE SUBIDA</div>
<div class="test-upload">
  <input type="file" id="testFile" accept=".glb,.gltf"/>
  <button onclick="testUpload()">SUBIR PRUEBA</button>
  <div id="uploadTestResult"></div>
</div>

<div class="code">
⚠ ELIMINA ESTE ARCHIVO (diagnostico.php) después de usarlo.<br>
<a href="login.html">← Ir al login</a> &nbsp;|&nbsp; <a href="admin.php">Ir al admin</a>
</div>

<script>
async function testUpload() {
  const file = document.getElementById('testFile').files[0];
  const res  = document.getElementById('uploadTestResult');
  if (!file) { res.textContent = 'Selecciona un archivo primero.'; return; }
  res.textContent = 'Subiendo...';
  res.style.color = '#f39c12';

  // First login check
  const meRes = await fetch('api.php?action=me');
  const me = await meRes.json();
  if (!me.ok) {
    res.innerHTML = '✗ No hay sesión activa. <a href="login.html">Inicia sesión primero</a>.';
    res.style.color = '#c0392b'; return;
  }

  const fd = new FormData();
  fd.append('model', file);
  try {
    const r    = await fetch('api.php?action=upload', { method:'POST', body:fd });
    const data = await r.json();
    if (data.ok) {
      res.textContent = '✓ Subido correctamente → ' + data.data.path;
      res.style.color = '#27ae60';
    } else {
      res.textContent = '✗ Error: ' + data.error;
      res.style.color = '#c0392b';
    }
  } catch(e) {
    res.textContent = '✗ ' + e.message;
    res.style.color = '#c0392b';
  }
}
</script>
</body>
</html>