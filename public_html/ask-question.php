<?php
	require_once(realpath(dirname(__FILE__) . "/../resources/config.php"));
	require_once(LIBRARY_PATH . "/templateFunctions.php");

	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		try{
			$question = $db->prepare('SELECT * FROM questions WHERE id = ?');
			$question->execute(array($_POST['id']));

			$row_count = $question->rowCount();
			if ($row_count > 0){
				$stmt = $db->prepare("UPDATE questions SET topic = ?, question = ?, name = ?, email = ? WHERE id = " . $_POST['id']);
			} else{
				$stmt = $db->prepare('INSERT INTO questions(topic, question, name, email) VALUES(?, ?, ?, ?)');
			}			
			$stmt->execute(array($_POST['topic'], $_POST['question'], $_POST['name'], $_POST['email']));
		
		} catch(PDOException $e){
			echo $e->getMessage();
		}

		header('Location: /question.php?id=' . $db->lastInsertId());
		die();

	} else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])){
		try{
			$stmt = $db->prepare('SELECT * FROM questions WHERE id = ?');
			$stmt->execute(array($_GET['id']));
			$question = $stmt->fetch();
		} catch(PDOException $e){
			echo $e->getMessage();
		}
		$variables = array(
			'id' => $_GET['id'],
			'name' => $question['name'],
			'email' => $question['email'],
			'topic' => $question['topic'],
			'question' => $question['question']
		);
		renderLayoutWithContentFile("ask.php", $variables);
	} else{
		$variables = array(
			'id' => '',
			'name' => '',
			'email' => '',
			'topic' => '',
			'question' => ''
		);
		renderLayoutWithContentFile("ask.php", $variables);
	}
?>