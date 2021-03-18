<?php
    $editname = '';
    $edittex = '';
    $editnum = 0;
    $editpass = '';
    $del = 0;
    $date = date("Y/m/d H:i:s");
    $name = '';
    $comment = ''; 
    //＝＝＝＝＝＝＝＝＝＝＝DBに接続＝＝＝＝＝＝＝＝＝＝＝＝＝
    $dsn = "mysql:dbname=tb********;host=localhost";
    $user = "tb-******";
    $pass = "PASSWORD";
    $pdo = new PDO($dsn, $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    $sql = "CREATE TABLE IF NOT EXISTS mission_5_2" //もしまだこのテーブルが存在しないなら
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
    . "date DATETIME,"
    . "passw TEXT"
	.");";
	$stmt = $pdo->query($sql);

    //＝＝＝＝＝＝＝＝＝＝編集ボタンが押された時、フォームに名前とコメントを表示＝＝＝＝＝＝＝＝＝＝
    if(isset($_POST["submit3"])){
        $sql = 'SELECT * FROM mission_5_2';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchall();
        foreach ($results as $row){
            if($row[0] == $_POST["edit"] and $row[4] == $_POST["pass3"] and $row[4] != ""){ //パスワードが空じゃなくて投稿番号と送信された編集番号が等しい時
                $editnum = $row[0];         //編集する番号
                $editname = $row[1];        //編集する前の名前
                $edittex = $row[2];         //編集する前のコメント　　これらをフォームに<? phpで紐付けてる
                $editpass = $row[4];        //パスワード
            }
        }
    }

    //＝＝＝＝＝＝＝＝＝＝投稿をフォーム下に更新していく＝＝＝＝＝＝＝＝＝＝
    if(isset($_POST["submit"])){
        $comment = $_POST["tex"];
        $passw = $_POST["pass1"];
        $name = $_POST["name"];
        //----------------------編集番号が空だったら新規投稿処理------------------------------
        if(empty($_POST["result"])){
            if(!empty($_POST["name"]) and (!empty($_POST["tex"]))){
                $sql = $pdo -> prepare("INSERT INTO mission_5_2 (name, comment,date,passw) VALUES (:name, :comment,:date,:passw)");  //データ入力
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':passw', $passw, PDO::PARAM_STR);
                $sql -> execute();
            }   
           

        //-----------------------編集番号が入っていて、パスワードが同じの場合編集処理-------------------------------
        }else{
            $pass3 = $_POST["pass3"];
            $editnum = $_POST["result"];
            $sql = 'SELECT * FROM mission_5_2';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchall();
            foreach ($results as $row){
                if($row[0] == $editnum){        //投稿番号と編集番号が等しい時(編集する行の時) 
                    $id = $row[0];
	                $sql = 'UPDATE mission_5_2 SET name=:name,comment=:comment,date=:date WHERE id=:id';
	                $stmt = $pdo->prepare($sql);
	                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
	                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	                $stmt->execute();
                }
            }
        }
    }
    //＝＝＝＝＝＝＝＝＝＝＝＝＝＝削除処理＝＝＝＝＝＝＝＝＝＝＝＝＝＝
    if(isset($_POST["submit2"])){
        $del = $_POST["del"];
        $sql = 'SELECT * FROM mission_5_2';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchall();
        foreach($results as $row){
            if($del == $row[0] and $row[4] == $_POST["pass2"] and $row[4] != ""){
                $id = $row[0];
	            $sql = 'delete from mission_5_2 where id=:id';
	            $stmt = $pdo->prepare($sql);
	            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	            $stmt->execute();
            }       
        } 
    }
?>
<!doctype html>
<html lang="ja">
    <head>
        <meta charset = "UTF-8">
        <title>mission_5-2</title>
    </head>
    <body>
        自由に書いて投稿してね
        <form action="" method="post">
            <input type="text" name="name" placeholder="名前" value="<?php echo $editname; ?>">
            <input type="text" name="tex" placeholder="コメント" value="<?php echo $edittex; ?>">
            <input type="text" name="pass1" placeholder="パスワード" value="<?php echo $editpass; ?>">
            <input type="submit" name="submit"><br>
        
            <input type="text" name="del" placeholder="削除番号">
            <input type="text" name="pass2" placeholder="パスワード">
            <input type="submit" name="submit2" value="削除"><br>
            
            <input type="text" name="edit" placeholder="編集番号">
            <input type="text" name="pass3" placeholder="パスワード">
            <input type="submit" name="submit3" value="編集"><br>

            <input type="hidden" name="result" value="<?php echo $editnum; ?>">
        </form>
        <?php
            $sql = 'SELECT * FROM mission_5_2';  //SELECT「どの項目（列）のデータを検索するか」を指定する
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();   //fetchAllは結果データを全件まとめて配列で取得する
            foreach ($results as $row){     //$rowの中にはテーブルのカラム名が入る
                echo $row['id'].' '.$row['name'].' '.$row['comment'].' '.$row['date'];        
                echo "<hr>";
            } 
        ?>
    </body>
</html>