<?php
require dirname(__DIR__).'/vendor/autoload.php';

$before = '';
$after = '';

if (!empty($_POST['text'])) {
    $before = $_POST['text'];
    $after = Typofixer\Typofixer::fix($before);
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Typofixer</title>
	<style type="text/css">
		html {
			font-family: sans-serif;
			line-height: 1.15;
		}
		body {
			margin: 0;
		}
		form {
			padding: 1rem;
			box-sizing: border-box;
			display: flex;
			flex-direction: column;
			height: 100vh;
		}
		.textareas {
			display: flex;
			flex-grow: 1;
		}
		textarea {
			width: 50%;
			flex: 0 0 auto;
			box-sizing: border-box;
			padding: 1em;
			border: none;
			font-size: 100%;
			line-height: 1.3;
			font-family: monospace;
			resize: none;
		}
		textarea:first-child {
			background: #ddd;
		}
		textarea:focus {
			outline: none;
		}
		button {
			background: blue;
			color: white;
			padding: 1em;
			border: none;
			border-radius: 3px;
			font-family: inherit;
			font-weight: bold;
			font-size: 1.4rem;
		}
		p {
			margin-bottom: 0;
		}
	</style>
</head>
<body>
<form method="post">
	<div class="textareas">
		<textarea name="text"><?= htmlspecialchars($before) ?></textarea>
		<textarea><?= htmlspecialchars($after) ?></textarea>
	</div>
	<p>
		<button type="submit">Send</button>
	</p>
</form>
</body>
</html>