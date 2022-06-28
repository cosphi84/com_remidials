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
  class RemidialsControllerRegistration extends FormController
  {
      public function cancel($key = null)
      {
          parent::cancel($key);
          $this->setRedirect(
              Route::_('index.php?option=com_remidials&view=nilai'),
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
          $grpMahasiswa = ComponentHelper::getParams('com_siak')->get('grpMahasiswa');
          $grpsUser = $user->get('groups');
          $params = ComponentHelper::getParams('com_remidials');
          $tanggal = Date::getInstance();
                      
          $currentUrl = (string) Uri::getInstance();

          // pastikan hanya mahasiswa disini
          if (!in_array($grpMahasiswa, $grpsUser)) {
              $this->setRedirect(
                  Route::_('index.php'),
                  Text::_('JGLOBAL_AUTH_ACCESS_DENIED')
              );
          }

          $data = $input->get('jform', array(), 'array');
          $context = "$this->option.edit.registrasi";
          
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


          $dataNilai = $model->loadNilai($dataOK['nilai_id']);
          $dataOK['catid'] = $params->get('jenis_remidial');
          $dataOK['tahun_ajaran'] = $dataNilai->tahun_ajaran;
          $dataOK['dosen_id'] = $dataNilai->dosen;
          
          switch ($dataOK['catid']) {
            case 'sp':
                $dataOK['nilai_awal'] = $dataNilai->nilai_akhir;
                break;
            default:
                $dataOK['nilai_awal'] = $dataNilai->{$dataOK['catid']};
                break;
          }

          $dataOK['created_by'] = $user->id;
          $dataOK['created_date'] = $tanggal->toSql(true);

          if (!$model->save($dataOK)) {
              $app->setUserState($context.'.data', $dataOK);
              $this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
              $this->setMessage($model->getError(), 'error');
              $this->setRedirect($currentUrl);
              return false;
          }

          $app->setUserState($context.'.data', null);


          // send email
          $isiEmail = '<p>Kepada Sdr. '. $user->name .'('. $user->username.'),<br /><br />Kami telah menerima pendaftaran perbaikan nilai '. strtoupper($dataOK['catid']).' anda melalui SIAK dengan keterangan sbb:<br>';
          $isiEmail .= '<ul><li>Kode MK : '. $dataNilai->kodemk. '</li>';
          $isiEmail .= '<li>Mata kuliah : '. $dataNilai->mk. '</li>';
          $isiEmail .= '<li>Nilai Awal : '. $dataOK['nilai_awal']. '</li>';
          $isiEmail .= '<li>Tahun Ajaran : '. $dataOK['tahun_ajaran']. '</li>';
          $isiEmail .= '<li>Dosen : '. Factory::getUser($dataNilai->dosen)->name. '</li></ul></p>';
          $isiEmail .= '<p>Terimakasih</p>';
          $isiEmail .= '<br><br>Email ini dikirim otomatis oleh Sistem SIAK dan tidak perlu di balas.';
          
          $mailer = Factory::getMailer();
          $mailer->addRecipient($user->email);
          $mailer->setSubject('Konfirmasi Pendaftaran Remidial '. $dataNilai->kodemk);
          $mailer->setBody($isiEmail);

          try {
              $mailer->send();
          } catch (Exception $e) {
              Log::add('Caugh Exception: '. $e->getMessage(), Log::ERROR, 'jerror');
          }

          $this->setRedirect(
              $currentUrl,
              Text::_('COM_REMIDIAL_ADD_SUCCESSFUL')
          );

          return true;
      }
  }
