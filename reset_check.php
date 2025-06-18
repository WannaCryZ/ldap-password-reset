<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
$username = $_GET['username']; // 从表单中获取用户名
$code = $_GET['code']; // 从表单中获取验证码
//$email = $_POST['email']; // 从表单中获取邮箱地址
if($username == null ||$code ==null){
    echo "信息校验失败";
    exit;
}
// 连接数据库
$conn = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
if ($conn->connect_error) {
    die("连接失败: ". $conn->connect_error);
}
// 查询数据库中是否存在该用户名和验证码
$sql = "SELECT * FROM reset_pass_temp WHERE username = '$username' AND code = '$code' AND expire_time > NOW()"; // 假设表名为 reset_password
$result = $conn->query($sql);
if ($result->num_rows > 0) { // 验证通过，跳转到密码修改页面
    header("Location: reset_password.html?username=$username&code=$code"); // 假设密码修改页面为 reset_password.php
    exit; // 确保脚本终止执行
} else { // 验证失败，提示错误信息
    header("Location: ./view/reset_check_error.html");
}