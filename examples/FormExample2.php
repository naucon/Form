<?php
require realpath(__DIR__ . '/../') . '/vendor/autoload.php';

// start session
session_start();

require_once 'Entities/Product.php';
$product = new \Product();
$product->setProductId(1);
$product->setProductNumber('V001');
$product->setProductDesc('Apple');
$product->setPrice(9.95);



use Naucon\Form\FormManager;
$formManager = new FormManager();
$form = $formManager->createForm($product, 'yourforms');
$form->bind($_POST);

if ($form->isBound() && $form->isValid()) {
    // some action, like saving the data to database
    echo '<span>Your successfull message!</span><br/>';
} else {
    var_dump($form->getErrors());
}

$entityContainer = $form->getFirstEntityContainer();

?>

<form method="post">
	<input type=text name="_csrf_token" value="<?php echo $form->getSynchronizerToken(); ?>" />

	<fieldset>
		<label>Product number</label>
		<input type="text" name="<?php echo $entityContainer->getFormName('product_number') ?>" value="<?php echo $entityContainer->getFormValue('product_number') ?>" />
		<?php
		if ($entityContainer->hasError('product_number')) {
			echo 'WRONG: ' . $entityContainer->getError('product_number')->getMessage();
		}
		?>
		<br/>

		<label>Product description</label>
		<input type="text" name="<?php echo $entityContainer->getFormName('product_desc') ?>" value="<?php echo $entityContainer->getFormValue('product_desc') ?>" />
		<?php
			if ($entityContainer->hasError('product_desc')) {
				echo 'WRONG: ' . $entityContainer->getError('product_desc')->getMessage();
			}
		?>
		<br/>

		<label>Price</label>
		<input type="text" name="<?php echo $entityContainer->getFormName('price') ?>" value="<?php echo $entityContainer->getFormValue('price') ?>" />
		<?php
			if ($entityContainer->hasError('price')) {
				echo 'WRONG: ' . $entityContainer->getError('price')->getMessage();
			}
		?>
		<br/>
    </fieldset>

    <fieldset>
        <input type="submit" name="send" value="Submit">
		<input type="reset" value="Reset">
    </fieldset>
</form>

<?php

// close session
session_write_close();