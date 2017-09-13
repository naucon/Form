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
use Naucon\Form\FormCollectionInterface;
$formManager = new FormManager();
$form = $formManager->createFormCollection($products, 'yourforms', array('collection_type' => FormCollectionInterface::COLLECTION_TYPE_ANY));
$form->bind($_POST);

if ($form->isBound() && $form->isValid()) {
    // some action, like saving the data to database
    echo '<span>Your successfull message!</span><br/>';
} else {
    var_dump($form->getErrors());
}

?>

<form method="post">
	<input type=text name="_csrf_token" value="<?php echo $form->getSynchronizerToken(); ?>" />
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
    foreach ($form->getEntityContainerIterator() as $entityContainer) {
        $inputAttr = '';
        if ($entityContainer->getName() == 2) {
            $inputAttr = ' disabled="disabled"';
            $entityContainer->getEntity()->setProductNumber('');
        }
?>
            	<tr>
            		<td>
            			<input type="text" name="<?php echo $entityContainer->getFormName('product_number') ?>" value="<?php echo $entityContainer->getFormValue('product_number') ?>"<?php echo $inputAttr ?> />
            			<?php
            			    if ($entityContainer->hasError('product_number')) {
                                echo 'WRONG: ' . $entityContainer->getError('product_number')->getMessage();
            			    }
            			?>
            		</td>
            		<td>
            			<input type="text" name="<?php echo $entityContainer->getFormName('product_desc') ?>" value="<?php echo $entityContainer->getFormValue('product_desc') ?>"<?php echo $inputAttr ?> />
            			<?php
            			    if ($entityContainer->hasError('product_desc')) {
                                echo 'WRONG: ' . $entityContainer->getError('product_desc')->getMessage();
            			    }
            			?>
            		</td>
            		<td>
            			<input type="text" name="<?php echo $entityContainer->getFormName('price') ?>" value="<?php echo $entityContainer->getFormValue('price') ?>"<?php echo $inputAttr ?> />
            			<?php
            			    if ($entityContainer->hasError('price')) {
                                echo 'WRONG: ' . $entityContainer->getError('price')->getMessage();
            			    }
            			?>
            		</td>
            	</tr>
<?php
    }
?>
        	</tbody>
        </table>
    </fieldset>
    <fieldset>
        <input type="submit" name="send" value="Submit">
    </fieldset>
</form>

<?php

// close session
session_write_close();