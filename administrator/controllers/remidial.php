<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

 defined('_JEXEC') or die();

 class RemidialsControllerRemidial extends FormController
 {
     protected function allowAdd($data = array())
     {
         return parent::allowAdd($data);
     }

     protected function allowEdit($data = array(), $key = 'id')
     {
         $id = isset($data[$key]) ? $data[$key] : 0;
         if (!empty($id)) {
             return Factory::getUser()->authorise('core.edit', 'com_remidials.remidial.'.$id);
         }
     }

     public function getModel($name = 'Remidial', $prefix = 'RemidialsModel', $config = array('ignore_request' => true))
     {
         return parent::getModel($name, $prefix, $config);
     }

     /**
      * Method to save a record.
      *
      * @param   string  $key     The name of the primary key of the URL variable.
      * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
      *
      * @return  boolean  True if successful, false otherwise.
      *
      * @since   1.6
      */
     public function save($key = null, $urlVar = null)
     {
         // Check for request forgeries.
         $this->checkToken();

         $app   = Factory::getApplication();
         $model = $this->getModel();
         $table = $model->getTable();
         $data  = $this->input->post->get('jform', array(), 'array');
         $checkin = property_exists($table, $table->getColumnAlias('checked_out'));
         $context = "$this->option.edit.$this->context";
         $task = $this->getTask();

         // Determine the name of the primary key for the data.
         if (empty($key)) {
             $key = $table->getKeyName();
         }

         // To avoid data collisions the urlVar may be different from the primary key.
         if (empty($urlVar)) {
             $urlVar = $key;
         }

         $recordId = $this->input->getInt($urlVar);

         // Populate the row id from the session.
         $data[$key] = $recordId;

         // Access check.
         if (!$this->allowSave($data, $key)) {
             $this->setError(Text::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
             $this->setMessage($this->getError(), 'error');

             $this->setRedirect(
                 Route::_(
                     'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(),
                     false
                 )
             );

             return false;
         }

         // Validate the posted data.
         // Sometimes the form needs some posted data, such as for plugins and modules.
         $form = $model->getForm($data, false);

         if (!$form) {
             $app->enqueueMessage($model->getError(), 'error');

             return false;
         }

         // Send an object which can be modified through the plugin event
         $objData = (object) $data;
         $app->triggerEvent(
             'onContentNormaliseRequestData',
             array($this->option . '.' . $this->context, $objData, $form)
         );
         $data = (array) $objData;

         // Test whether the data is valid.
         $validData = $model->validate($form, $data);

         
         // Check for validation errors.
         if ($validData === false) {
             // Get the validation messages.
             $errors = $model->getErrors();

             // Push up to three validation messages out to the user.
             for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
                 if ($errors[$i] instanceof \Exception) {
                     $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                 } else {
                     $app->enqueueMessage($errors[$i], 'warning');
                 }
             }

             /**
              * We need the filtered value of calendar fields because the UTC normalision is
              * done in the filter and on output. This would apply the Timezone offset on
              * reload. We set the calendar values we save to the processed date.
              */
             $filteredData = $form->filter($data);

             foreach ($form->getFieldset() as $field) {
                 if ($field->type === 'Calendar') {
                     $fieldName = $field->fieldname;

                     if (isset($filteredData[$fieldName])) {
                         $data[$fieldName] = $filteredData[$fieldName];
                     }
                 }
             }

             // Save the data in the session.
             $app->setUserState($context . '.data', $data);

             // Redirect back to the edit screen.
             $this->setRedirect(
                 Route::_(
                     'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar),
                     false
                 )
             );

             return false;
         }

         // Attempt to save the data.
         if (!$model->save($validData)) {
             // Save the data in the session.
             $app->setUserState($context . '.data', $validData);

             // Redirect back to the edit screen.
             $this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
             $this->setMessage($this->getError(), 'error');

             $this->setRedirect(
                 Route::_(
                     'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar),
                     false
                 )
             );

             return false;
         }

         // Save succeeded, so check-in the record.
         if ($checkin && $model->checkin($validData[$key]) === false) {
             // Save the data in the session.
             $app->setUserState($context . '.data', $validData);

             // Check-in failed, so go back to the record and display a notice.
             $this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
             $this->setMessage($this->getError(), 'error');

             $this->setRedirect(
                 Route::_(
                     'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar),
                     false
                 )
             );

             return false;
         }

         
         
         // Redirect the user and adjust session state based on the chosen task.
         switch ($task) {
            case 'apply':
                // Set the record data in the session.
                $recordId = $model->getState($this->context . '.id');
                $this->holdEditId($context, $recordId);
                $app->setUserState($context . '.data', null);
                $model->checkout($recordId);

                // Redirect back to the edit screen.
                $this->setRedirect(
                    Route::_(
                        'index.php?option=' . $this->option . '&view=' . $this->view_item
                        . $this->getRedirectToItemAppend($recordId, $urlVar),
                        false
                    )
                );
                break;

            
            default:
                // Clear the record id and data from the session.
                $this->releaseEditId($context, $recordId);
                $app->setUserState($context . '.data', null);

                $url = 'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend();

                // Check if there is a return value
                $return = $this->input->get('return', null, 'base64');

                if (!is_null($return) && Uri::isInternal(base64_decode($return))) {
                    $url = base64_decode($return);
                }

                // Redirect to the list screen.
                $this->setRedirect(Route::_($url, false));
                break;
        }

         // Invoke the postSave method to allow for the child class to access the model.
         $this->postSaveHook($model, $validData);

         return true;
     }
 }
