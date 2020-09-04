<!DOCTYPE html>
<html lang = "ja">
<head>
    <meta charset = "UTF-8">
    <title>mission_5-2</title>
</head>    
<body>
    <?php
        //データベース接続設定
        $dsn = 'mysql:dbname=データベース名;host=localhost';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
        //テーブル作成
        $sql = "CREATE TABLE IF NOT EXISTS tb_5"
        ."("
        ."id INT AUTO_INCREMENT PRIMARY KEY,"
        ."name char(32),"
        ."comment TEXT,"
        ."date DATETIME,"
        ."pass char(50)"
        .");";
        $stmt = $pdo -> query($sql);
        
        $date = date("Y/m/d/ H:i:s");
        
        //POST受信
        //送信ボタンが押されたとき
        if(!empty($_POST["submit"])){
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $pass = $_POST["pass"];
            $edit_post = $_POST["edit_post"];
        }
        //削除ボタンが押されたとき
        if(!empty($_POST["del"])){
            $delNo = $_POST["delNo"];
            $delpass = $_POST["delpass"];    
        }
        //編集ボタンが押されたとき
        if(!empty($_POST["edit"])){
            $edit = $_POST["editNo"];
            $editpass = $_POST["editpass"];    
        }
        
        //投稿フォーム
        //名前とコメントとパスワードが入力されていたら
        if(!empty($name).($comment).($pass)){
            //$edit_postが空か空でないかでモード切替
            //新規投稿モード
            if(empty($edit_post)){
                //データレコードに名前、コメント、日付、パスワードを挿入
                $sql = $pdo -> prepare("INSERT INTO tb_5 (name, comment,date,pass) VALUES (:name, :comment, :date, :pass)");
	            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
            	$sql -> execute();
            //編集モード    
            } else {
                //入力されているデータレコードを編集
            	$sql = 'UPDATE tb_5 SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
            	$stmt = $pdo->prepare($sql);
            	$stmt -> bindParam(':name', $name, PDO::PARAM_STR);
            	$stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
            	$stmt -> bindParam(':date', $date, PDO::PARAM_STR);
                $stmt -> bindParam(':pass', $pass, PDO::PARAM_STR);
            	$stmt -> bindParam(':id', $edit_post, PDO::PARAM_INT);
	            $stmt -> execute();
            }
        }
        
        //編集フォーム
        //編集対象番号とパスワードが入力されていたら 
        if(!empty($edit).($editpass)){
            //$editと同じ数字のidを選択
            $sql = 'SELECT * FROM tb_5 WHERE id=:id';
	        $stmt = $pdo -> prepare($sql);
	        $stmt -> bindParam(':id', $edit, PDO::PARAM_INT);
	        $stmt -> execute();
	        $results = $stmt -> fetchAll();
            foreach($results as $row){
                //登録しているパスワードと入力したパスワードが同じなら
                if($editpass == $row['pass']){
                    //変数に格納
                    $editNo = $row['id'];
                    $editname = $row['name'];
                    $editcomment = $row['comment'];
                    $editPass = $row['pass'];
                } else {
                    echo "パスワードが違います";
                }
            }
        }
        
        //削除フォーム  
        //削除対象番号とパスワードが入力されていたら
        if(!empty($delNo).($delpass)){
            //$delNoと同じ数字のidを選択
            $sql = 'SELECT * FROM tb_5 WHERE id=:id';
    	    $stmt = $pdo -> prepare($sql);
    	    $stmt -> bindParam(':id', $delNo, PDO::PARAM_INT);
    	    $stmt -> execute();
	        $results = $stmt->fetchAll();
	        foreach ($results as $row){
	            //登録しているパスワードと入力したパスワードが同じなら
	            if($delpass == $row['pass']){
	                //$delNoと同じ数字のidを削除
        	        $sql = 'DELETE FROM tb_5 WHERE id=:id';
        	        $stmt = $pdo->prepare($sql);
        	        $stmt->bindParam(':id', $delNo, PDO::PARAM_INT);
        	        $stmt->execute();
	            } else {
	                 echo "パスワードが違います";
	            }
	        }
        }
    ?>
    
    <form action ="" method = "post">
        <!-- 投稿 -->
        <!-- 編集内容は投稿欄に表示 -->
        <input type = "text" name = "name" placeholder = "名前" value = "<?php if(isset($editname)){echo $editname;} ?>">
        <br>
        <input type = "text" name = "comment" placeholder = "コメント" value = "<?php if(isset($editcomment)){echo $editcomment;} ?>">
        <br>
        <input type = "text" name = "pass" placeholder = "パスワード" value = "<?php if(isset($editPass)){echo $editPass;} ?>">
        <!-- モード切替判断用 -->
        <input type = "hidden" name = "edit_post" value = "<?php if(isset($editNo)){echo $editNo;} ?>">
        <input type = "submit" name = "submit" value = "送信">
        <br>
        <br>
        
        <!-- 削除 -->
        <input type = "text" name = "delNo" placeholder = "削除対象番号">
        <br>
        <input type = "text" name = "delpass" placeholder = "パスワード">
        <input type = "submit" name = "del" value = "削除">
        <br>
        <br>
        
        <!-- 編集 -->
        <input type = "text" name = "editNo" placeholder = "編集対象番号">
        <br>
        <input type = "text" name = "editpass" placeholder = "パスワード">
        <input type = "submit"  name = "edit" value = "編集">
        <hr>
    </form>
    
    <?php
        //投稿内容の表示
        $sql = 'SELECT * FROM tb_5';
    	$stmt = $pdo -> query($sql);
    	$results = $stmt -> fetchAll();
    	foreach ($results as $row){
    		echo $row['id'].'';
    		echo $row['name'].'';
    		echo $row['comment'].'';
    		echo $row['date'].'<br>';
    		echo '<hr>';
    	}
    ?>
</body>
</html>