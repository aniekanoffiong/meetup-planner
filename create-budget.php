<?php
    if (isset($_POST['create_budget'])) {
        
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