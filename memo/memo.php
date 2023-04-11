<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('DB_PASS', 'hxp9zWzqzpwXTIvK');
define("FILE_DIR", "/Applications/MAMP/htdocs/");

date_default_timezone_set('Asia/Tokyo');

$subject = null;
$content = null;
$memo_date = null;
$result = array();
$search_result = array();


if(isset($_POST['submit'])) {
    $subject = $_POST['subject'];
    $content = $_POST['content'];
    $memo_date = date('Y-m-d H:i:s');

    try{
        $db = new PDO('mysql:host=localhost; dbname=board; charset=utf8', 'root', DB_PASS);
        $sql = "INSERT INTO memo(subject, content, memo_date) VALUES(?,?,?)";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($subject, $content, $memo_date));

        $db = null;
        $stmt = null;

        header('Location: ./memo.php');

    } catch(PDOException $e) {
        echo $e->getMessage();

    }

}


try {
    $db = new PDO('mysql:host=localhost; dbname=board; charset=utf8', 'root', DB_PASS);
    $sql = "SELECT * FROM memo ORDER BY memo_date DESC";
    $stmt = $db->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $db = null;
    $stmt = null;

} catch(PDOException $e) {
    echo $e->getMessage();

}

if(isset($_POST['btn_search'])) {
    $search = $_POST['search'];

    try {
        $db = new PDO('mysql:host=localhost; dbname=board; charset=utf8','root',DB_PASS);
        $stmt = $db->prepare("SELECT * FROM memo WHERE subject LIKE ? ESCAPE '!'");
        # エスケープを行うために「！」を使用している。！が鍵のような役割
        $stmt->bindValue(1, '%' . preg_replace('/(?=[!_%])/', '!', $search) . '%', PDO::PARAM_STR);
        $stmt->execute();
        $search_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $db = null;
        $stmt = null;

    } catch(PDOException $e) {
        echo $e->getMessage();

    }
}

if(isset($_FILES['image']['tmp_name'])) {
    $image_upload = move_uploaded_file($_FILES['image']['tmp_name'], FILE_DIR.$_FILES['image']['name']);
    if($image_upload == false) {
        $error_msg = 'ファイルのアップロードに失敗しました。';
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Notes</title>
<link rel="stylesheet" href="memo.css">
</head>
<body>

<?php if(isset($_POST['btn_search'])): ?>

<h1><?php echo $_POST['search']; ?>の検索結果</h1>

<?php foreach($search_result as $value): ?>
<article>
    <time><?php echo date('Y/m/d', strtotime($value['memo_date'])); ?></time>
    <h2><?php echo $value['subject']; ?></h2>
    <p><?php echo nl2br($value['content']); ?></p>
</article>
<?php endforeach; ?>

<form method="post">
    <input type="submit" name="btn_back" value="戻る">
</form>

<?php else: ?>



<h1>MemoNotes</h1>

<form method="post" class="search">
    <input type="search" name="search" placeholder="search">
    <input type="submit" name="btn_search" value="検索">
</form>

<form method="post" enctype="multipart/form-data" class="main">
    <div>
        <input type="text" name="subject" placeholder="タイトル">
    </div>
    <div>
        <textarea name="content" id="content" placeholder="内容"></textarea>
    </div>
    <div>
        <input type="file" id="image" name="image">
    </div>
    <div class="sent">
        <input type="submit" name="submit" value="入力">
    </div>
</form>

<hr>

<?php if(isset($result)): ?>
<?php foreach($result as $value): ?>
    <article>
        <time><?php echo date('Y/m/d', strtotime($value['memo_date'])); ?></time>
        <a href="delete.php?memo_id=<?php echo $value['id']; ?>">削除</a>
        <a href="edit.php?memo_id=<?php echo $value['id']; ?>">編集</a>
        <h2><?php echo $value['subject']; ?></h2>
        <p><?php echo nl2br($value['content']); ?></p>
        <br>
    </article>
<?php endforeach; ?>

    <img src="http://localhost/<?php echo $_FILES['image']['name']; ?>">

<?php endif; ?>

<?php endif; ?>
</body>
</html>