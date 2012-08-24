<p><strong><?= $question ?></strong></p>

<?php if (isset($answer_list)) : ?>
	<form method="post">
		<?php foreach ($answer_list as $key => $value) : ?>
			<input type="radio" name="answer" value="<?= $key ?>"> <?= $value ?><br>
		<?php endforeach; ?>

		<br><input type="submit" value="Vote">
	</form>
<?php endif; ?>