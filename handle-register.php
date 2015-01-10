<?php

require_once('lib/lib-password.php'); //password hashing lib - crpypt forward compat
require_once('lib/lib-core.php');
require_once('helpers/helper-mailsend.php');

$sgmail = new sgmail();
$isemail = filteremail($_POST['email']);

// TODO: use templates here

if (!$isemail) {
    require_once 'layout-headerlg.php';
    echo "Please enter a valid email. <br><br><a href='register.php'>Go Back</a>";
    require_once 'layout-footerlg.php';
    die();
}
if ((strlen($_POST['username']) > 15) || (strlen($_POST['password']) > 25) || (strlen($_POST['email']) > 50)) {
    require_once 'layout-headerlg.php';
    echo "Your username must not be over 15 characters, password must be under 25 characters but over 6 characters, and email must be under 50 charcaters. <br><br><a href='register.php'>Go Back</a>";
    require_once 'layout-footerlg.php';
    die();
}
if (strlen($_POST['username']) == 0 || strlen($_POST['password']) < 4 || strlen($_POST['email']) == 0) {
    require_once 'layout-headerlg.php';
    echo "Fields may not be left blank, password must be over 4 characters. <br><br><a href='register.php'>Go Back</a>";
    require_once 'layout-footerlg.php';
    die();
}
if (!ctype_alnum($_POST['username'])) {
    require_once 'layout-headerlg.php';
    echo "Your username must be alphanumerical (numbers and letters only). <br><br><a href='register.php'>Go Back</a>";
    require_once 'layout-footerlg.php';
    die();
}
/*
  if ($_POST['tos']!='accept') {
  require_once 'layout-headerlg.php';
  echo "You must accept the <a href='tos.php'>Terms of Service</a> in order to register.<br><br><a href='register.php'>Go Back</a>";
  require_once 'layout-footerlg.php';
  die();
  }
 */

$salt = mcrypt_create_iv(23, MCRYPT_DEV_URANDOM);
$rstr = mcrypt_create_iv(23, MCRYPT_DEV_URANDOM);

$reg = array(
    "username" => $mysqli->real_escape_string($_POST['username']),
    "email" => $mysqli->real_escape_string($_POST['email']),
    "password" => $mysqli->real_escape_string($_POST['password']),
    "rkey" => sha1($mysqli->real_escape_string($_POST['username']) . date('zjDygs') . $rstr));

//check if already exists
$ireg;
$ireg['1'] = sqlex('auth', 'email', 'username', $reg['username']);
$ireg['2'] = sqlex('auth', 'username', 'email', $reg['email']);
$ireg['3'] = sqlfetch('auth', 'valid', 'email', $reg['email']);



if (($ireg['1'] || $ireg['2']) && $ireg['3']) {
    require_once 'layout-headerlg.php';
    echo "Username/email already in use. <br><br><a href='register.php'>Go Back</a>";
    require_once 'layout-footerlg.php';
    die();
}

$opts = array(
    'cost' => 10,
    'salt' => $salt
);

$hashed = password_hash($reg['password'], PASSWORD_BCRYPT, $opts);
$reg['password'] = $hashed;
$active = ($regtype == "free" ? "1" : "0")

//$qr = "INSERT INTO `auth` (username,email,password,rkey,valid,ip) VALUES ('{$reg['username']}','{$reg['email']}','{$hashed}','{$reg['rkey']}','{$active}', '{$ip}');";
//$rr = $mysqli->query($qr) or showerror();
$qp = "INSERT INTO `auth` (username,email,password,rkey,valid,ip) VALUES (?,?,?,?,?,?)";
$st = $mysqli->prepare($qp) or showerror();
$st->bind_param('ssssss', $reg['username'], $reg['email'], $hashed, $reg['rkey'], $active, $ip) or showerror();
$st->execute() or showerror();

if ($regtype == 'email') {
    $sglink = "http://{$wsa}/activate.php?key=" . $reg['rkey'] . '&user=' . $reg['username'];
    $sgmsg = "Please validate your {$wsn} Account by clicking the link below or pasting it into your browser:<br>"
            . '<a href="' . $sglink . '">' . $sglink . '</a>'
            . "<br><br>If you did not register at {$wsn} (<a href='//{$wsa}'>{$wsn}</a>), please disregard this email."
            . "<br>";
    $to = $reg['email'];
    $sm = $sgmail->sendmail($to, '$wsn Account Validation', $sgmsg);


    require_once 'layout-headerlg.php';
    echo "Thanks for registering. Check your email for an activation link. You must activate your account before logging in (top right corner)";
    require_once 'layout-footerlg.php';
    die();
}
else {
    require_once 'layout-headerlg.php';
    echo "Thanks for registering. You may now login (top right corner)";
    require_once 'layout-footerlg.php';
    die();
}
