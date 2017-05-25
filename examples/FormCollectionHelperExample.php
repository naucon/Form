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
$forms = $formManager->createFormCollection($paymentMethods, 'your_payment_forms', array('collection_type' => FormCollectionInterface::COLLECTION_TYPE_ONE));
$forms->bind($_POST);
$forms->addFormOptionDefaultValues('dd');

if ($forms->isBound() && $forms->isValid()) {
    // some action, like saving the data to database
    echo '<span>Your successfull message!</span><br/>';
} else {
    var_dump($forms->getErrors());
}

?>

<?php
    use Naucon\Form\FormHelper;
    $formHelper = new FormHelper($forms);

    echo $formHelper->formStart();
    echo $formHelper->formTag('errors');

    foreach ($formHelper as $entityContainer) {
        if ($entityContainer->getName()=='cc') {
?>
    <fieldset>
    	<?php echo $formHelper->formOption('radio', 'cc' ); ?>
		<label>Credit Card</label>
		<br/>
        <?php echo $formHelper->formField('label', 'card_brand'); ?>
        <?php echo $formHelper->formChoice('select', 'card_brand', array('VISA', 'MC', 'AMAX') ); ?>
    	<?php echo $formHelper->formField('error', 'card_brand'); ?>
        <br/>
        <?php echo $formHelper->formField('label', 'card_holder_name'); ?>
        <?php echo $formHelper->formField('text', 'card_holder_name'); ?>
    	<?php echo $formHelper->formField('error', 'card_holder_name'); ?>
		<br/>
        <?php echo $formHelper->formField('label', 'card_number'); ?>
        <?php echo $formHelper->formField('text', 'card_number'); ?>
    	<?php echo $formHelper->formField('error', 'card_number'); ?>
        <br/>
        <?php echo $formHelper->formField('label', 'expiration_date'); ?>
        <?php echo $formHelper->formField('text', 'expiration_date'); ?>
        <?php echo $formHelper->formField('error', 'expiration_date'); ?>
        <br/>
    </fieldset>
<?php
        } elseif ($entityContainer->getName() == 'dd') {
?>
    <fieldset>
    	<?php echo $formHelper->formOption('radio', 'dd' ); ?>
		<label>Direct Debit</label>
		<br/>
        <?php echo $formHelper->formField('label', 'account_holder_name'); ?>
        <?php echo $formHelper->formField('text', 'account_holder_name'); ?>
    	<?php echo $formHelper->formField('error', 'account_holder_name'); ?>
        <br/>
        <?php echo $formHelper->formField('label', 'iban'); ?>
        <?php echo $formHelper->formField('text', 'iban'); ?>
    	<?php echo $formHelper->formField('error', 'iban'); ?>
		<br/>
        <?php echo $formHelper->formField('label', 'bic'); ?>
        <?php echo $formHelper->formField('text', 'bic'); ?>
    	<?php echo $formHelper->formField('error', 'bic'); ?>
        <br/>
        <?php echo $formHelper->formField('label', 'bank'); ?>
        <?php echo $formHelper->formField('text', 'bank'); ?>
        <?php echo $formHelper->formField('error', 'bank'); ?>
        <br/>
    </fieldset>
<?php
        }
    }
?>
    <fieldset>
        <?php echo $formHelper->formTag('submit', 'Submit'); ?>
        <?php echo $formHelper->formTag('reset', 'Reset'); ?>
    </fieldset>
<?php
    echo $formHelper->formEnd();

// close session
session_write_close();
