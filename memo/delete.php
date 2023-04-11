<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('DB_PASS', 'password');


if(isset($_GET['memo_id']) && empty($_POST['memo_id'])){
    $memo_id = $_GET['memo_id'];

    try {
        $pdo = new PDO('mysql:host=localhost; dbname=board; charset=utf8', 'root', DB_PASS);
        $stmt = $pdo->prepare("SELECT * FROM memo WHERE id = ?");
        $stmt->bindValue(1,$memo_id);
        $stmt->execute();
        $reslut = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $pdo = null;
        $stmt = null;

    } catch(PDOException $e) {
        echo $e->getMessage();
    }

} elseif(isset($_POST['memo_id'])){
    $memo_id = $_POST['memo_id'];

    try {
        $db = new PDO('mysql:host=localhost; dbname=board; charset=utf8', 'root', DB_PASS);
        $stmt = $db->prepare('DELETE FROM memo WHERE id = ?');
        $stmt->bindValue(1, $memo_id);
        $delete_res = $stmt->execute();
    
        if($delete_res) {
            echo 'データの削除に成功しました。';
        } else {
            echo 'データの削除に失敗しました。';
        }
    
    } catch(PDOException $e) {
        echo $e->getMessage();
    
    }
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>削除完了</title>
    <link rel="stylesheet" href="memo.css">
  </head>
  <body>   
    <?php if(empty($delete_res)): ?>
    <form method="post">
        <p>以下のメモを本当に削除してもよろしいですか？</p>
        <?php foreach($reslut as $value): ?>
            <article>
                <time><?php echo date('Y/m/d', strtotime($value['memo_date'])); ?></time>
                <h2><?php echo $value['subject']; ?></h2>
                <p><?php echo nl2br($value['content']); ?></p>
            </article>
        <?php endforeach; ?>
        <input type="submit" name="btn_yes" value="はい" class="btn_yes">
        <input type="hidden" name="memo_id" value="<?php echo $_GET['memo_id']; ?>">
    </form>
    <?php endif; ?>

    <div class="home">
        <a class="home" href="memo.php">メモ一覧へ</a>
    </div>
    
  </body>
</html>
