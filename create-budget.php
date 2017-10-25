<?php
    require_once 'include/config.php';    
    require_once 'class/staticFunc.php';

    if (isset($_POST['create_budget'])) {
        $event_name = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
        $date = filter_var($_POST['date'], FILTER_SANITIZE_STRING);

        $sql = 'insert into budgets values(:id, :user_id, :event_name, :date)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => NULL, ':user_id' => 1, ':event_name' => $event_name, ':date' => $date]);
        if ($stmt->rowCount() > 0) {
            staticFunc::redirect('/budget-items.php');
        }
    }
?>

<?php include 'partials/header.php'; ?>

<div class="container margin-top-xxxl">
	<div class="row">
		<div class="bordered centralize">
			<form action="create-budget.php" method="post">
                <h3 class="heading-desc">
                    Create Budget</h3>
                <div class="form-group">
                    <label for="title">Event Title</label>
                    <input type="text" class="form-control" name="title" placeholder="Event title">
                </div>
                <div class="form-group">
                    <label for="title">Event Date</label>
                    <input type="date" class="form-control" name="date" placeholder="Event Date">
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-info center-block" name="create_budget" value="Create Budget">
                </div>
            </form>
		</div>
	</div>
</div>

<?php include 'partials/footer.php'; ?>