<?php
require dirname(__DIR__).'/vendor/autoload.php';

use Typofixer\Fixer;
use Typofixer\Fixers;

$before = '';
$after = '';

if (!empty($_POST['text'])) {
    $before = $_POST['text'];
    $after = Fixer::fix($before);
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Typofixer</title>
	<style type="text/css">
		body {
			margin: 0;
			font-family: sans-serif;
		}
		.textareas {
			display: flex;
		}
		textarea {
			width: 50%;
			flex: 0 0 auto;
			height: calc(100vh - 50px);
			box-sizing: border-box;
			padding: 1em;
			border: none;
			font-size: 1em;
			line-height: 1.3em;
		}
		textarea:first-child {
			background: #ddd;
		}
	</style>
</head>
<body>
<form method="post">
	<div class="textareas">
		<textarea name="text"><?= htmlspecialchars($before) ?></textarea>
		<textarea><?= htmlspecialchars($after) ?></textarea>
	</div>
	<button type="submit">Send</button>
</form>
</body>
</html>