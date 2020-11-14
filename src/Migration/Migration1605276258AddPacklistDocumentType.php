<?php declare(strict_types=1);

namespace EventCandyPacklist\Migration;

use Doctrine\DBAL\Connection;
use ErrorException;
use EventCandy\Sets\Utils;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1605276258AddPacklistDocumentType extends MigrationStep
{
    public const PACKLIST = 'packlist';
    public function getCreationTimestamp(): int
    {
        return 1605276258;
    }

    public function update(Connection $connection): void
    {
        $this->createDocumentTypes($connection);
        $this->createDocumentConfiguration($connection);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement updateDestructive
    }

    private function createDocumentTypes(Connection $connection): void
    {
        $enId = $this->getLanguageId('en-GB', $connection);
        $deID = $this->getLanguageId('de-DE', $connection);

        $packlistId = Uuid::randomBytes();

        $connection->insert('document_type', ['id' => $packlistId, 'technical_name' => self::PACKLIST, 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);

        $connection->insert('document_type_translation', ['document_type_id' => $packlistId, 'language_id' => $deID, 'name' => 'Packliste', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('document_type_translation', ['document_type_id' => $packlistId, 'language_id' => $enId, 'name' => 'Packlist', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
    }

    private function getLanguageId(string $lang, Connection $connection): string
    {
        $sql = 'select `language`.`id` from `language` 
                left join `locale` on `language`.`locale_id` = `locale`.`id`
                where `locale`.`code` = :code';

        $result = $connection->fetchArray($sql, ['code' => $lang]);

        Utils::log(print_r(Uuid::fromBytesToHex($result[0]), true));
        return $result[0];
    }

    private function createDocumentConfiguration(Connection $connection): void
    {


        $packlistConfigId = Uuid::randomBytes();

        $packlistId = $connection->fetchColumn('SELECT id FROM `document_type` WHERE `technical_name` = :technical_name', ['technical_name' => self::PACKLIST]);

        $defaultConfig = [
            'displayPrices' => true,
            'displayFooter' => false,
            'displayHeader' => true,
            'displayLineItems' => true,
            'diplayLineItemPosition' => true,
            'displayPageCount' => true,
            'displayCompanyAddress' => true,
            'pageOrientation' => 'portrait',
            'pageSize' => 'a4',
            'itemsPerPage' => 100,
            'companyName' => 'Muster AG',
            'taxNumber' => '000111000',
            'vatId' => 'XX 111 222 333',
            'taxOffice' => 'Coesfeld',
            'bankName' => 'Kreissparkasse Münster',
            'bankIban' => 'DE11111222223333344444',
            'bankBic' => 'SWSKKEFF',
            'placeOfJurisdiction' => 'Coesfeld',
            'placeOfFulfillment' => 'Coesfeld',
            'executiveDirector' => 'Max Mustermann',
            'companyAddress' => 'Muster AG - Ebbinghoff 10 - 48624 Schöppingen',
        ];


        $configJson = json_encode($defaultConfig);

        $connection->insert('document_base_config', ['id' => $packlistConfigId, 'name' => self::PACKLIST, 'global' => 1, 'filename_prefix' => self::PACKLIST . '_', 'document_type_id' => $packlistId, 'config' => $configJson, 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);

        $connection->insert('document_base_config_sales_channel', ['id' => Uuid::randomBytes(), 'document_base_config_id' => $packlistConfigId, 'document_type_id' => $packlistId, 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);


        // number ranges
        $definitionNumberRangeTypes = [
            'document_packlist' => [
                'id' => Uuid::randomHex(),
                'global' => 0,
                'nameDe' => 'Packliste',
                'nameEn' => 'Packlist',
            ]
        ];

        $definitionNumberRanges = [
            'document_packlist' => [
                'id' => Uuid::randomHex(),
                'name' => 'Packlists',
                'nameDe' => 'Packlisten',
                'global' => 1,
                'typeId' => $definitionNumberRangeTypes['document_packlist']['id'],
                'pattern' => '{n}',
                'start' => 1000,
            ]
        ];

        $languageEn = $this->getLanguageId('en-GB', $connection);
        $languageDe = $this->getLanguageId('de-DE', $connection);

        foreach ($definitionNumberRangeTypes as $typeName => $numberRangeType) {
            $connection->insert(
                'number_range_type',
                [
                    'id' => Uuid::fromHexToBytes($numberRangeType['id']),
                    'global' => $numberRangeType['global'],
                    'technical_name' => $typeName,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]
            );
            $connection->insert(
                'number_range_type_translation',
                [
                    'number_range_type_id' => Uuid::fromHexToBytes($numberRangeType['id']),
                    'type_name' => $numberRangeType['nameEn'],
                    'language_id' => $languageEn,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]
            );
            $connection->insert(
                'number_range_type_translation',
                [
                    'number_range_type_id' => Uuid::fromHexToBytes($numberRangeType['id']),
                    'type_name' => $numberRangeType['nameDe'],
                    'language_id' => $languageDe,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]
            );
        }

        foreach ($definitionNumberRanges as $numberRange) {
            $connection->insert(
                'number_range',
                [
                    'id' => Uuid::fromHexToBytes($numberRange['id']),
                    'global' => $numberRange['global'],
                    'type_id' => Uuid::fromHexToBytes($numberRange['typeId']),
                    'pattern' => $numberRange['pattern'],
                    'start' => $numberRange['start'],
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]
            );
            $connection->insert(
                'number_range_translation',
                [
                    'number_range_id' => Uuid::fromHexToBytes($numberRange['id']),
                    'name' => $numberRange['name'],
                    'language_id' => $languageEn,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]
            );
            $connection->insert(
                'number_range_translation',
                [
                    'number_range_id' => Uuid::fromHexToBytes($numberRange['id']),
                    'name' => $numberRange['nameDe'],
                    'language_id' => $languageDe,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]
            );
        }
    }
}
