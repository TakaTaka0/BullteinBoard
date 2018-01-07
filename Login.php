<?php

session_start();

$db['host'] = "localhost";
$db['user'] = "observer";
$db['pass'] = "test";
$db['dbname'] = "bullteinBoard";

$errorMessage = "";

if (isset($_POST["login"])) {
    // 1. ユーザIDの入力チェック
    // if (empty($_POST["userid"])) {  // emptyは値が空のとき
    //     $errorMessage = 'ユーザーIDが未入力です。';
    // } else if (empty($_POST["password"])) {
    //     $errorMessage = 'パスワードが未入力です。';
    // }

    if (empty($_POST["userName"])) {  // emptyは値が空のとき
        $errorMessage = 'ユーザー名が未入力です。';
    } else if (empty($_POST["password"])) {
        $errorMessage = 'パスワードが未入力です。';
    }




    if (!empty($_POST["userName"]) && !empty($_POST["password"])) {
        // 入力したユーザID/ユーザー名を格納
        $userid   = $_POST["userid"];
        $userName = $_POST["userName"];
        // 2. ユーザIDとパスワードが入力されていたら認証する
        $dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

        // 3. エラー処理
        try {
            $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

            // $stmt = $pdo->prepare('SELECT * FROM userData WHERE id = ?');
            // $stmt->execute(array($userid));
            // var_dump($stmt);

             $stmtName = $pdo->prepare('SELECT * FROM userData WHERE name = ?');
             $stmtName->execute(array($userName));


            $password = $_POST["password"];

            if ($row = $stmtName->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($password, $row['password'])) {
                    session_regenerate_id(true);

                    // 入力したIDのユーザー名を取得
                    $id = $row['id'];
                    $sql = "SELECT * FROM userData WHERE id = $id";  //入力したIDからユーザー名を取得
                    // $stmt = $pdo->query($sql);
                    // foreach ($stmt as $row) {
                    //     $row['name'];  // ユーザー名
                    // }
                    // $name = $row['name'];
                    // var_dump($name);
                    //
                    // $sqlName = "SELECT * FROM userData WHERE name = $name";
                    // $stmtName = $pdo->query($sqlName);

                    $_SESSION["USERID"] = $row['id'];
                    $_SESSION["NAME"] = $row['name'];
                    header("Location: Main.php");  // メイン画面へ遷移
                    exit();  // 処理終了
                } else {
                    // 認証失敗
                    $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
                }
            } else {
                // 4. 認証成功なら、セッションIDを新規に発行する
                // 該当データなし
                $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
            }
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
            //$errorMessage = $sql;
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
            // echo $e->getMessage();
        }
    }
}
?>

<html>
    <head>
            <meta charset="UTF-8">
            <title>ログイン</title>
    </head>
    <body>
        <h1>ログイン画面</h1>
        <form id="loginForm" name="loginForm" action="" method="POST">
                <legend>ログインフォーム</legend>
                <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
                <label for="userid">ユーザー名</label><input type="text" id="userName" name="userName" placeholder="ユーザー名を入力" value="<?php if (!empty($_POST["userName"])) {echo htmlspecialchars($_POST["userName"], ENT_QUOTES);} ?>">
                <br>
                <label for="password">パスワード</label><input type="password" id="password" name="password" value="" placeholder="パスワードを入力">
                <br>
                <input type="submit" id="login" name="login" value="ログイン">
        </form>
        <br>
        <form action="SignUp.php">
                <legend>新規登録フォーム</legend>
                <input type="submit" value="新規登録">
        </form>
    </body>
</html>
