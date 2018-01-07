<?php
session_start();
//require_once("SignUp.php");
//var_dump($_SESSION);
require_once('./usefulClass.php');

$var = new chkValiable;

// ログイン状態チェック
if (!isset($_SESSION["NAME"])) {
    header("Location: Logout.php");
    exit;
}

$signUpMessage = 'あなたのログインIDは' .$_SESSION['USERID']. 'です';
echo $signUpMessage;

$err_msg1 = "";
$err_msg2 = "";
$message ="";
$name = ( isset( $_POST["name"] ) === true ) ?$_POST["name"]: "";
$comment  = ( isset( $_POST["comment"] )  === true ) ?  trim($_POST["comment"])  : "";

$var = new chkValiable;
$var->dumpValiable($comment);

//$history = (isset( $_POST["history"] ) === true) ?$_POST["history"]: "";
$history = date("Y/m/d");
//投稿がある場合のみ処理を行う
if (  isset($_POST["send"] ) ===  true ) {
    if ( $name   === "" ) $err_msg1 = "名前を入力してください";

    if ( $comment  === "" )  $err_msg2 = "コメントを入力してください";

    if( $err_msg1 === "" && $err_msg2 === "" ){
        $fp = fopen( "data.txt" ,"a" );
        fwrite( $fp ,  $name."\t".$comment."\t".$history."\n");
        $message ="書き込みに成功しました。";


        $getData    = new DbManager;
        //$getData->pdo();
        //$gotData = $getData->select('SELECT * FROM userData');
        $getComment = 'test';
        $tableName  = 'userData';
        $columnName = 'comment';
        $comment    = $_POST["comment"];
        $userName   = $_POST["name"];
        $gotData    = $getData->insertTable($userName, $comment);
        $var->dumpValiable($gotData);
    }

}

$fp = fopen("data.txt","r");
$fpCount = fopen("data.txt","r");
for ($count=0; fgets($fpCount); $count++){
//var_dump($count);
if ($count >=10){
  fopen("data.txt","w");
    }
}
$dataArr= array();
while( $res = fgets( $fp)){
    $tmp = explode("\t",$res);
    $arr = array(
        "name"     =>$tmp[0],
        "comment"  =>$tmp[1],
        "history"  =>$tmp[2]
    );
    $dataArr[]= $arr;
}

?>

<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>メイン</title>
    </head>
    <body>
        <h1>メイン画面</h1>
        <!-- ユーザーIDにHTMLタグが含まれても良いようにエスケープする -->
        <p>ようこそ<u><?php echo htmlspecialchars($_SESSION["NAME"], ENT_QUOTES); ?></u>さん</p>  <!-- ユーザー名をechoで表示 -->


        <?php echo $message; ?>
        <form method="post" action="Main.php">
        名前：<input type="text" name="name" id="nameId" value="<?php echo $_SESSION["NAME"]; ?>" >
            <?php echo $err_msg1; ?><br>
            コメント：<input  name="comment" id="commentId" value="<?php echo $comment; ?>">
            <?php echo $err_msg2; ?><br>
        コメント日：<?php echo $history; ?>

<br>
          <input type="submit" name="send" value="クリック" >
          <input type="button" name="reset_chk" value="リセット" onclick="clearForm()">
        </form>

        <script type="text/javascript">

            function clearForm(){
                document.getElementById('nameId').value="";
                document.getElementById('commentId').value="";
            }

        </script>
        <dl>
          <?php foreach( $dataArr as $data ):?>
           <p><span><?php echo $data["name"]; ?></span> : <span><?php echo $data["comment"]; ?></span> @<span><?php echo $data["history"]; ?></span></p>
          <?php endforeach;?>
　　　　 </dl>
        <ul>
            <li><a href="Logout.php">ログアウト</a></li>
        </ul>
    </body>
</html>
