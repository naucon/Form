naucon Form Package
===================

About
-----
The Package provides a form component for php to process and validate html forms. A form helper is also included to render your form in html markup (php oder smarty templates).

One of the most common tasks in a web developing are forms. This package helps you to process and validate forms in an easy way to maintain and secure.

The package can be integrated into any php web application (min. php5.5+).


### Features

* Binding form data to an entity by getter and setter
* validate form data by validation rule defined in the entity
* secure forms with synchronizer token to prevent XSRF/CSRF vulnerabilities
* translate form labels and form violations
* form helper to render html markup
* rendering html forms with output filtering to prevent XSS vulnerabilities
* smarty plugins for form helper


### Compatibility

* PHP5.5+


Installation
------------

install the latest version via composer

    composer require naucon/form


Examples
--------

Start the build-in webserver to see the examples in action:

    cd examples
    php -S 127.0.0.1:3000

open url in browser

    http://127.0.0.1:3000/index.html


Get Started
-----------

#### 1. Entity

First we need an entity. The entity can be a plain php class with getter and setter. The attributes of the class have to be accessible through getters and setters or by being public.
The submitted form data will be mapped to an instance of this entity.

	<?php
		class User
		{
		    protected $username;
		    protected $email;
		    protected $newsletter;

		    public function getUsername()
		    {
    		    return $this->username;
    		}

    		public function setUsername($username)
    		{
    		    $this->username = $username;
    		}

		    public function getEmail()
		    {
		        return $this->email;
		    }

    		public function setEmail($email)
    		{
    		    $this->email = $email;
    		}

		    public function getNewsletter()
		    {
    		    return $this->newsletter;
    		}

		    public function setNewsletter($newsletter)
		    {
    		    $this->newsletter = $newsletter;
    		}
    	}
	}

Create an instance of the entity to assign it to your form.

	require_once 'User.php';
	$user = new User();


#### 2. Form binding 

Next we get an instance of the form manager class and create a form instance which performs the actual form processing.

Add an entity to the form class as first parameter.

		use Naucon\Form\FormManager;
		$formManager = new FormManager();
        $form = $formManager->createForm($user, 'yourform');

As second parameter provide a name or namespace for the form. In the example above we use `yourform`. This unique name is a kind of a namespace or a key to make sure that your form does not interfere with other forms.

The CSRF-Token requires a session. So make sure you have started a session.

	<?php
		session_start();

		use Naucon\Form\FormManager;
		$formManager = new FormManager();
        $form = $formManager->createForm($user, 'yourform');
		...

Now we can bind submitted form data. Therefore we call the method `bind()` and add the submitted form data eg. `$_POST` or any other array as parameter.
To ensure that the form works correctly you should allways call the `bind()` method, even if no form data were submitted.
With `isBound()` you can verify that the submitted form data are bound correctly to an entity.

If the entity was bound, you can perform any further action with the entity - like save to database.

		$form->bind(isset($_POST) ? $_POST : null);

		if ($form->isBound()) {
		    // some action, like saving the data to database
		}

Make sure that the session will be closed to write the session data to the session storage.
		
		// here rendered the form html!
	
		session_write_close();
	?>


#### 3. XSRF/CSRF Protection

When `bind()` is submitted the data will only be processed if a synchronizer token (also called secure token or xsrf/csrf token) was submitted and is valid. This token prevents XSRF/CSRF attacks by setting a random token to the session when a form is presented. In the form the token is added to a hidden field. When the form data are submitted the token is part of the payload and will be compared to the previously stored token in the session. The binding will only be processed if these two token are identical.

	<form method="post">
		<input type=text name="_csrf_token" value="<?php echo $form->getSynchronizerToken(); ?>" />

The markup looks like this:

	<form method="post" id="yourform">
		<input type="hidden" name="_csrf_token" value="67187c7e5abeb31b93339ab5cbb263b6" />

The XSRF/CSRF protection can be disabled when calling the method `setSynchronizerTokenValidation()` with parameter `false`.

	$formManager->setSynchronizerTokenValidation(false);

or

	$form->setSynchronizerTokenValidation(false);


#### 4. Validation

To validate the submitted form data we use the Symfony Validator Component.
The constrains (validation rules) are defined in a static method `loadValidatorMetadata()`, yml file, xml file or annotation within the entity.

How to define constrains and which validators are available, please have a look at the Symfony Validator documentation: https://symfony.com/doc/2.8/validation.html

In our example we add a static method `loadValidatorMetadata()` to our `User` entity:

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('username', new Assert\NotBlank());
        $metadata->addPropertyConstraint('username', new Assert\Length(
            array('min' => 5, 'max' => 50)
        ));
        $metadata->addPropertyConstraint('email', new Assert\Email());
        $metadata->addPropertyConstraint('age', new Assert\Type('numeric'));
    }

You can also use yml, xml files or annotations. For more information how to define constrains, have a look at the Symfony Validator documentation: http://symfony.com/doc/current/components/validator.html

        $options['config_paths'] = array(__DIR__ . '/Resources/config/');

        $formManager = new FormManager($options);

In case you need to exclude certain validation constraints for a particular use case, or you would like to apply only a condition subset by configuration, you may use **validation groups** to do so:

        $options['validation_groups'] = array('Default', 'registration');

If an array of validation group names is supplied in the form options, then only constraints marked with those group names will be applied when validating the form.
To find out more about validation group configuration, please see the documentation at https://symfony.com/doc/current/validation/groups.html.

Next we call `isValid()` after `bind()` and `isBound()`. In other words we validate the entity after it is bound.

		$form->bind(isset($_POST) ? $_POST : null);

		if ($form->isBound() 
			&& $form->isValid()) {
		    // some action, like saving the data to database
		}
		
		// here rendered the form html!
	
		session_write_close();
	?>

Validation errors stored in a `FormError` instance. They can be accessed through the following methods:

	$form->getErrors()
	$entityContainer->hasError('username')
	$entityContainer->getError('username')
	$entityContainer->getError('username')->getMessage();

`$entityContainer` is an instance of `EntityContainer`. It is not the entity `User`. The `EntityContainer` contains the entity. It can be acceessed by calling `EntityContainer::getEntity()`.

If your form contains only one entity - you can access the EntityContainer by calling:

	$form->getFirstEntityContainer()

If your form contains multiple entities you can iterate through the EntityContainer:

	foreach ($form->getEntityContainerIterator() as $entityContainer) {
        ...
    }

Entity container can also be iterated through a form instance.

	foreach ($form as $entityContainer) {
        ...
    }

#### 5. Translation

To translate form label and validation violations we use the symfony translator component.

To change the current language call `setLocales`:

    $form->getTranslator()->setLocale('de_DE');


You can specify the current and a fallback language in the configuration:

    $options = [
        'locale' => 'en_EN',
        'fallback_locales' => array('en_EN'),
    ];

    $formManager = new FormManager($options);


The default translations for the contrains from the form and symfony components are loaded by default.
To add translations form labels or your own contrains you have to specify one ore more pathes where to find these translations:

    $paths = array(realpath(__DIR__) . '/Resources/translations');
    $options['translator_paths' => $paths];

    $formManager = new FormManager($options);



#### 6. Form rendering

##### With Form Helper

With the form helper you can easily render a html form field, labels, buttons without worrying about field names and values.

The following form helpers are available and can be accessed through the `FormHelper` instance or smarty plugins.

* Choices
	* "checkbox"
	* "radio"
	* "select"
* Fields
	* "error"
	* "hidden"
	* "label"
	* "password"
	* "text"
	* "textarea"
* Tags
	* "errors"
	* "reset"
	* "submit"


**Limitations:** At the moment the form helper do not consider different html standards like html4.01, xhtml, html5 etc. Also some html markup notations are missing.


##### FormHelper with php templates

First you have to create an instance of the `FormHelper` class. The form helper requires an instance of a `Form` instance.

	<?php
		use Naucon\Form\FormHelper;
	    $formHelper = new FormHelper($form);


Form helper contains the following methodes to render form elements:

* formStart($method='post', $action=null, $enctype=null, $options=array())
* formEnd()
* formField($helperName, $propertyName, array $options=array())
* formChoice($helperName, $propertyName, $choices, array $options=array())
* formTag($helperName, $content=null, array $options=array())

Example how to render a form with FormHelper:

    echo $formHelper->formStart();
    echo $formHelper->formTag('errors');

    foreach ($formHelper as $entityContainer) {
	?>
	    <fieldset>
	        <?php echo $formHelper->formField('label', 'username'); ?>
	        <?php echo $formHelper->formField('text', 'username', array('maxlength' => 32)); ?>
	    	<?php echo $formHelper->formField('error', 'username'); ?>
	        <br/>
	        <?php echo $formHelper->formField('label', 'email'); ?>
	        <?php echo $formHelper->formField('text', 'email'); ?>
	        <?php echo $formHelper->formField('error', 'email'); ?>
	        <br/>
	        <?php echo $formHelper->formField('label', 'newsletter'); ?>
	        <?php echo $formHelper->formField('checkbox', 'newsletter', 1); ?>
	        <?php echo $formHelper->formField('error', 'newsletter'); ?>
	        <br/>
	    </fieldset>
	    <fieldset>
	        <?php echo $formHelper->formTag('submit', 'Submit'); ?>
	        <?php echo $formHelper->formTag('reset', 'Reset'); ?>
	    </fieldset>
	<?php
    }
    echo $formHelper->formEnd();


##### FormHelper with Smarty3 Templates

First we have to add the form helper smarty plugins to smarty.

	$smarty->setPluginsDir('vendor/Naucon/Form/Helper/view/smarty');

Next we have to assign the form instance to smarty.

	$smarty->assign('form', $form);

The form package contains the following blocks and function plugins:

Plugins                        | Tags              | Attributes
:----------------------------- | :---------------- | :----------
smarty_block_ncform            | {ncform}{/ncform} | from, method?, action?, enctype?, id?, class?, style?
smarty_function_ncform_field   | {ncform_field}    | type, field, value?, maxlength?, id?, class?, style?
smarty_function_ncform_choice  | {ncform_choice}   | type, field, value?, choices?, id?, class?, style?
smarty_function_ncform_tag     | {ncform_tag}      | type, value?, id?, class?, style?


Example how to render a form in a smarty template:

	{ncform from=$form}
		{ncform_tag type='errors'}
		<fieldset>
			{ncform_field type='label' field='username'}
			{ncform_field type='text' field='username' maxlength=32}
			{ncform_field type='error' field='username'}
			<br/>
			{ncform_field type='label' field='email'}
			{ncform_field type='text' field='email'}
			{ncform_field type='error' field='email'}
			<br/>
			{ncform_field type='label' field='newsletter'}
			{ncform_choice type='radio' field='newsletter' value=1}
			{ncform_field type='error' field='newsletter'}
			<br/>
		</fieldset>
 		<fieldset>
 			{ncform_tag type='submit' value='Submit'}
 			{ncform_tag type='reset' value='Reset'}
 		</fieldset>
	{/ncform}

The example above renders the following html markup:

	<form method="post" id="yourform">
		<input type="hidden" name="_ncToken" value="67187c7e5abeb31b93339ab5cbb263b6" />
		<fieldset>
			<label for="yourform_username">username</label>
			<input type="text" name="yourform[username]" value="" id="yourform_username" maxlength="32" />
			<br/>
			<label for="yourform_email">email</label>
			<input type="text" name="yourform[email]" value="" id="yourform_email" />
			<br/>
			<label for="yourform_newsletter">newsletter</label>
			<input type="checkbox" name="yourform[newsletter]" value="1" id="yourform_newsletter" />
			<br/>
		</fieldset>
 		<fieldset>
 			<input type="submit" value="Submit" />
		 	<input type="reset" value="Reset" />
		</fieldset>
	</form>

##### without form helper

Without a form helper you have to add the form fields to your html markup by your self. Be careful to set the correct name attribute - otherwise the form data can not be binded.

The name attribute starts with the form name `yourform` followed by the property name in brackets `[username]`.

	<input type="text" name="yourform[username]" value="" />
	<input type="text" name="yourform[email]" value="" />
	<input type="checkbox" name="yourform[newsletter]" value="1" />

In a form with multiple entities the name attribute must contain also an entity name in brackets `[0]` between the form and property name:

	<input type="text" name="yourform[0][username]" value="" />
	<input type="text" name="yourform[1][username]" value="" />

Or you can ask the EntityContainer instance for the name attribute and value - if you like.

    <label>Username</label>
    <input type="text" name="<?php echo $entityContainer->getFormName('username'); ?>" value="<?php echo $entityContainer->getFormValue('username'); ?>" />
    <?php
        if ($formEntity->hasError('username')) {
            echo 'WRONG: ' . $formEntity->getError('username')->getMessage();
        }
    ?>
    <br/>

    <label>First name</label>
    <input type="text" name="<?php echo $entityContainer->getFormName('firstname'); ?>" value="<?php echo $entityContainer->getFormValue('firstname'); ?>" />
    <?php
        if ($formEntity->hasError('firstname')) {
            echo 'WRONG: ' . $formEntity->getError('firstname')->getMessage();
        }
    ?>
    <br/>

### Form Collections

To process (bind and validate) more than one form you can use a form collection. A form collection can be created through calling `createFormCollection()` from the `FormManager`.
As first parameter add an array of entities. The second parameter is the form name. The entities must not have the same time - but in the following example we do.

    $products = array(new Product(), new Product(), new Product());
    
    use Naucon\Form\FormManager;
    $formManager = new FormManager();
    $form = $formManager->createFormCollection($products, 'yourforms');
    $form->bind($_POST);

To render the form we iterate through the form entites like this:

    foreach ($form->getEntityContainerIterator() as $entityContainer) {


The behaviour of a form collection can be specified with the `collection_type` option. The value can be one of these options FormCollectionInterface::COLLECTION_TYPE_ALL, FormCollectionInterface::COLLECTION_TYPE_MANY, FormCollectionInterface::COLLECTION_TYPE_ONE.

* `FormCollectionInterface::COLLECTION_TYPE_ALL`: process all given form entities
* `FormCollectionInterface::COLLECTION_TYPE_ONE`: process only one of the given form entities. The one is specified through the form option.
* `FormCollectionInterface::COLLECTION_TYPE_MANY`: process some of the given form entities. The same are specified through the form option.


#### Collection type one

For example you have two forms with a different set of fields where the use have to choose from. We would use the COLLECTION_TYPE_ONE or COLLECTION_TYPE_MANY.
Add a third parameter with the collection type.

    $creditCardEntity = new CreditCard();
    $directDebitEntity = new DirectDebit();
    $paymentMethods = array('cc' => $creditCardEntity, 'dd' => $directDebitEntity);
    
    use Naucon\Form\FormManager;
    use Naucon\Form\FormCollectionInterface;
    $formManager = new FormManager();
    $forms = $formManager->createFormCollection($paymentMethods, 'payment_forms', FormCollectionInterface::COLLECTION_TYPE_ONE);
    $forms->bind($_POST);

For every form entity we add a radio button or checkbox like this:

    foreach ($forms->getEntityContainerIterator() as $entityContainer) {

        if ($entityContainer->getName()=='cc') {
    ?>
            <fieldset>
                <input type="radio" name="<?php echo $forms->getFormOptionName() ?>" value="<?php echo $entityContainer->getName() ?>" <?php if ($forms->isFormOptionSelected($entityContainer->getName())) echo ' checked="checked"' ?> />
                <label>Credit Card</label>
                <br/>
            </fieldet>

with helpers it would look like this:

    $formHelper = new FormHelper($form);

    echo $formHelper->formStart();
    echo $formHelper->formTag('errors');

    foreach ($formHelper as $formEntity) {
        if ($formEntity->getName()=='cc') {
    ?>
        <fieldset>
        	<?php echo $formHelper->formOption('radio', 'cc' ); ?>

To define a default form option call `FormCollection::setFormOptionDefaultValues(array $defaultValues)` or `FormCollection::addFormOptionDefaultValues($formEntityName)`.


    $forms->setFormOptionDefaultValues(array('cc'))

or

    $forms->addFormOptionDefaultValues('dd');


### Configuration

The forms can be configured by adding a parameter when creating a form manager or form instance.

    $formManager = new FormManager($options);

or

    $form = $formManager->createForm($entity, 'testform', $options);

The default options look like this:

    $options = [
        'csrf_parameter' => '_csrf_token',
        'csrf_protection' => true,
        'collection_type' => FormCollectionInterface::COLLECTION_TYPE_ALL,
        'locale' => 'en_EN',
        'fallback_locales' => array('en_EN'),
        'translator_paths' => array(),
        'config_paths' => array()
    ];

* `csrf_parameter` = specifies the name attribute of csrf input hidden field
* `csrf_protection` = enabled/disables csrf protection
* `collection_type` = see documentation for form collection
* `locale` = default language
* `fallback_locales` = fallback language if a translation is not find in the current language
* `translator_paths` = specifies where the translation files are located
* `config_paths` = specifies where the validation files are located



### Form Hooks

With hooks in the form entity you can influence the form processing. In the following example we use prevalidation hook to remove whitespaces from the already bind properties.
Also we add additional validation to a post validation hook.

    class Address
    {
        protected $streetName;
        protected $streetNumber;
        protected $postalCode;
        protected $town;
        protected $country;

       ...

        public function prevalidatorHook(FormHook $formHook)
        {
            $this->streetName = trim($this->streetName);
            $this->streetNumber = trim($this->streetNumber);
            $this->postalCode = trim($this->postalCode);
            $this->town = trim($this->town);
            $this->country = trim($this->country);
        }

        public function postvalidatorHook(FormHook $formHook)
        {
            if ($this->postalCode != '54321') {
                $formHook->setError('postal_code', 'has unexpected value');
            }
        }

        ...

The hook is basically a method with a defined naming conversion within the form entity. If present in a form entity it will be called while processing.

* postbindHook (called after a entity is bound)
* prevalidatorHook (called befor a entity is validated)
* postvalidatorHook (called after a entity was validated)

The hooks receives an instance of `FormHook` which gives you restricted access to entity container to change errors:

* getErrors()
* hasErrors()
* getError($key)
* hasError($key)
* setError($key, $message)


Roadmap
-------

* decouple translation, validator from configuration (smaller, extra options)
* test label mit alpha entity names / collections
* test encoding value
* rename form error to violation
* violation for form collection option (inkl. translation)
* violation for csrf validation (inkl. translation)
* state machine for bind and validation
* add smarty block plugin to restrict form output to first or last entity (for tables and buttons).
* add smarty block plugin to restrict to defined entity (form collections).