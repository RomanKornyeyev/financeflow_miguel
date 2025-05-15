<?php
namespace App\Service;

use App\Entity\Movimiento;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;



class ExcelExportService
{
  /**
   * @param Movimiento[] $movimientos
   */
  public function exportMovimientos(array $movimientos, array $filtros = []): StreamedResponse
  {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Cabecera (filtros)
    $sheet->setCellValue('A1', $this->formatearFiltros($filtros));
    $sheet->mergeCells('A1:E1');
    $sheet->getStyle('A1')->applyFromArray([
      'font' => [
        'bold' => true,
        'size' => 11,
        'color' => ['rgb' => '000000'], // negro
      ],
      'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '8DB4E2'], // azul claro
      ],
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_LEFT,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
      ],
    ]);
    $sheet->getRowDimension(1)->setRowHeight(30); // opcional para más aire


    // Cabecera (titulos)
    $sheet->fromArray(['Fecha', 'Importe', 'Categoría', 'Tipo', 'Descripción'], null, 'A2');
    $sheet->getStyle('A2:E2')->getFont()->setBold(true);
    $sheet->getStyle('A2:E2')->applyFromArray([
      'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
      ],
      'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '16365C'], // azul oscuro
      ],
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
      ],
    ]);
    $sheet->getRowDimension(2)->setRowHeight(20);

    // estilos generales
    foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
      $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // data
    $row = 3;
    foreach ($movimientos as $movimiento) {
      $sheet->setCellValue('A' . $row, $movimiento->getFechaMovimiento()->format('Y-m-d'));
      $sheet->setCellValue('B' . $row, $movimiento->getImporte());
      $sheet->setCellValue('C' . $row, $movimiento->getCategoria()->value); // enum
      $sheet->setCellValue('D' . $row, $movimiento->getTipoTransaccion()->value); // enum
      $sheet->setCellValue('E' . $row, $movimiento->getDescripcion());
      $row++;
    }

    $lastRow = $row - 1;

    // estilos de rejilla
    $sheet->setShowGridlines(false);
    $sheet->getStyle("A1:E{$lastRow}")->applyFromArray([
      'borders' => [
        'allBorders' => [
          'borderStyle' => Border::BORDER_THIN,
          'color' => ['rgb' => 'BFBFBF'],
        ],
      ],
    ]);

    // Alineación a la derecha del importe (columna B)
    $sheet->getStyle("B2:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    $writer = new Xlsx($spreadsheet);

    return new StreamedResponse(function () use ($writer) {
      $writer->save('php://output');
    }, 200, [
      'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'Content-Disposition' => 'attachment;filename="movimientos.xlsx"',
      'Cache-Control' => 'max-age=0',
    ]);
  }

  private function formatearFiltros(array $filtros): string
  {
    $partes = [];

    if (!empty($filtros['desde'])) {
      $partes[] = 'desde el ' . $filtros['desde']->format('d/m/Y');
    }

    if (!empty($filtros['hasta'])) {
      $partes[] = 'hasta el ' . $filtros['hasta']->format('d/m/Y');
    }

    if (!empty($filtros['tipoTransaccion'])) {
      $partes[] = 'tipo: ' . ucfirst($filtros['tipoTransaccion']);
    }

    if (!empty($filtros['categoria'])) {
      $partes[] = 'categoría: ' . ucfirst(str_replace('_', ' ', $filtros['categoria']));
    }

    if (!empty($filtros['concepto'])) {
      $partes[] = 'concepto que contiene: "' . $filtros['concepto'] . '"';
    }

    if (!empty($filtros['descripcion'])) {
      $partes[] = 'descripción que contiene: "' . $filtros['descripcion'] . '"';
    }

    if (!empty($filtros['importeMin'])) {
      $partes[] = 'importe desde ' . $filtros['importeMin'] . ' €';
    }

    if (!empty($filtros['importeMax'])) {
      $partes[] = 'hasta ' . $filtros['importeMax'] . ' €';
    }

    return count($partes) > 0
      ? ucfirst(implode(', ', $partes))
      : 'Posición global';
  }

}
