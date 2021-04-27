<!DOCTYPE html>
<html>

<head>
  <title>LoOped.com</title>
</head>
<style>
  h2 {
    font-weight: 200;
    color: #31326f;
  }

  p {
    color: #3b3b44;
    margin: 20px 0;
  }

  .user-agent {
    font-weight: bold;
  }

  .reset-link {
    display: block;
    text-decoration: none;
    background-color: #31326f;
    color: #fff;
    width: 150px;
    font-size: 18px;
    padding: 10px 8px;
    cursor: pointer;
  }
</style>

<body>
  <h2>Hello, {{ $details['name'] }}</h2>
  <p>A request has been recieved to change the password for your account.</p>
  <p class="user-agent">Made from: {{ $details['userAgent'] }}</p>
  <a class="reset-link" href={{url($details['URL'])}}>Reset Password</a>
  <p>This link will expire in 24 hours...</p>
  <p>If you did not initiate this request, please contact us immediately at</p>
  <a href="mailto:support@looped.com">support@looped.com</a>
</body>

</html>