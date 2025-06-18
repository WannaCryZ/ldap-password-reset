<?php
/**
 * 修改密码
 * */
error_reporting(0);
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT']. '/function/common.php';
$username = $_POST['username']; // 从表单中获取用户名
$old_password = $_POST['old_password']; // 从表单中获取验证码
$new_password = $_POST['new_password']; // 从表单中获取验证码
$confirm_password = $_POST['confirm_password']; // 从表单中获取二次输入密码
//$email = $_POST['email']; // 从表单中获取邮箱地址

if ($username == null || $old_password == null || $new_password == null || $confirm_password == null) {
    header('Content-Type: application/json');
    echo "信息校验失败";
    header("Location: ../view/reset_check_error.html");
    exit;
}
//校验
if ($new_password != $confirm_password) { // 验证两次输入的密码是否一致
    header('Content-Type: application/json');
    echo json_encode([ // 提示错误信息
       'status' => 'error',
       'success' => false,
       'message' => '两次输入的密码不一致！'
    ]);
    exit; // 确保脚本终止执行
}

/**
 * SSHA加密算法
 * @param $password  需要加密的字符串
 * @return 返回加密好的字符串
 * */
function ldap_ssha($password)
{
    $salt = "";
    for ($i = 1; $i <= 10; $i++) {
        $salt .= substr('0123456789abcdef', rand(0, 15), 1);
    }
    $hash = "{SSHA}".base64_encode(pack("H*", sha1($password . $salt)) . $salt);
    return $hash;
}


// 连接LDAP服务器
$ldapconn = ldap_connect(LDAP_SERVER) or die("Could not connect to LDAP server");
ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3); // 设置LDAP协议版本为3
$ldapbind = @ldap_bind($ldapconn, LDAP_ADMIN_DN, LDAP_ADMIN_PASSWORD) or die("LDAP bind failed"); // 绑定LDAP管理员账号
// 查询用户信息
$filter = "(uid=". ldap_escape($username, "", LDAP_ESCAPE_FILTER). ")"; // 构造搜索过滤器
$search = ldap_search($ldapconn, LDAP_BASE_DN, $filter); // 执行搜索
$info = ldap_get_entries($ldapconn, $search); // 获取搜索结果
if ($info['count'] == 0) { // 用户不存在
    header('Content-Type: application/json');
    echo json_encode([ // 提示错误信息
      'status' => 'error',
      'success' => false,
      'message' => '用户不存在！'
    ]);
    ldap_close($ldapconn); // 关闭LDAP连接
    exit; // 确保脚本终止执行
}

$user_dn = $info[0]['dn']; // 获取用户的DN
//echo $user_dn;
// 验证用户旧密码是否正确
if (ldap_bind($ldapconn, $user_dn, $old_password)) { // 验证用户旧密码是否正确
    // 修改用户密码
    $new_password = ldap_ssha($new_password); // 调用ldap_ssha函数进行密码加密
    $password_attr = array("userPassword" => $new_password);
    if (ldap_modify($ldapconn, $user_dn, $password_attr)) { // 修改用户密码
        header('Content-Type: application/json');
        echo json_encode([ // 修改用户密码成功
           'status' =>'success',
           'success' => true,
           'message' => '密码修改成功！'
        ]);
        
        //写入日志
        $result = "";
        saveLog($username,"邮件修改密码","修改邮件密码成功",$result);
        //发送通知邮件
        include_once $_SERVER['DOCUMENT_ROOT']. '/function/send_mail.php';
        sendMailNotice($info[0]['mail'][0],$info[0]['givenname'][0],$username,$result);
        ldap_close($ldapconn); // 关闭LDAP连接
    } else { // 修改用户密码失败
        header('Content-Type: application/json');
        echo json_encode([
          'status' => 'error',
          'success' => false,
          'message' => '密码修改失败！'. ldap_error($ldapconn)
        ]);
        
        ldap_close($ldapconn); // 关闭LDAP连接
    }
    exit; // 确保脚本终止执行
} else { // 验证用户旧密码失败
    header('Content-Type: application/json');
    echo json_encode([
       'status' => 'error',
       'success' => false,
       'message' => '旧密码错误！'
    ]);
    ldap_close($ldapconn); // 关闭LDAP连接
}
exit; // 确保脚本终止执行


