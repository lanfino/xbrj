<?php
// 引入 phpqrcode 库
include 'phpqrcode/qrlib.php';

// 设置上传目录
$uploadDir = 'uploads/'; // 请确保该目录存在并且有写权限
$qrcodeDir = 'qrcodes/'; // 二维码保存目录

// 检查并创建目录
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
if (!file_exists($qrcodeDir)) {
    mkdir($qrcodeDir, 0755, true);
}

// 检查是否有文件上传
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    
    // 检查上传是否有错误
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($file['name']);
        $uploadFilePath = $uploadDir . $fileName;

        // 将文件从临时目录移动到目标目录
        if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
            // 获取文件的真实URL
            $fileUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $uploadFilePath;

            // 生成高质量二维码并保存
            $qrcodeFileName = $qrcodeDir . uniqid() . '.png';
            $errorCorrectionLevel = 'H'; // 纠错级别: L, M, Q, H (从低到高)
            $matrixPointSize = 10; // 二维码尺寸，值越大二维码越大
            
            // 生成二维码
            QRcode::png($fileUrl, $qrcodeFileName, $errorCorrectionLevel, $matrixPointSize, 2);

            // 获取二维码的URL
            $qrcodeUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $qrcodeFileName;

            echo json_encode(['status' => 'success', 'url' => $fileUrl, 'qrcode_url' => $qrcodeUrl]);
        } else {
            echo json_encode(['status' => 'error', 'message' => '文件上传失败']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => '上传过程中出现错误']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => '未上传文件']);
}
