<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="survey.2.css?2" rel="stylesheet" />
<title>Janny application - soyposter.party</title>
</head>
<body>
<?php if(@$_SESSION['pow_passed'] !== true || @$_SESSION['pow_ip'] !== $_SERVER['REMOTE_ADDR']): ?>
<iframe src="/verify/" style="width:0;height:0;border:none;opacity:0"></iframe>
<?php endif; ?>
<h1>Janitor application</h1>
<p>we are looking for soyposter.party jannies, please fill out this application, good luck!</p>
<p>If you have applied make sure to post every now and then on the same IP, or else you will miss the notification.</p>
<?php
if (isset($_COOKIE['survey1_submitted'])) {
	echo "<div class='notice'><b>You've already submitted an application.</b></div>";
} else {
?>
<form action="response.php" method="post" name="janny_application" id="janny_application">
<input type="hidden" name="verify" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
<table id="table">
<!--  <tr>
    <td><label for="janny_email">What's your email?</label></td>
    <td width="70%"><input type="email" name="janny_email" id="janny_email" class="input" maxlength="254" autocomplete="email" required/></td>
  </tr> -->
  <tr>
    <td><label for="janny_username">What username would you like?</label></td>
    <td><input type="text" name="janny_username" id="janny_username" class="input" minlength="3" maxlength="30" required/></td>
  </tr>
  <tr>
    <td><label for="janny_namefag">What is your soysphere namefag?</label></td>
    <td><input type="text" name="janny_namefag" id="janny_namefag" class="input" minlength="3" maxlength="100" required/></td>
  </tr>
  <tr>
    <td><label for="janny_splinter">Which splinters do you use?<br />Do you janny on any of them?</label></td>
    <td><textarea name="janny_splinter" id="janny_splinter" class="input" minlength="6" maxlength="1000" required></textarea></td>
  </tr>
  <tr>
    <td><label for="janny_reason">Why do you think you'd be a good janitor?</label></td>
    <td><textarea name="janny_reason" id="janny_reason" class="input" minlength="10" maxlength="1000" required></textarea></td>
  </tr>
  <tr>
    <td>Captcha: <img src="response.php?action=get_captcha" width="74" height="20" name="captcha_image"></td>
	<td><input type="text" name="captcha" class="input" style="width: 50px;min-width:50px" minlength="5" maxlength="5" autocomplete="off" required>
	<input type="submit" value="Submit" />
	<input type="button" onclick="document.janny_application.captcha_image.src = 'response.php?action=get_captcha&' + Math.random()" value="Refresh captcha" /></td>
  </tr>
</table>
</form>
<?php
}
?>
</body>
</html>

