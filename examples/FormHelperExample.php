<?php
require realpath(__DIR__ . '/../') . '/vendor/autoload.php';

// start session
session_start();

require_once 'Entities/User.php';
$user = new \User();
$user->setUsername('max.mustermann');
$user->setFirstname('Max');
$user->setLastname('Mustermann');
$user->setEmail('max.mustermann@yourdomain.com');
$user->setAge(21);

use Naucon\Form\FormManager;
$formManager = new FormManager();
$form = $formManager->createForm($user, 'yourform');
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
        <?php echo $formHelper->formField('label', 'username'); ?>
        <?php echo $formHelper->formField('text', 'username'); ?>
    	<?php echo $formHelper->formField('error', 'username'); ?>
        <br/>
        <?php echo $formHelper->formField('label', 'firstname'); ?>
        <?php echo $formHelper->formField('text', 'firstname'); ?>
    	<?php echo $formHelper->formField('error', 'firstname'); ?>
		<br/>
        <?php echo $formHelper->formField('label', 'lastname'); ?>
        <?php echo $formHelper->formField('text', 'lastname'); ?>
    	<?php echo $formHelper->formField('error', 'lastname'); ?>
        <br/>
        <?php echo $formHelper->formField('label', 'email'); ?>
        <?php echo $formHelper->formField('text', 'email'); ?>
        <?php echo $formHelper->formField('error', 'email'); ?>
        <br/>
        <?php echo $formHelper->formField('label', 'age'); ?>
        <?php echo $formHelper->formField('text', 'age', array('maxlength' => 3) ); ?>
        <?php echo $formHelper->formField('error', 'age'); ?>
        <br/>
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
