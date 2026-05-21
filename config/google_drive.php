<?php
define('UPLOAD_DIR', '../assets/uploads/');

function uploadToDrive($file_temp, $file_name) {
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0777, true);
    }
    
    $target_path = UPLOAD_DIR . time() . '_' . basename($file_name);
    if (move_uploaded_file($file_temp, $target_path)) {
        return [
            'success' => true,
            'path' => $target_path,
            'drive_link' => $target_path
        ];
    }
    return ['success' => false];
}
?>