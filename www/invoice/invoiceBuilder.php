<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_POST['submit'])) {
    if (!isset($_FILES['priceFile']) || $_FILES['priceFile']['error'] !== UPLOAD_ERR_OK)
        exit("Ошибка при загрузке файла price.docx");

    $uploadedFilePath = $_FILES['priceFile']['tmp_name'];

    $lastName = $_POST['lastName'] ?? '';
    $city = $_POST['city'] ?? '';
    $address = $_POST['address'] ?? '';
    $deliveryDate = $_POST['deliveryDate'] ?? '';
    $items = $_POST['items'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $color = $_POST['color'] ?? '';

    $basePrices = readPricesFromDocx($uploadedFilePath);

    $colorMarkups = [
        'Орех' => 1.1,
        'Дуб мореный' => 1.2,
        'Палисандр' => 1.3,
        'Эбеновое дерево' => 1.4,
        'Клен' => 1.5,
        'Лиственница' => 1.6
    ];

    $markup = $colorMarkups[$color] ?? 1.0;

    $totalWithoutMarkup = 0;
    $orderDetails = [];

    foreach ($items as $itemName => $val) {
        if (!isset($basePrices[$itemName]))
            continue;

        $quantity = (int)($quantities[$itemName] ?? 1);
        $base = $basePrices[$itemName];

        $itemCost = $base * $quantity;
        $totalWithoutMarkup += $itemCost;

        $orderDetails[] = [
            'name' => $itemName,
            'price' => $base,
            'quantity' => $quantity,
            'sum' => $itemCost
        ];
    }

    $totalWithMarkup = $totalWithoutMarkup * $markup;
    $invoiceNumber = rand(1000, 9999);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Накладная');

    $offset = 10;
    writeInvoice(
        $sheet,
        $offset,
        $invoiceNumber,
        $city,
        $address,
        $deliveryDate,
        $orderDetails,
        $color,
        $markup,
        $totalWithoutMarkup,
        $totalWithMarkup
    );

    $fileName = 'Документ_на_выдачу_' . $invoiceNumber . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} else {
    echo "Нет данных для формирования заказа.";
}

function writeInvoice(
    $sheet,
    $offset,
    $invoiceNum,
    $city,
    $address,
    $deliveryDate,
    $orderDetails,
    $selectedColor,
    $colorMarkup,
    $totalWithoutMarkup,
    $totalWithMarkup
): void
{
    $currentRow = addBarcode($offset, $sheet);
    $currentRow = addInvoiceTitle($sheet, $currentRow, $invoiceNum);
    $currentRow = addAddress($sheet, $currentRow, $city, $address);
    $currentRow = addDeliveryDate($sheet, $currentRow, $deliveryDate);

    $currentRow += 1;
    $tableHeaderRow = $currentRow;

    $currentRow = addInvoiceTableHeader($sheet, $currentRow);
    $currentRow = addProductRows($orderDetails, $sheet, $currentRow);
    $currentRow = addSelectedColor($sheet, $currentRow, $selectedColor, $colorMarkup, $totalWithoutMarkup);

    $lastTableRow = $currentRow;
    $currentRow = addTotalRow($sheet, $currentRow, $totalWithMarkup);

    addBorders($sheet, $tableHeaderRow, $lastTableRow);

    setColumnsSize($sheet);

    $currentRow += 2;
    $currentRow = addTotalDescription($orderDetails, $sheet, $currentRow, $totalWithMarkup);
    $currentRow += 1;

    addGuaranteeText($currentRow, $sheet);
}

function addGuaranteeText($currentRow, $sheet): void
{
    $guaranteeText = file_get_contents(__DIR__ . '/assets/Гарантийное обслуживание.txt');
    $endGuaranteeTextRow = $currentRow + 20;
    $sheet->mergeCells("E$currentRow:J$endGuaranteeTextRow");

    $textParts = explode("EOF", $guaranteeText);
    $richText = new RichText();

    $boldPart = $richText->createTextRun($textParts[0]);

    // It does not work, but maybe I'll figure it out later
    $boldPart->getFont()->setBold(false);

    for ($i = 1; $i < count($textParts); $i++) {
        $richText->createTextRun($textParts[$i])->getFont()->setBold(false);
    }

    $sheet->setCellValue("E$currentRow", $richText);

    $sheet
        ->getStyle("E$currentRow")
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_LEFT)
        ->setVertical(Alignment::VERTICAL_TOP)
        ->setWrapText(true);
}

function addTotalDescription($orderDetails, $sheet, int $currentRow, $totalSum): int
{
    $itemsCount = count($orderDetails);
    $formattedNumber = number_format($totalSum, 2, ',', '');
    $sheet->setCellValue("E$currentRow", "Всего наименований: $itemsCount, на сумму: $formattedNumber руб.");
    return $currentRow + 1;
}

function setColumnsSize($sheet): void
{
    foreach (range('F', 'J') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }


}

function addBorders($sheet, int $tableHeaderRow, int $lastTableRow): void
{
    $borderStyle = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
            ],
        ],
    ];
    $sheet->getStyle("E$tableHeaderRow:J$lastTableRow")->applyFromArray($borderStyle);

    $noBorderStyle = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_NONE,
            ],
        ],
    ];

    $sheet->getStyle("E$lastTableRow:I$lastTableRow")->applyFromArray($noBorderStyle);
}

function addTotalRow($sheet, int $currentRow, $totalSum): int
{
    $sheet->mergeCells("E$currentRow:I$currentRow");
    $sheet->setCellValue("E$currentRow", "Итого:");
    $sheet
        ->getStyle("E$currentRow")
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER)
        ->setWrapText(true);

    $sheet->getStyle("E$currentRow")->getFont()->setBold(true);

    $sheet->setCellValue("J$currentRow", $totalSum);
    $sheet->getStyle("J$currentRow")->getFont()->setBold(true);

    $sheet
        ->getStyle("J$currentRow")
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER)
        ->setWrapText(true);

    return $currentRow + 1;
}

function addSelectedColor($sheet, $rowNumber, $selectedColor, $colorMarkup, $totalSum): int
{
    $sheet->setCellValue("E$rowNumber", "");
    $sheet->setCellValue("F$rowNumber", "Цвет: $selectedColor");

    $colorDrawing = new Drawing();

    $colorPath = [
        'Орех' => __DIR__ . '/assets/орех.png',
        'Дуб мореный' => __DIR__ . '/assets/лиственница.png',
        'Палисандр' => __DIR__ . '/assets/дуб.png',
        'Эбеновое дерево' => __DIR__ . '/assets/палисандр.png',
        'Клен' => __DIR__ . '/assets/клен.png',
        'Лиственница' => __DIR__ . '/assets/эбен.png'
    ];

    $colorDrawing->setPath($colorPath[$selectedColor]);
    $colorDrawing->setCoordinates("G$rowNumber");

    $colorDrawing->setOffsetX(10);
    $colorDrawing->setOffsetY(9);
    $colorDrawing->setHeight(45);
    $colorDrawing->setWorksheet($sheet);

    $sheet
        ->getStyle("F$rowNumber")
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_LEFT)
        ->setVertical(Alignment::VERTICAL_CENTER)
        ->setWrapText(true);

    $sheet
        ->getStyle("G$rowNumber")
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER)
        ->setWrapText(true);

    $sheet->getRowDimension($rowNumber)->setRowHeight(50);

    $sheet->mergeCells("H$rowNumber:I$rowNumber");
    $sheet->setCellValue("H$rowNumber", $colorMarkup);

    $sheet
        ->getStyle("H$rowNumber:J$rowNumber")
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);

    $sheet->setCellValue("J$rowNumber", $totalSum);

    return $rowNumber + 1;
}

function addProductRows($orderDetails, $sheet, $currentRow): int
{
    foreach ($orderDetails as $index => $product) {
        $sheet->setCellValue("E$currentRow", $index + 1);
        $sheet->setCellValue("F$currentRow", $product['name']);
        $sheet->setCellValue("G$currentRow", "");
        $sheet->setCellValue("H$currentRow", $product['price']);
        $sheet->setCellValue("I$currentRow", $product['quantity']);
        $sheet->setCellValue("J$currentRow", $product['sum']);

        $sheet->getStyle("E$currentRow")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getStyle("F$currentRow")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getStyle("G$currentRow:J$currentRow")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $currentRow++;
    }

    return $currentRow;
}

function addInvoiceTableHeader($sheet, int $rowNumber): int
{
    $sheet->setCellValue("E$rowNumber", "№");
    $sheet->setCellValue("F$rowNumber", "Наименование товара");
    $sheet->setCellValue("G$rowNumber", "Цвет");
    $sheet->setCellValue("H$rowNumber", "Цена");
    $sheet->setCellValue("I$rowNumber", "Кол-во");
    $sheet->setCellValue("J$rowNumber", "Сумма");

    $sheet->getStyle("E$rowNumber:J$rowNumber")->getFont()->setBold(true);

    $sheet->getStyle("E$rowNumber:J$rowNumber")
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER)
        ->setWrapText(true);

    return $rowNumber + 1;
}

function addDeliveryDate($sheet, int $rowNumber, $deliveryDate): int
{
    $date = date_create($deliveryDate);
    $formattedDate = date_format($date,"d.m.Y");

    $sheet->mergeCells("E$rowNumber:J$rowNumber");
    $sheet->setCellValue("E$rowNumber", "Дата получения заказа: $formattedDate");

    $sheet
        ->getStyle("E$rowNumber")
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);

    return $rowNumber + 1;
}

function addAddress($sheet, int $rowNumber, $city, $address): int
{
    $sheet->mergeCells("E$rowNumber:J$rowNumber");
    $sheet->setCellValue("E$rowNumber", "Адрес получения заказа: г. $city, $address");

    $sheet->getStyle("E$rowNumber")
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);

    return $rowNumber + 1;
}

function addInvoiceTitle($sheet, int $rowNumber, $invoiceNum): int
{
    $sheet->mergeCells("E$rowNumber:J$rowNumber");
    $sheet->setCellValue("E$rowNumber", "Накладная № $invoiceNum");

    $sheet->getStyle("E$rowNumber")->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);

    $sheet->getStyle("E$rowNumber")->getFont()->setBold(true)->setSize(16);

    return $rowNumber + 1;
}

function addBarcode($offset, $sheet): int
{
    $barcodeDrawing = new Drawing();
    $barcodeDrawing->setPath(__DIR__ . '/assets/штрих.JPG');
    $barcodeDrawing->setCoordinates("I" . ($offset - 2));
    $barcodeDrawing->setOffsetX(5);
    $barcodeDrawing->setOffsetY(5);
    $barcodeDrawing->setHeight(50);
    $barcodeDrawing->setWorksheet($sheet);
    return 1 + $offset;
}

function readPricesFromDocx($filePath): array
{
    $basePrices = [];
    $phpWord = IOFactory::load($filePath);
    $section = $phpWord->getSections()[0];

    $elements = $section->getElements();
    $tables = array_filter($elements, function ($element) {
        return $element instanceof Table;
    });
    $table = reset($tables);
    $rows = $table->getRows();
    $headerRowsToSkip = 2;

    for ($i = $headerRowsToSkip; $i < count($rows); $i++) {
        $row = $rows[$i];
        $cells = $row->getCells();

        if (count($cells) < 2) continue;

        $name = trim(getCellText($cells[0]));
        $price = floatval(str_replace(',', '.', getCellText($cells[1])));
        $basePrices[$name] = $price;
    }

    return $basePrices;
}

function getCellText($cell)
{
    return $cell->getElements()[0]->getText();
}
