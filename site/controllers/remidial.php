<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

 defined('_JEXEC') or die();

 /**
  * Registration Controller
  * Handle HTTP Post from front-end
  */
  class RemidialsControllerRemidial extends FormController
  {
      public function cancel($key = null)
      {
          parent::cancel($key);
          $this->setRedirect(
              Route::_('index.php?option=com_remidials&view=remidials'),
              Text::_('COM_REMIDIAL_OPERATION_CANCELED')
          );
      }

      /**
       * Save
       * Save post data
       */
      public function save($key = null, $urlVar = null)
      {
          Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

          $app = Factory::getApplication();
          $input = $app->input;
          $model = $this->getModel();
          $user = Factory::getUser();
          $grpDosen = ComponentHelper::getParams('com_siak')->get('grpDosen');
          $grpsUser = $user->get('groups');
          $id = $app->input->get('id', 0, 'int');
          
                      
          $currentUrl = (string) Uri::getInstance();

          // pastikan hanya mahasiswa disini
          if (!in_array($grpDosen, $grpsUser)) {
              $this->setRedirect(
                  Route::_('index.php?option=com_remidials'),
                  Text::_('JGLOBAL_AUTH_ACCESS_DENIED')
              );
          }

          $data = $input->get('jform', array(), 'array');
          $context = "{$this->option}.remidi.remidial";
          
          $form = $model->getForm($data, false);
          if (!$form) {
              $app->enqueueMessage($model->getErrors(), 'error');
              return false;
          }

          $dataOK = $model->validate($form, $data);
          if ($dataOK === false) {
              $errors = $model->getErrors();
              for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
                  if ($errors[$i] instanceof Exception) {
                      $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                  } else {
                      $app->enqueueMessage($errors[$i], 'warning');
                  }
              }
              $app->setUserState($context.'.data', $data);
              $this->setRedirect($currentUrl);
              return false;
          }

          $dataOK['id'] = $id;
        /*
          if ($model->updateNilaiMaster($dataOK)) {
              $dataOK['update_master_nilai'] = 1;
          } else {
              $dataOK['update_master_nilai'] = 0;
          }
          */
          
          if (!$model->save($dataOK)) {
              $app->setUserState($context.'.data', $dataOK);
              $this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
              $this->setMessage($model->getError(), 'error');
              $this->setRedirect($currentUrl);
              return false;
          }
          

          $app->setUserState($context.'.data', null);
          $model->setState('remidial.id', $id);
          
          
          $dataNIlai = $model->getItem();
          
          // send email
          $isiEmail = 'Yth. '. $user->name.', <br />'
                    . 'Terima kasih sudah mengisi nilai perbaikan :<br />'
                    . '<ul>'
                    
                    . '<li>Kode MK       : '. $dataNIlai->kodemk. '</li>'
                    . '<li>Matakuliah    : '. $dataNIlai->mk.'</li>'
                    . '<li>NPM Mahasiswa : '. $dataNIlai->NPM. '</li>'
                    . '<li>Mahasiswa     : '. $dataNIlai->mahasiswa. '</li>'
                    . '<li>Nilai Awal    : '. $dataNIlai->nilai_awal. '</li>'
                    . '<li>Nilai Akhir   : '. $dataNIlai->nilai_remidial . '</li>'
                    . '<li>Status        : '. $dataNIlai->status .' - '. $dataNIlai->text .'</li>'
                    . '</ul>'
                    . '------------------------------------------------------------------ <br />';
        
          
          $mailer = Factory::getMailer();
          $mailer->addRecipient($user->email, $user->name);
          $mailer->setFrom('siakadmin@ft-untagcirebon.ac.id', 'No-Reply Admin SIAK');
          $mailer->setSubject('Bukti Input Nilai Perbaikan / SP');
          $mailer->isHtml(true);
          $mailer->Encoding = 'base64';
          $mailer->setBody($isiEmail);

          try {
              $mailer->send();
          } catch (Exception $e) {
              Log::add('Caugh Exception: '. $e->getMessage(), Log::ERROR, 'jerror');
          }

          $this->setRedirect(
              Route::_('index.php?option=com_remidials&view=remidials'),
              Text::_('COM_REMIDIAL_INPUT_NILAI_SUCCESSFUL')
          );

          return true;
      }
  }
