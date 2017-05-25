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

?>

<?php

	use Naucon\Form\FormHelper;
	$formHelper = new FormHelper($form);

	echo $formHelper->formStart();
	echo $formHelper->formTag('errors');

	foreach ($formHelper as $entityContainer) {
?>
	<fieldset>
		<?php echo $formHelper->formField('label', 'product_number'); ?>
		<?php echo $formHelper->formField('text', 'product_number'); ?><br/>
		<?php echo $formHelper->formField('error', 'product_number'); ?>
		<br/>

		<?php echo $formHelper->formField('label', 'product_desc'); ?>
		<?php echo $formHelper->formField('text', 'product_desc'); ?><br/>
		<?php echo $formHelper->formField('error', 'product_desc'); ?>

		<?php echo $formHelper->formField('label', 'price'); ?>
		<?php echo $formHelper->formField('text', 'price'); ?><br/>
		<?php echo $formHelper->formField('error', 'price'); ?>
	</fieldset>
	<fieldset>
		<?php echo $formHelper->formTag('submit', 'Submit'); ?>
		<?php echo $formHelper->formTag('reset', 'Reset'); ?>
	</fieldset>

<?php
	}
	echo $formHelper->formEnd();

// close session
session_write_close();
