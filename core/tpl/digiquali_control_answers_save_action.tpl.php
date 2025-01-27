<?php

if ($action == 'save') {
	$data = json_decode(file_get_contents('php://input'), true);

	$controldet = new ControlLine($db);
	$sheet->fetch($object->fk_sheet);
	$object->fetchObjectLinked($sheet->id, 'digiquali_sheet', '', '', 'OR', 1, 'sourcetype', 0);
	$questionIds = $object->linkedObjectsIds['digiquali_question'];

	foreach ($questionIds as $questionId) {
		$controldettmp = $controldet;
		//fetch controldet avec le fk_question et fk_control, s'il existe on l'update sinon on le crée
		$result = $controldettmp->fetchFromParentWithQuestion($object->id, $questionId);

		if ($result > 0 && is_array($result)) {
			$controldettmp = array_shift($result);
			//sauvegarder réponse
			if ($data['autoSave'] && $questionId == $data['questionId']) {
				$questionAnswer = $data['answer'];
			} else {
				$questionAnswer = GETPOST('answer' . $questionId);
			}

			if (!empty($questionAnswer)) {
				$controldettmp->answer = $questionAnswer;
			}

			//sauvegarder commentaire
			if ($data['autoSave'] && $questionId == $data['questionId']) {
				$comment = $data['comment'];
			} else {
				$comment = GETPOST('comment' . $questionId);
			}

			if (dol_strlen($comment) > 0) {
				$controldettmp->comment = $comment;
			}

			$question->fetch($questionId);
			$controldettmp->update($user);
		} else {
			$controldettmp = $controldet;

			$controldettmp->ref = $controldettmp->getNextNumRef();

			$controldettmp->fk_control  = $object->id;
			$controldettmp->fk_question = $questionId;

			//sauvegarder réponse
			if ($data['autoSave'] && $questionId == $data['questionId']) {
				$questionAnswer = $data['answer'];
			} else {
				$questionAnswer = GETPOST('answer' . $questionId);
			}

			if (!empty($questionAnswer)) {
				$controldettmp->answer = $questionAnswer;
			} else {
				$controldettmp->answer = '';
			}

			//sauvegarder commentaire
			if ($data['autoSave'] && $questionId == $data['questionId']) {
				$comment = $data['comment'];
			} else {
				$comment = GETPOST('comment' . $questionId);
			}

			if (dol_strlen($comment) > 0) {
				$controldettmp->comment = $comment;
			} else {
				$controldettmp->comment = '';
			}

			$question->fetch($questionId);

			$controldettmp->entity = $conf->entity;
			$controldettmp->insert($user);
		}
	}

	$object->call_trigger('CONTROL_SAVEANSWER', $user);
	setEventMessages($langs->trans('AnswerSaved'), []);
	header('Location: ' . $_SERVER['PHP_SELF'] . (dol_strlen(GETPOST('track_id')) > 0 ? '?action=saved_success' : '?id=' . GETPOST('id')));
	exit;
}
