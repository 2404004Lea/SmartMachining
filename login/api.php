<?php

session_start();
require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// ── Helpers ───────────────────────────────────────────
function ok($data = null)  { echo json_encode(['ok' => true,  'data' => $data]); exit; }
function fail($msg, $code = 400) { http_response_code($code); echo json_encode(['ok' => false, 'error' => $msg]); exit; }
function requireAuth() { if (empty($_SESSION['admin_id'])) fail('No autorizado', 401); }
function bodyJson(): array {
    $raw = file_get_contents('php://input');
    return $raw ? (json_decode($raw, true) ?? []) : [];
}
function genId(): string { return 't' . time() . rand(100, 999); }

// ── Rutas ─────────────────────────────────────────────

// GET tools — público
if ($action === 'tools' && $method === 'GET') {
    $db   = getDB();
    $stmt = $db->query('SELECT * FROM herramientas ORDER BY created_at DESC');
    ok($stmt->fetchAll());
}

// GET tool — público
if ($action === 'tool' && $method === 'GET') {
    $id = $_GET['id'] ?? '';
    if (!$id) fail('ID requerido');
    $db   = getDB();
    $stmt = $db->prepare('SELECT * FROM herramientas WHERE id = ?');
    $stmt->execute([$id]);
    $row  = $stmt->fetch();
    if (!$row) fail('Herramienta no encontrada', 404);
    ok($row);
}

// POST login
if ($action === 'login' && $method === 'POST') {
    $body = bodyJson();
    $user = trim($body['username'] ?? '');
    $pass = trim($body['password'] ?? '');
    if (!$user || !$pass) fail('Credenciales requeridas');

    $db   = getDB();
    $stmt = $db->prepare('SELECT * FROM admins WHERE username = ?');
    $stmt->execute([$user]);
    $admin = $stmt->fetch();

    if (!$admin || $pass !== $admin['password']) {
        fail('Usuario o contraseña incorrectos', 401);
    }

    $_SESSION['admin_id']     = $admin['id'];
    $_SESSION['admin_user']   = $admin['username'];
    $_SESSION['admin_nombre'] = $admin['nombre'];
    ok(['nombre' => $admin['nombre'], 'username' => $admin['username']]);
}

// POST logout
if ($action === 'logout' && $method === 'POST') {
    session_destroy();
    ok();
}

// GET me
if ($action === 'me' && $method === 'GET') {
    requireAuth();
    ok(['nombre' => $_SESSION['admin_nombre'], 'username' => $_SESSION['admin_user']]);
}

// POST save — crear o actualizar
if ($action === 'save' && $method === 'POST') {
    requireAuth();
    $body = bodyJson();

    $id          = trim($body['id']          ?? '');
    $nombre      = trim($body['nombre']      ?? '');
    $categoria   = trim($body['categoria']   ?? '');
    $descripcion = trim($body['descripcion'] ?? '');
    $model_path  = trim($body['model_path']  ?? '');
    $color       = trim($body['color']       ?? '#e67e22');
    $destacado   = isset($body['destacado']) && $body['destacado'] ? 1 : 0;

    if (!$nombre)     fail('El nombre es obligatorio');
    if (!$model_path) fail('La ruta del modelo es obligatoria');
    if (!$id)         $id = genId();

    $db = getDB();
    // Verificar si existe
    $stmt = $db->prepare('SELECT id FROM herramientas WHERE id = ?');
    $stmt->execute([$id]);
    $exists = $stmt->fetch();

    if ($exists) {
        $sql = 'UPDATE herramientas SET nombre=?, categoria=?, descripcion=?, model_path=?, color=?, destacado=? WHERE id=?';
        $db->prepare($sql)->execute([$nombre, $categoria, $descripcion, $model_path, $color, $destacado, $id]);
        ok(['id' => $id, 'action' => 'updated']);
    } else {
        $sql = 'INSERT INTO herramientas (id,nombre,categoria,descripcion,model_path,color,destacado) VALUES (?,?,?,?,?,?,?)';
        $db->prepare($sql)->execute([$id, $nombre, $categoria, $descripcion, $model_path, $color, $destacado]);
        ok(['id' => $id, 'action' => 'created']);
    }
}

// DELETE delete
if ($action === 'delete' && $method === 'DELETE') {
    requireAuth();
    $id = $_GET['id'] ?? '';
    if (!$id) fail('ID requerido');
    $db = getDB();
    $db->prepare('DELETE FROM herramientas WHERE id = ?')->execute([$id]);
    ok(['deleted' => $id]);
}


// POST upload — subir archivo .glb
if ($action === 'upload' && $method === 'POST') {
    requireAuth();

    // Diagnóstico de error de subida
    if (empty($_FILES['model'])) {
        fail('No se recibió el archivo. Verifica que file_uploads=On en php.ini');
    }

    $errCode = $_FILES['model']['error'];
    if ($errCode !== UPLOAD_ERR_OK) {
        $errMessages = [
            UPLOAD_ERR_INI_SIZE   => 'Archivo demasiado grande (upload_max_filesize en php.ini)',
            UPLOAD_ERR_FORM_SIZE  => 'Archivo demasiado grande (MAX_FILE_SIZE del formulario)',
            UPLOAD_ERR_PARTIAL    => 'El archivo se subió parcialmente',
            UPLOAD_ERR_NO_FILE    => 'No se seleccionó ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'No hay carpeta temporal configurada',
            UPLOAD_ERR_CANT_WRITE => 'No se puede escribir en disco',
            UPLOAD_ERR_EXTENSION  => 'Una extensión PHP bloqueó la subida',
        ];
        fail($errMessages[$errCode] ?? 'Error desconocido al subir: código ' . $errCode);
    }

    $file = $_FILES['model'];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, ['glb', 'gltf'])) {
        fail('Solo se permiten archivos .glb o .gltf');
    }

    // Carpeta destino
    $modelsDir = __DIR__ . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR;
    if (!is_dir($modelsDir)) {
        if (!mkdir($modelsDir, 0755, true)) {
            fail('No se pudo crear la carpeta models/. Créala manualmente con permisos 755.');
        }
    }

    if (!is_writable($modelsDir)) {
        fail('La carpeta models/ no tiene permisos de escritura. Ejecuta: chmod 755 models/');
    }

    // Nombre seguro: conservar nombre original limpio
    $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', basename($file['name']));
    $dest     = $modelsDir . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        fail('move_uploaded_file falló. tmp=' . $file['tmp_name'] . ' dest=' . $dest);
    }

    ok(['path' => 'models/' . $safeName, 'filename' => $safeName]);
}

fail('Acción no reconocida', 404);

