<?php
    require_once 'include/config.php';    
    require_once 'class/staticFunc.php';

    $sql = 'select * from budgets where user_id = :user_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => 1]);
    if ($stmt->rowCount() > 0) {
		$budget = $stmt->fetch();
    }
    var_dump($budget);
?>

<?php include 'partials/header.php'; ?>

<div class="container margin-top-xxxl">
	<div class="row">
		<div class="bordered centralize">
            <div class="panel panel-info">
                <div class="panel-heading"><?= $budget->event_name; ?></div>
            </div>
            <ul class="list-group">
                <li class="list-group-item"><input type="checkbox">Venue</li>
                <li class="list-group-item"><input type="checkbox">Chairs</li>
                <li class="list-group-item"><input type="checkbox">Tables</li>
                <li class="list-group-item"><input type="checkbox">Projector/Screen</li>
                <li class="list-group-item"><input type="checkbox">Power</li>
                <li class="list-group-item"><input type="checkbox">Sound</li>
                <li class="list-group-item"><input type="checkbox">Internet</li>
                <li class="list-group-item"><input type="checkbox">T-Shirt</li>
                <li class="list-group-item"><input type="checkbox">Photography</li>
                <li class="list-group-item"><input type="checkbox">Video Coverage</li>
                <li class="list-group-item"><input type="checkbox">Food</li>
                <li class="list-group-item"><input type="checkbox">Snack</li>
                <li class="list-group-item"><input type="checkbox">Drinks</li>
                <li class="list-group-item"><input type="checkbox">Accommodation</li>
                <li class="list-group-item"><input type="checkbox">Transportation</li>
                <li class="list-group-item"><input type="checkbox">Security</li>
                <li class="list-group-item"><input type="checkbox">Backdrop/Banner/Flex</li>
                <li class="list-group-item"><input type="checkbox">Sticker/Poster/Hand bills/Tags</li>
            </ul>
        </div>
	</div>
</div>

<?php include 'partials/footer.php'; ?>