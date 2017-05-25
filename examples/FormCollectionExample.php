<?php
require realpath(__DIR__ . '/../') . '/vendor/autoload.php';

// start session
session_start();

require_once 'Entities/CreditCard.php';
require_once 'Entities/DirectDebit.php';
$creditCardEntity = new \CreditCard();
$directDebitEntity = new \DirectDebit();
$paymentMethods = array('cc' => $creditCardEntity, 'dd' => $directDebitEntity);

use Naucon\Form\FormManager;
use Naucon\Form\FormCollectionInterface;
$formManager = new FormManager();
$forms = $formManager->createFormCollection($paymentMethods, 'payment_forms', array('collection_type' => FormCollectionInterface::COLLECTION_TYPE_ONE));
$forms->bind($_POST);

if ($forms->isBound() && $forms->isValid()) {
    echo '<span>Your successfull message!</span><br/>';
} else {
    var_dump($forms->getErrors());
}

?>
<form method="post">
	<input type=text name="_csrf_token" value="<?php echo $forms->getSynchronizerToken(); ?>" />
<?php
    foreach ($forms->getEntityContainerIterator() as $entityContainer) {
?>
<?php
        if ($entityContainer->getName()=='cc') {
?>
	<fieldset>
		<input type="radio" name="<?php echo $forms->getFormOptionName() ?>" value="<?php echo $entityContainer->getName() ?>" <?php if ($forms->isFormOptionSelected($entityContainer->getName())) echo ' checked="checked"' ?> />
		<label>Credit Card</label>
		<br/>

		<label>Card brand</label>
		<select name="<?php echo $entityContainer->getFormName('card_brand') ?>">
			<option<?php if ($entityContainer->getFormValue('card_brand') == 'VISA') echo ' selected="selected"' ?>>VISA</option>
			<option<?php if ($entityContainer->getFormValue('card_brand') == 'MC') echo ' selected="selected"' ?>>MC</option>
			<option<?php if ($entityContainer->getFormValue('card_brand') == 'AMAX') echo ' selected="selected"' ?>>AMAX</option>
		</select>
		<?php
		    if ($entityContainer->hasError('card_brand')) {
                echo 'WRONG: ' . $entityContainer->getError('card_brand')->getMessage();
		    }
		?>
		<br/>

		<label>Card holder</label>
		<input type="text" name="<?php echo $entityContainer->getFormName('card_holder_name') ?>" value="<?php echo $entityContainer->getFormValue('card_holder_name') ?>" />
		<?php
		    if ($entityContainer->hasError('card_holder_name')) {
                echo 'WRONG: ' . $entityContainer->getError('card_holder_name')->getMessage();
		    }
		?>
		<br/>

		<label>Card number</label>
		<input type="text" name="<?php echo $entityContainer->getFormName('card_number') ?>" value="<?php echo $entityContainer->getFormValue('card_number') ?>" />
		<?php
		    if ($entityContainer->hasError('card_number')) {
                echo 'WRONG: ' . $entityContainer->getError('card_number')->getMessage();
		    }
		?>
		<br/>

		<label>Expiration date</label>
		<input type="text" name="<?php echo $entityContainer->getFormName('expiration_date') ?>" value="<?php echo $entityContainer->getFormValue('expiration_date') ?>" />
		<?php
		    if ($entityContainer->hasError('expiration_date')) {
                echo 'WRONG: ' . $entityContainer->getError('expiration_date')->getMessage();
		    }
		?>
		<br/>
	</fieldset>
<?php
        } elseif ($entityContainer->getName() == 'dd') {
?>
	<fieldset>
		<input type="radio" name="<?php echo $forms->getFormOptionName() ?>" value="<?php echo $entityContainer->getName() ?>"<?php if ($forms->isFormOptionSelected($entityContainer->getName())) echo ' checked="checked"' ?> />
		<label>Direct Debit</label>
		<br/>

		<label>Account Holder</label>
		<input type="text" name="<?php echo $entityContainer->getFormName('account_holder_name') ?>" value="<?php echo $entityContainer->getFormValue('account_holder_name') ?>" />
		<?php
		    if ($entityContainer->hasError('account_holder_name')) {
                echo 'WRONG: ' . $entityContainer->getError('account_holder_name')->getMessage();
		    }
		?>
		<br/>

		<label>IBAN</label>
		<input type="text" name="<?php echo $entityContainer->getFormName('iban') ?>" value="<?php echo $entityContainer->getFormValue('iban') ?>" />
		<?php
		    if ($entityContainer->hasError('iban')) {
                echo 'WRONG: ' . $entityContainer->getError('iban')->getMessage();
		    }
		?>
		<br/>

		<label>BIC</label>
		<input type="text" name="<?php echo $entityContainer->getFormName('bic') ?>" value="<?php echo $entityContainer->getFormValue('bic') ?>" />
		<?php
		    if ($entityContainer->hasError('bic')) {
                echo 'WRONG: ' . $entityContainer->getError('bic')->getMessage();
		    }
		?>
		<br/>

		<label>Bank</label>
		<input type="text" name="<?php echo $entityContainer->getFormName('bank') ?>" value="<?php echo $entityContainer->getFormValue('bank') ?>" />
		<?php
		    if ($entityContainer->hasError('bank')) {
                echo 'WRONG: ' . $entityContainer->getError('bank')->getMessage();
		    }
		?>
		<br/>
	</fieldset>
<?php
        }
?>
<?php
    }
?>
    <fieldset>
        <input type="submit" name="send" value="Submit">
    </fieldset>
</form>

<?php

// close session
session_write_close();