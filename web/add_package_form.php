<!DOCTYPE html>
<html>
<body>

<form method="post" action="add_package.php">

    City:<br><input type="text" name="city" value="<?php echo isset($_POST['city']) ? $_POST['city'] : ''; ?>">
    <br/>

    Street:<br><input type="text" name="street" value="<?php echo isset($_POST['street']) ? $_POST['street'] : ''; ?>">
    <br/>

    Number:<br><input type="text" name="number" value="<?php echo isset($_POST['number']) ? $_POST['number'] : ''; ?>">
    <br/>

    Vol Length:<br><input type="text" name="length" value="<?php echo isset($_POST['length']) ? $_POST['length'] : ''; ?>">
    <br/>

    Vol Width:<br><input type="text" name="width" value="<?php echo isset($_POST['width']) ? $_POST['width'] : ''; ?>">
    <br/>

    Vol Height:<br><input type="text" name="height" value="<?php echo isset($_POST['height']) ? $_POST['height'] : ''; ?>">
    <br/>

    Weight:<br><input type="text" name="weight" value="<?php echo isset($_POST['weight']) ? $_POST['weight'] : ''; ?>">
    <br/>

    <input type="submit" value="Add package">
</form>

</body>
</html>