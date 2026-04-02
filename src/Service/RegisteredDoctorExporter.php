<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\RegisteredDoctor;
use App\Repository\RegisteredDoctorRepository;
use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class RegisteredDoctorExporter
{
    public function __construct(
        private readonly RegisteredDoctorRepository $repository,
    ) {
    }

    public function createStreamedResponse(?string $search = null): Response
    {
        $doctors = $this->repository->findAllMatchingSearch($search);

        $response = new StreamedResponse(function () use ($doctors): void {
            $spreadsheet = $this->buildSpreadsheet($doctors);
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        $suffix = (new DateTimeImmutable())->format('Y-m-d_His');
        $filename = null !== $search
            ? 'inscriptions-medecins-filtre-' . $suffix . '.xlsx'
            : 'inscriptions-medecins-' . $suffix . '.xlsx';
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    /**
     * @param list<RegisteredDoctor> $doctors
     */
    private function buildSpreadsheet(array $doctors): Spreadsheet
    {
        $sheet = new Spreadsheet();
        $active = $sheet->getActiveSheet();
        $active->setTitle('Inscriptions');

        $headers = [
            'ID',
            'Date création',
            'Type chambre',
            'Prénom',
            'Nom',
            'E-mail',
            'Téléphone',
            'Établissement',
            'Notes',
            'P1 prénom',
            'P1 nom',
            'P1 e-mail',
            'P2 prénom',
            'P2 nom',
            'P2 e-mail',
            'Tél. commun',
            'Établissement (double)',
            'Notes (double)',
        ];

        $colCount = \count($headers);

        for ($i = 0; $i < $colCount; ++$i) {
            $cell = Coordinate::stringFromColumnIndex($i + 1) . '1';
            $active->setCellValue($cell, $headers[$i]);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);
        $headerRange = 'A1:' . $lastColLetter . '1';
        $active->getStyle($headerRange)->getFont()->setBold(true);
        $active->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E8F4FC');
        $active->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $active->getStyle($headerRange)->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM)
            ->getColor()->setRGB('39C3F9');

        $row = 2;

        foreach ($doctors as $d) {
            $values = [
                $d->getId(),
                $d->getCreatedAt()?->format('Y-m-d H:i:s'),
                $d->getRoomType(),
                $d->getFirstName(),
                $d->getLastName(),
                $d->getEmail(),
                $d->getPhone(),
                $d->getInstitution(),
                $d->getNotes(),
                $d->getParticipant1FirstName(),
                $d->getParticipant1LastName(),
                $d->getParticipant1Email(),
                $d->getParticipant2FirstName(),
                $d->getParticipant2LastName(),
                $d->getParticipant2Email(),
                $d->getSharedPhone(),
                $d->getSharedInstitution(),
                $d->getSharedNotes(),
            ];

            for ($i = 0; $i < $colCount; ++$i) {
                $cell = Coordinate::stringFromColumnIndex($i + 1) . $row;
                $active->setCellValue($cell, $values[$i]);
            }
            ++$row;
        }

        $dataLastRow = max(1, $row - 1);

        if ($dataLastRow >= 2) {
            $active->getStyle('A2:' . $lastColLetter . $dataLastRow)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)
                ->getColor()->setRGB('D0D7DE');
        }

        for ($i = 1; $i <= $colCount; ++$i) {
            $active->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
        $active->freezePane('A2');

        return $sheet;
    }
}
