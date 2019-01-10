<?php
  // ここにDBに登録する処理を記述する
  // 登録処理

// 1.DB接続
$dsn = 'mysql:dbname=oneline_bbs;host=localhost';
//DSNとはData source Name どこに接続するかの情報
$user = 'root'; //誰が
$password = '';	//パスワードはなにか
$dbh = new PDO($dsn,$user,$password); //接続処理
//dbhとはDatabase handle
//データベースを扱うことができるやつ
//phpとDBの仲介をしてくれる
$dbh->query('SET NAMES utf8');
//文字コード設定

// 2.SQL実装
if(!empty($_POST)){ //POST送信かどうかの判別
	//POST送信が、empty（空白）じゃないとき、if文が試行される
	$nickname = $_POST['nickname'];
	$comment = $_POST['comment'];
	//$_POSTは連想配列である
	//INSERT INTO テーブル名 ( カラム名1, カラム名2, ...) VALUES ( 値1, 値2, ... ) ;
	$sql = 'INSERT INTO`posts`(`nickname`,`comment`,`created`)VALUES(?,?,NOW())';
	//?を使う理由
	//SQLインジェクション対策
	//now()はSQLの関数 現在日時を算出
	$data = [$nickname,$comment];
	//これでも同じ意味$data = array($nickname,$commnet);
	$stmt = $dbh->prepare($sql);
	$stmt->execute($data);
	//ここで初めてSQLが実行される
}

// 一覧表示
//投稿情報をすべて取得する
// SELECT * FROM テーブル名;
$sql = 'SELECT * FROM `posts`';
$stmt = $dbh->prepare($sql);
$stmt->execute();
// SQL文に?がないので$data渡す必要なし

$posts = []; //取得したデータを格納するための配列
while(true){ //全レコードを取得する
	$record =  $stmt->fetch(PDO::FETCH_ASSOC);
	//1行ずつ処理
	if($record == false){
		//レコードが存在しないときfalseになる
		break;
	}
	$posts[] = $record;
	//配列にレコードを追加
}

//echo '<pre>';
//var_dump($posts);
//echo '</pre>'
?>


<!DOCTYPE html>
	<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>セブ掲示版</title>
</head>
<body>
	<!-- formタグにはmethodとaction必須-->
	<!--
		method:送信方法：どうアクセスするか
		action:送信先アクセスする場所
		acitionが空白の場合、自分自身に値が戻る
	-->
    <form method="post" action="">
    	<!-- formタグ内のinputタグやtextareaタグのname属性が$_POSTのキーになる
    	-->
      <p><input type="text" name="nickname" placeholder="nickname"></p>
      <p><textarea type="text" name="comment" placeholder="comment"></textarea></p>
      <p><button type="submit" >つぶやく</button></p>
    </form>
    <!-- ここにニックネーム、つぶやいた内容、日付を表示する -->
    <!-- 一覧表示  -->
    <!-- 投稿情報を全て表示する = 一件ずつ繰り返し表示処理をする $postsは配列なので、foreachがつかえる
    	foreach($配列名 as $任意の変数名
    	foreach(複数形 as 単数形)
    -->

    <?php foreach ($posts as $post): ?>
    	<p><?php echo $post['nickname']; ?></p>
    	<!-- 日付 -->
    	<p><?php echo $post['created']; ?></p>
    	<!-- コメント -->
    	<p><?php echo $post['comment']; ?></p>
    	<hr>
    <?php endforeach; ?>
   <!--  -->


</body>
</html>