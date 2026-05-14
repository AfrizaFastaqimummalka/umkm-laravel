<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Alignment, Border};

class ExcelService
{
    private string $colorHeader = '0F6E56';
    private string $colorRed    = '8B1A1A';
    private string $colorLight  = 'E8F8F2';
    private string $colorAlt    = 'F5FDFB';
    private string $colorWhite  = 'FFFFFF';

    public function generate(array $data): string
    {
        $wb = new Spreadsheet();
        $wb->removeSheetByIndex(0);
        $this->sheetRingkasan($wb, $data);
        $this->sheetDetail($wb, $data, 'pemasukan');
        $this->sheetDetail($wb, $data, 'pengeluaran');

        $path = sys_get_temp_dir() . '/laporan_' . uniqid() . '.xlsx';
        (new Xlsx($wb))->save($path);
        return $path;
    }

    private function sheetRingkasan(Spreadsheet $wb, array $data): void
    {
        $ws = $wb->createSheet(0);
        $ws->setTitle('Ringkasan');
        $ws->setShowGridlines(false);
        foreach (['A'=>4,'B'=>30,'C'=>26,'D'=>4] as $c=>$w) $ws->getColumnDimension($c)->setWidth($w);

        $ws->mergeCells('B2:C2');
        $ws->setCellValue('B2','LAPORAN KEUANGAN UMKM TEMPE');
        $ws->getStyle('B2')->applyFromArray(['font'=>['bold'=>true,'size'=>14,'color'=>['rgb'=>'063B2C']],'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER]]);

        $ws->mergeCells('B3:C3');
        $ws->setCellValue('B3', $data['label'] ?? '');
        $ws->getStyle('B3')->applyFromArray(['font'=>['size'=>11,'color'=>['rgb'=>'555555']],'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER]]);

        $rows = [
            5 => ['Total Pemasukan',   $data['total_pemasukan'],   '0F6E56'],
            6 => ['Total Pengeluaran', $data['total_pengeluaran'],  'DC2626'],
            7 => ['Saldo Bersih',      $data['saldo'],              $this->colorHeader],
        ];
        foreach ($rows as $row => [$lbl, $val, $clr]) {
            $ws->setCellValue("B{$row}", $lbl);
            $ws->setCellValue("C{$row}", 'Rp ' . number_format($val, 0, ',', '.'));
            foreach (['B','C'] as $col) {
                $ws->getStyle("{$col}{$row}")->applyFromArray([
                    'font'      => ['bold'=>true,'color'=>['rgb'=> $col==='C'?$clr:'374151']],
                    'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$this->colorLight]],
                    'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'CCCCCC']]],
                    'alignment' => ['horizontal'=> $col==='C'?Alignment::HORIZONTAL_RIGHT:Alignment::HORIZONTAL_LEFT,'vertical'=>Alignment::VERTICAL_CENTER],
                ]);
            }
            $ws->getRowDimension($row)->setRowHeight(28);
        }
    }

    private function sheetDetail(Spreadsheet $wb, array $data, string $tipe): void
    {
        $isPemasukan = $tipe === 'pemasukan';
        $bgHeader    = $isPemasukan ? $this->colorHeader : $this->colorRed;
        $bgAlt       = $isPemasukan ? $this->colorAlt    : 'FFF5F5';
        $items       = $data[$tipe] ?? [];
        $total       = $isPemasukan ? $data['total_pemasukan'] : $data['total_pengeluaran'];

        $ws = $wb->createSheet();
        $ws->setTitle($isPemasukan ? 'Pemasukan' : 'Pengeluaran');
        $ws->setShowGridlines(false);

        foreach (['A'=>6,'B'=>16,'C'=>22,'D'=>40,'E'=>22] as $c=>$w) $ws->getColumnDimension($c)->setWidth($w);

        $headers = ['No','Tanggal','Kategori','Keterangan','Jumlah (Rp)'];
        foreach ($headers as $i=>$h) {
            $col = chr(65+$i);
            $ws->setCellValue("{$col}1",$h);
            $ws->getStyle("{$col}1")->applyFromArray([
                'font'      => ['bold'=>true,'color'=>['rgb'=>$this->colorWhite]],
                'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$bgHeader]],
                'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'CCCCCC']]],
                'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            ]);
        }
        $ws->getRowDimension(1)->setRowHeight(24);

        foreach ($items as $i=>$item) {
            $row = $i+2;
            $bg  = $i%2!==0 ? $bgAlt : null;
            $vals = ['A'=>[$i+1,Alignment::HORIZONTAL_CENTER],'B'=>[$item['tanggal'],Alignment::HORIZONTAL_CENTER],'C'=>[str_replace('_',' ',ucfirst($item['kategori']??'')),Alignment::HORIZONTAL_LEFT],'D'=>[$item['keterangan']??'-',Alignment::HORIZONTAL_LEFT],'E'=>[(int)$item['jumlah'],Alignment::HORIZONTAL_RIGHT]];
            foreach ($vals as $col=>[$val,$align]) {
                $ws->setCellValue("{$col}{$row}",$val);
                $style = ['borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'CCCCCC']]],'alignment'=>['horizontal'=>$align,'vertical'=>Alignment::VERTICAL_CENTER]];
                if ($bg) $style['fill'] = ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$bg]];
                $ws->getStyle("{$col}{$row}")->applyFromArray($style);
                if ($col==='E') $ws->getStyle("{$col}{$row}")->getNumberFormat()->setFormatCode('#,##0');
            }
            $ws->getRowDimension($row)->setRowHeight(20);
        }

        $tr = count($items)+2;
        $ws->mergeCells("A{$tr}:D{$tr}");
        $ws->setCellValue("A{$tr}",'TOTAL');
        $ws->setCellValue("E{$tr}",(int)$total);
        $ws->getStyle("E{$tr}")->getNumberFormat()->setFormatCode('#,##0');
        foreach (['A','E'] as $col) {
            $ws->getStyle("{$col}{$tr}")->applyFromArray(['font'=>['bold'=>true,'color'=>['rgb'=>$this->colorWhite]],'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$bgHeader]],'borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'CCCCCC']]],'alignment'=>['horizontal'=>Alignment::HORIZONTAL_RIGHT,'vertical'=>Alignment::VERTICAL_CENTER]]);
        }
        $ws->getRowDimension($tr)->setRowHeight(24);
    }
}
