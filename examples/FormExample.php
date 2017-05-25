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

    //var_dump( $userEntity );
} else {
    var_dump($form->getErrors());
}

$entityContainer = $form->getFirstEntityContainer();

?>

<form method="post">
	<input type=text name="_csrf_token" value="<?php echo $form->getSynchronizerToken(); ?>" />



    <fieldset>
        <label>Username</label>
        <input type="text" name="<?php echo $entityContainer->getFormName('username'); ?>" value="<?php echo $entityContainer->getFormValue('username'); ?>" />
		<?php
		    if ($entityContainer->hasError('username')) {
                echo 'WRONG: ' . $entityContainer->getError('username')->getMessage();
		    }
		?>
        <br/>

        <label>First name</label>
        <input type="text" name="<?php echo $entityContainer->getFormName('firstname'); ?>" value="<?php echo $entityContainer->getFormValue('firstname'); ?>" />
		<?php
		    if ($entityContainer->hasError('firstname')) {
                echo 'WRONG: ' . $entityContainer->getError('firstname')->getMessage();
		    }
		?>
        <br/>

        <label>Last name</label>
        <input type="text" name="<?php echo $entityContainer->getFormName('lastname'); ?>" value="<?php echo $entityContainer->getFormValue('lastname'); ?>" />
		<?php
		    if ($entityContainer->hasError('lastname')) {
                echo 'WRONG: ' . $entityContainer->getError('lastname')->getMessage();
		    }
		?>
        <br/>

        <label>E-Mail-Address</label>
        <input type="text" name="<?php echo $entityContainer->getFormName('email'); ?>" value="<?php echo $entityContainer->getFormValue('email'); ?>" />
		<?php
		    if ($entityContainer->hasError('email')) {
                echo 'WRONG: ' . $entityContainer->getError('email')->getMessage();
		    }
		?>
        <br/>

        <label>Age</label>
        <input type="text" name="<?php echo $entityContainer->getFormName('age'); ?>" value="<?php echo $entityContainer->getFormValue('age'); ?>" />
		<?php
		    if ($entityContainer->hasError('age')) {
                echo 'WRONG: ' . $entityContainer->getError('age')->getMessage();
		    }
		?>
        <br/>
    </fieldset>
    <fieldset>
        <input type="submit" value="Submit">
        <input type="reset" value="Reset">
    </fieldset>
</form>

<?php

// close session
session_write_close();