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

            // 显示网页内容
            echo "
            <div class='container'>
                <h2>图片上传成功！</h2>
                <div class='content'>
                    <p><strong>图片地址:</strong> <a href='$fileUrl' target='_blank'>$fileUrl</a></p>
                    <p><strong>二维码地址:</strong> <a href='$qrcodeUrl' target='_blank'>$qrcodeUrl</a></p>
                    <img src='$qrcodeUrl' alt='二维码'/>
                </div>
                <a href='xiaobai.html' class='back-button'>返回上传页面</a>
            </div>
            ";
        } else {
            echo "<p style='color: red; text-align: center;'>文件上传失败</p>";
        }
    } else {
        echo "<p style='color: red; text-align: center;'>上传过程中出现错误</p>";
    }
} else {
    echo "<p style='color: red; text-align: center;'>未上传文件</p>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .container {
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        text-align: center;
        max-width: 600px;
        width: 100%;
    }
    h2 {
        color: #333;
        margin-bottom: 20px;
    }
    .content {
        margin-bottom: 30px;
    }
    .content img {
        max-width: 100%;
        height: auto;
        margin-top: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 5px;
    }
    .content a {
        color: #007bff;
        text-decoration: none;
    }
    .content a:hover {
        text-decoration: underline;
    }
    .back-button {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s;
    }
    .back-button:hover {
        background-color: #0056b3;
    }
</style>
