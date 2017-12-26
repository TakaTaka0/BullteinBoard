<?php
// セッション開始
session_start();

$db['host'] = "localhost";  // DBサーバのURL
$db['user'] = "observer";  // ユーザー名
$db['pass'] = "test";  // ユーザー名のパスワード
$db['dbname'] = "bullteinBoard";  // データベース名

// エラーメッセージ、登録完了メッセージの初期化
$errorMessage  = "";
$signUpMessage = "";
$validUsername = "";
$validPassword = "";
$dsn           = "";

// 未入力状態でログインボタンが押された場合
if (isset($_POST["signUp"])) {
    // 1. ユーザIDの入力チェック
    if (empty($_POST["username"])) {  // 値が空のとき
       $errorMessage = 'ユーザーIDが未入力です。';
    } else if (empty($_POST["password"])) {
       $errorMessage = 'パスワードが未入力です。';
    } else if (empty($_POST["password2"])) {
       $errorMessage = 'パスワードが未入力です。';
    }

    // 入力状態でログインボタンが押された場合
    if (!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["password2"]) && $_POST["password"] === $_POST["password2"]) {

　　　　  // 入力したユーザIDとパスワードを格納
　　     $username = $_POST["username"];
　　     $password = $_POST["password"];
　　     　
　　     if (!preg_match('/^[0-9a-zA-Z]{2,32}$/', $_POST["username"])){
　　      $validUsername = "ユーザー名は2文字以上で入力してください。";
　　     }
　　     if (!preg_match('/^[0-9a-zA-Z]{8,32}$/', $_POST["password"])){
　　      $validPassword = "パスワードは8文字以上で入力してください。";
　　     }
　　     // 2. ユーザIDとパスワードが入力されていたら認証する
　　     if (preg_match('/^[0-9a-zA-Z]{8,32}$/', $_POST["password"])){
　　       $dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);
　　     }

        // 3. エラー処理
        try {
            $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

            $stmt = $pdo->prepare("INSERT INTO userData(name, password) VALUES (?, ?)");

            $stmt->execute(array($username, password_hash($password, PASSWORD_DEFAULT)));  // パスワードのハッシュ化を行う（今回は文字列のみなのでbindValue(変数の内容が変わらない)を使用せず、直接excuteに渡しても問題ない）
            $userid = $pdo->lastinsertid();  // 登録した(DB側でauto_incrementした)IDを$useridに入れる

            //$signUpMessage = '登録が完了しました。あなたの登録IDは '. $userid. ' です。パスワードは '. $password. ' です。';  // ログイン時に使用するIDとパスワード
            $_SESSION['NAME']=$username;
            $_SESSION['PASSWORD']=$password;
            $_SESSION['USERID']=$userid;

            if (isset($_SESSION['NAME'])) {
                echo '<script>
                         location.href = "Main.php";
                      </script>';
            }

        } catch (PDOException $e) {
              $errorMessage = '登録できませんでした';
              // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
              // echo $e->getMessage();
          }
    } else if ($_POST["password"] != $_POST["password2"]) {
              $errorMessage = 'パスワードに誤りがあります。';
      }
}
?>


<!doctype html>
<html>
    <head>
            <meta charset="UTF-8">
            <title>新規登録</title>
    </head>
    <body>
        <h1>新規登録画面</h1>
        <form id="loginForm" name="loginForm" action="" method="POST">
            <fieldset>
                <legend>新規登録フォーム</legend>
                <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
                <div><font color="#ff0000"><?php echo htmlspecialchars($validPassword, ENT_QUOTES); ?></font></div>
                <div><font color="#ff0000"><?php echo htmlspecialchars($validUsername, ENT_QUOTES); ?></font></div>
                <label for="username">ユーザー名</label><input type="text" id="username" name="username" placeholder="ユーザー名を入力" value="<?php if (!empty($_POST["username"])) {echo htmlspecialchars($_POST["username"], ENT_QUOTES);} ?>">
                <br>
                <label for="password">パスワード</label><input type="password" id="password" name="password" value="" placeholder="パスワードを入力">
                <br>
                <label for="password2">パスワード(確認用)</label><input type="password" id="password2" name="password2" value="" placeholder="再度パスワードを入力">
                <br>
                <input type="submit" id="signUp" name="signUp" value="新規登録">
            </fieldset>
        </form>
        <br>
        <form action="Login.php">
            <input type="submit" value="戻る">
        </form>
    </body>
</html>
