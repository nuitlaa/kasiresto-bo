<?php
date_default_timezone_set("Asia/Bangkok");
function uploadfile(string $page, string $field = 'foto'){
    $request    = \Config\Services::request();
    $validation = \Config\Services::validation();

    $file = $request->getFile($field);

    if (!$file || !$file->isValid()) {
        return false;
    }

    // ===============================
    // ✅ VALIDASI FILE
    // ===============================
    $rules = [
        $field => [
            'uploaded['.$field.']',
            'mime_in['.$field.',image/bmp,image/gif,image/jpeg,image/jpg,image/png,image/webp]',
            'max_size['.$field.',50000]',
        ],
    ];

    if (!$validation->setRules($rules)->withRequest($request)->run()) {
        return false;
    }

    // ===============================
    // ✅ SET FOLDER TUJUAN
    // ===============================
    $folderdestination = $page.'/'.md5(rand(1,999999999).date('YmdHis'));
    $uploadPath = FCPATH . 'f/' . $folderdestination;

    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    // ===============================
    // ✅ NAMA FILE BARU
    // ===============================
    $newName = date('YmdHis').rand(1111111,9999999).$file->getRandomName();

    // ===============================
    // ✅ UPLOAD FILE ORIGINAL
    // ===============================
    $file->move($uploadPath, $newName);

    // ===============================
    // ✅ IMAGE SERVICE
    // ===============================
    $image = \Config\Services::image();

    // ===============================
    // ✅ SETTING RESIZE & KOMPRES
    // ===============================
    $versions = [
        'thumb'  => ['w' => 150,  'h' => 150,  'q' => 60],
        'small'  => ['w' => 300,  'h' => 300,  'q' => 65],
        'medium' => ['w' => 600,  'h' => 600,  'q' => 70],
        'large'  => ['w' => 1200, 'h' => 1200, 'q' => 75],
    ];

    $ext  = pathinfo($newName, PATHINFO_EXTENSION);
    $name = pathinfo($newName, PATHINFO_FILENAME);

    $result = [];
    $result['original'] = $folderdestination.'/'.$newName;

    foreach ($versions as $key => $v) {

        $newResizeName = $name.'_'.$key.'.'.$ext;

        $image
            ->withFile($uploadPath.'/'.$newName)
            ->resize($v['w'], $v['h'], true, 'auto')
            ->save($uploadPath.'/'.$newResizeName, $v['q']);

        $result[$key] = $folderdestination.'/'.$newResizeName;
    }

    // ===============================
    // ✅ BUAT FILE index.php (SECURITY)
    // ===============================
    $indexPath = $uploadPath . '/index.php';
    if (!file_exists($indexPath)) {
        file_put_contents($indexPath, 'axbx');
    }

    // ===============================
    // ✅ RETURN SEMUA VERSI FILE
    // ===============================
    return $result['original'];
}

function multiupload(string $page, string $field = 'foto'){
    $request    = \Config\Services::request();
    $validation = \Config\Services::validation();
    $files      = $request->getFiles();

    if (!isset($files[$field])) {
        return false;
    }

    $results = [];

    foreach ($files[$field] as $file) {

        if (!$file->isValid()) {
            continue;
        }

        // ===============================
        // ✅ VALIDASI FILE
        // ===============================
        $rules = [
            $field => [
                'mime_in['.$field.',image/bmp,image/gif,image/jpeg,image/jpg,image/png,image/webp]',
                'max_size['.$field.',50000]',
            ],
        ];

        if (!$validation->setRules($rules)->run([$field => $file])) {
            continue;
        }

        // ===============================
        // ✅ SET FOLDER
        // ===============================
        $folderdestination = $page.'/'.md5(rand(1,999999999).date('YmdHis'));
        $uploadPath = FCPATH . 'f/' . $folderdestination;

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // ===============================
        // ✅ NAMA FILE BARU
        // ===============================
        $newName = date('YmdHis').rand(1111111,9999999).$file->getRandomName();

        // ===============================
        // ✅ UPLOAD ORIGINAL
        // ===============================
        $file->move($uploadPath, $newName);

        $image = \Config\Services::image();

        // ===============================
        // ✅ SETTING UKURAN & KOMPRES
        // ===============================
        $versions = [
            'thumb'  => ['w' => 150,  'h' => 150,  'q' => 60],
            'small'  => ['w' => 300,  'h' => 300,  'q' => 65],
            'medium' => ['w' => 600,  'h' => 600,  'q' => 70],
            'large'  => ['w' => 1200, 'h' => 1200, 'q' => 75],
        ];

        $ext  = pathinfo($newName, PATHINFO_EXTENSION);
        $name = pathinfo($newName, PATHINFO_FILENAME);

        $result = [];
        $result['original'] = $folderdestination.'/'.$newName;

        foreach ($versions as $key => $v) {

            $newResizeName = $name.'_'.$key.'.'.$ext;

            $image
                ->withFile($uploadPath.'/'.$newName)
                ->resize($v['w'], $v['h'], true, 'auto')
                ->save($uploadPath.'/'.$newResizeName, $v['q']);

            $result[$key] = $folderdestination.'/'.$newResizeName;
        }

        // ===============================
        // ✅ SECURITY index.php
        // ===============================
        $indexPath = $uploadPath . '/index.php';
        if (!file_exists($indexPath)) {
            file_put_contents($indexPath, 'axbx');
        }

        // ===============================
        // ✅ TAMBAHKAN KE HASIL MULTI
        // ===============================
        $results[] = $result;
    }

    return $results;
}


function safe_upload($file, $user_id, $maxWidth = 1600){
    if (! $file || ! $file->isValid()) {
        return false;
    }

    // Allowed mime types
    $allowed = ['image/jpeg', 'image/png', 'application/pdf'];
    if (! in_array($file->getMimeType(), $allowed)) {
        return false;
    }

    // Generate folder
    $hash  = substr(sha1($user_id . time()), 0, 16);
    $year  = date('Y');
    $month = date('m');

    $basePath = FCPATH . "file/user/$hash/$year/$month/";

    create_protected_folder(FCPATH . "file/");
    create_protected_folder(FCPATH . "file/user/");
    create_protected_folder(FCPATH . "file/user/$hash/");
    create_protected_folder(FCPATH . "file/user/$hash/$year/");
    create_protected_folder($basePath);

    // Generate filename
    $ext = strtolower($file->getExtension());
    $safeName = bin2hex(random_bytes(16)) . '.' . $ext;

    // Move file first
    if (! $file->move($basePath, $safeName)) {
        return false;
    }

    $finalPath = $basePath . $safeName;

    // Resize only if image
    if (in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
        $image = \Config\Services::image();

        $image->withFile($finalPath)
              ->resize($maxWidth, $maxWidth, true, 'auto')
              ->save($finalPath);
    }

    return "file/user/$hash/$year/$month/$safeName";
}

function safe_upload2($file, $user_id){
    // ==== 1. Validasi dasar ====
    if (! $file || ! $file->isValid()) {
        return false;
    }

    // ==== 2. HASH folder user ====
    $hash = substr(sha1($user_id . date('YmdHis')), 0, 16); // 16 karakter aman

    // ==== 3. Struktur folder ====
    $year  = date('Y');
    $month = date('m');

    $basePath = FCPATH . 'file/user/' . $hash . '/' . $year . '/' . $month . '/';

    // ==== 4. Buat folder berlapis ====
    create_protected_folder(FCPATH . 'file/');
    create_protected_folder(FCPATH . 'file/user/');
    create_protected_folder(FCPATH . 'file/user/' . $hash . '/');
    create_protected_folder(FCPATH . 'file/user/' . $hash . '/' . $year . '/');
    create_protected_folder($basePath);

    // ==== 5. Sanitasi nama file ====
    $ext = strtolower($file->getExtension());
    $safeName = bin2hex(random_bytes(10)) . '.' . $ext;

    // ==== 6. Pindahkan file ====
    if (! $file->move($basePath, $safeName)) {
        return false;
    }

    // ==== 7. Return path relative untuk database ====
    return 'file/user/' . $hash . '/' . $year . '/' . $month . '/' . $safeName;
}

function safe_multi_upload($files, $user_id, $maxWidth = 1600){
    $result = [];

    foreach ($files as $file) {
        $uploaded = safe_upload($file, $user_id, $maxWidth);
        if ($uploaded) {
            $result[] = $uploaded;
        }
    }

    return $result; // berupa array path file
}

function create_protected_folder($path){
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }

    $index = rtrim($path, '/') . '/index.php';
    if (! file_exists($index)) {
        file_put_contents($index, "<?php // Silence is golden.");
    }
}

function safe_delete($relativePath)
{
    $path = FCPATH . $relativePath;

    if (! file_exists($path) || ! is_file($path)) {
        return false;
    }

    unlink($path);

    // Clean up empty folders
    $dir = dirname($path);
    while ($dir !== FCPATH . 'file' && $dir !== '/' && $dir !== '.') {
        if (is_dir($dir) && count(scandir($dir)) <= 2) {
            rmdir($dir);
        }
        $dir = dirname($dir);
    }

    return true;
}
