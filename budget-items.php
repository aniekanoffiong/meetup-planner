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
                <h3 class="panel-header text-center">New Event</h3>
                <hr />
            </div>
            <ul class="list-group">
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Venue</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Chairs</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Tables</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Projector/Screen</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Power</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Sound</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Internet</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">T-Shirt</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Photography</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Video Coverage</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Food</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Snack</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Drinks</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Accommodation</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Transportation</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Security</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Backdrop/Banner/Flex</li>
                <li class="list-group-item"><input type="checkbox" class="margin-right-md">Sticker/Poster/Hand bills/Tags</li>
            </ul>
        </div>
	</div>
</div>

<?php include 'partials/footer.php'; ?>