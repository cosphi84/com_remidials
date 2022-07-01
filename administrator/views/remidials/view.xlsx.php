<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */
ini_set('display_error', 0);
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die();

jimport('phpexcel.library.PHPExcel');

class RemidialsViewRemidials extends HtmlView
{
    public function display($tpl = null)
    {
        $data = $this->get('Items');
        $errors = $this->get('Errors');

        if (count($errors) > 0) {
            throw new Exception(implode('<br />', $errors), 500);

            return false;
        }

        if (!class_exists('PHPExcel')) {
            throw new Exception('PHPExcel Not Found!', 500);
            return false;
        }

        $excel = new PHPExcel();
        $excel->getProperties()
            ->setCreator('SIAK - Sistem Informasi AKademik')
            ->setLastModifiedBy('SIAK Server')
            ->setTitle('Daftar Perbaikan Nilai / Remidial')
            ->setSubject('Daftar Perbaikan Nilai')
            ->setDescription('Daftar Mahasiswa yang mengjukan perbaikan nilai.')
            ->setKeywords('Remidial, SIAK')
            ->setCategory('SIAK Remdial');

        $excel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Status Remidial')
            ->setCellValue('C1', 'NPM')
            ->setCellValue('D1', 'Mahasiswa')
            ->setCellValue('E1', 'Prodi')
            ->setCellValue('F1', 'Konsentrasi')
            ->setCellValue('G1', 'Kelas')
            ->setCellValue('H1', 'Semester')
            ->setCellValue('I1', 'Kode MK')
            ->setCellValue('J1', 'Matakuliah')
            ->setCellValue('K1', 'Dosen')
            ->setCellValue('L1', 'Jenis Remidial')
            ->setCellValue('M1', 'Nilai Awal')
            ->setCellValue('N1', 'Nilai Remidi')
            ->setCellValue('O1', 'Tanggal Input Nilai')
            ->setCellValue('P1', 'Input Nilai Oleh')
            ->setCellValue('Q1', 'Tanggal Daftar');

    
        $a = 2;
        $b = 1;
        foreach ($data as $i=>$item) {
            $excel->setActiveSheetIndex(0)
                ->setCellValue('A'.$a, $b)
                ->setCellValue('B'.$a, $item->status.' - '. $item->text)
                ->setCellValue('C'.$a, $item->NPM)
                ->setCellValue('D'.$a, $item->mahasiswa)
                ->setCellValue('E'.$a, $item->prodi.' - '. $item->programstudi)
                ->setCellValue('F'.$a, $item->konsentrasi)
                ->setCellValue('G'.$a, $item->kelas)
                ->setCellValue('H'.$a, $item->semester)
                ->setCellValue('I'.$a, $item->kodemk)
                ->setCellValue('J'.$a, $item->mk)
                ->setCellValue('K'.$a, Factory::getUser($item->dosen_id)->name)
                ->setCellValue('L'.$a, strtoupper($item->catid))
                ->setCellValue('M'.$a, $item->nilai_awal)
                ->setCellValue('N'.$a, $item->nilai_remidial)
                ->setCellValue('O'.$a, $item->input_date)
                ->setCellValue('P'.$a, Factory::getUser($item->input_by)->name)
                ->setCellValue('Q'.$a, $item->created_date);
            $a++;
            $b++;
        }

        $excel->getActiveSheet()->setTitle('Remidial');
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Daftar_Remidial.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
 
        // If you're serving to IE over SSL, then the following may be needed
         header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
         header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
         header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
         header('Pragma: public'); // HTTP/1.0

         $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('php://output');

        return true;
    }
}
