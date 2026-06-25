<?php
declare(strict_types=1);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

const MAX_FILE_SIZE  = 10 * 1024 * 1024; // 10 MB
const ALLOWED_MIMES  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
const MIME_EXTENSION = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
];

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$file = $_FILES['image'] ?? null;

if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
    $msgs = [
        UPLOAD_ERR_INI_SIZE   => 'ไฟล์ใหญ่เกินขีดจำกัดของเซิร์ฟเวอร์',
        UPLOAD_ERR_FORM_SIZE  => 'ไฟล์ใหญ่เกินขีดจำกัดของฟอร์ม',
        UPLOAD_ERR_PARTIAL    => 'อัพโหลดไม่สมบูรณ์',
        UPLOAD_ERR_NO_FILE    => 'ไม่มีไฟล์ถูกส่งมา',
        UPLOAD_ERR_NO_TMP_DIR => 'ไม่พบ temp directory',
        UPLOAD_ERR_CANT_WRITE => 'เขียนไฟล์ไม่ได้',
    ];
    $code = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    echo json_encode(['error' => $msgs[$code] ?? 'อัพโหลดล้มเหลว']);
    exit;
}

if ($file['size'] > MAX_FILE_SIZE) {
    echo json_encode(['error' => 'ไฟล์ใหญ่เกิน 10 MB']);
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);

if (!in_array($mime, ALLOWED_MIMES, strict: true)) {
    echo json_encode(['error' => 'รองรับเฉพาะไฟล์ภาพ (JPG, PNG, GIF, WebP)']);
    exit;
}

$ext      = MIME_EXTENSION[$mime];
$filename = bin2hex(random_bytes(16)) . '.' . $ext;
$savePath = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $savePath)) {
    echo json_encode(['error' => 'บันทึกไฟล์ไม่สำเร็จ']);
    exit;
}

echo json_encode([
    'success'  => true,
    'filename' => $filename,
    'url'      => 'uploads/' . $filename,
]);
