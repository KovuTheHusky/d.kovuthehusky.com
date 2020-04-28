<?php $success = false;
if ($_POST) {
    require __DIR__ . '/configuration.php';
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array('secret' => RECAPTCHA_TOKEN, 'response' => $_POST['g-recaptcha-response'], 'remoteip' => $_SERVER['REMOTE_ADDR']);
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ),
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if (json_decode($result)->success) {
        $success = true;
    }
} ?>
<!DOCTYPE html>
<html lang="en" style="overflow: hidden;">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Email - KovuTheHusky</title>
  <link rel="shortcut icon" href="https://kovuthehusky.com/images/favicon.ico" type="image/x-icon" />
  <link rel="shortcut icon" href="https://kovuthehusky.com/images/favicon.png" sizes="any" type="image/png">
  <link rel="stylesheet" href="https://kovuthehusky.com/assets/main.css">
  <script src="https://kovuthehusky.com/node_modules/jquery/dist/jquery.min.js"></script>
  <script src="https://kovuthehusky.com/node_modules/popper.js/dist/umd/popper.min.js"></script>
  <script src="https://kovuthehusky.com/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
  <script src='http://www.google.com/recaptcha/api.js'></script>
</head>
<body style="overflow: hidden; background-color: #343a40 !important;">
<?php if ($success) { ?>
  <div style="display: inline-block; margin: 0;height: 100%; width: 100%;">
    <div style="display: table; height: 100%; width: 100%; padding: 16px;">
      <p style="display: table-cell; vertical-align: middle; text-align: center;">
        <a href="mailto:<?php echo EMAIL; ?>"><?php echo EMAIL; ?></a>
      </p>
    </div>
  </div>
<?php } else { ?>
  <div style="position: fixed; top: 50%; left: 50%; margin-top: -39px; margin-left: -152px;">
    <div style="width:304px;height:78px;">
      <form id="form" method="post">
        <div class="g-recaptcha" data-theme="dark" data-sitekey="6LeOIFoUAAAAAN2iCVvXLxnND4qRudDfsT554NiQ" data-callback="recaptcha">
        </div>
      </form>
    </div>
  </div>
  <script>
  function recaptcha() {
    $('#form').submit();
  };
  </script>
  <?php } ?>
</body>
</html>