<?php
require realpath(__DIR__ . '/../') . '/vendor/autoload.php';

// start session
session_start();

require_once 'Entities/Product.php';
$product1 = new \Product();
$product1->setProductId(1);
$product1->setProductNumber('V001');
$product1->setProductDesc('Apple');
$product1->setPrice(9.95);
$product2 = new \Product();
$product2->setProductId(2);
$product2->setProductNumber('V002');
$product2->setProductDesc('Banana');
$product2->setPrice(9.95);
$product3 = new \Product();
$product3->setProductId(3);
$product3->setProductNumber('V003');
$product3->setProductDesc('Orange');
$product3->setPrice(9.95);

$products = array($product1, $product2, $product3);

use Naucon\Form\FormManager;
$formManager = new FormManager();
$form = $formManager->createFormCollection($products, 'yourforms');
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

		if ($formHelper->isFirst()) {
			?>
			<fieldset>
				<table>
					<thead>
						<tr>
							<th>Product number</th>
							<th>Product description</th>
							<th>Price</th>
						</tr>
					</thead>
					<tbody>
			<?php
		}
		?>
		<tr>
			<td>
				<?php echo $formHelper->formField('label', 'product_number'); ?>
				<?php echo $formHelper->formField('text', 'product_number'); ?><br/>
				<?php echo $formHelper->formField('error', 'product_number'); ?>
			</td>
			<td>
				<?php echo $formHelper->formField('label', 'product_desc'); ?>
				<?php echo $formHelper->formField('text', 'product_desc'); ?><br/>
				<?php echo $formHelper->formField('error', 'product_desc'); ?>
			</td>
			<td>
				<?php echo $formHelper->formField('label', 'price'); ?>
				<?php echo $formHelper->formField('text', 'price'); ?><br/>
				<?php echo $formHelper->formField('error', 'price'); ?>
			</td>
		</tr>
		<?php
		if ($formHelper->isLast()) {
			?>
					</tbody>
				</table>
			</fieldset>
			<fieldset>
				<?php echo $formHelper->formTag('submit', 'Submit'); ?>
				<?php echo $formHelper->formTag('reset', 'Reset'); ?>
			</fieldset>

			<?php
		}
	}
	echo $formHelper->formEnd();

	// close session
	session_write_close();