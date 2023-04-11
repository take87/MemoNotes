<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('DB_PASS', 'password');

$memo_id = $_GET['memo_id'];

try {
    $pdo = new PDO('mysql:host=localhost; dbname=board; charset=utf8', 'root', DB_PASS);
    $stmt = $pdo->prepare('SELECT * FROM memo WHERE id = ?');
    $stmt->bindValue(1, $memo_id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo $e->getMessage();
}

if(isset($_POST['update'])){

    $subject = $_POST['subject'];
    $content = $_POST['content'];

    try {
        $pdo = new PDO('mysql:host=localhost; dbname=board; charset=utf8', 'root', DB_PASS);
        $stmt = $pdo->prepare("UPDATE memo set subject = ?, content = ? WHERE id = ?");
        $stmt->execute(array($subject, $content, $memo_id));
        $upload_res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "更新完了";
        
    } catch(PDOException $e) {
        echo $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>編集画面</title>
<link rel="stylesheet" href="memo.css">
</head>
<body>

<form method="post">
<?php if(isset($upload_res)):?>

<?php foreach($upload_res as $value): ?>
    <article>
        <time><?php echo date('Y/m/d', strtotime($upload_res['memo_date'])); ?></time>
        <input type="text" name="subject" value="<?php echo $upload_res['subject']; ?>">
        <textarea name="content"><?php echo $upload_res['content']; ?></textarea>
        <input type="submit" name="update" value="更新"> 
    </article>
<?php endforeach; ?>

<?php else: ?>

<?php foreach($result as $value): ?>
<article class="edit">
    <time><?php echo date('Y/m/d', strtotime($value['memo_date'])); ?></time>
    <input type="text" name="subject" value="<?php echo $value['subject']; ?>">
    <textarea name="content"><?php echo $value['content']; ?></textarea>
    <input type="submit" name="update" value="更新"> 
</article>
<?php endforeach; ?>
</form>

<?php endif; ?>

<div class="home">
    <a class="home" href="memo.php">メモ一覧へ</a>
</div>

</body>
</html>
