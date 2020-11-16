<?php declare(strict_types=1);

namespace EventCandyPacklist;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class EventCandyPacklist extends Plugin
{

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $sql = 'select * from `ec_packlist_data` order by `created_at` desc';
        /** @var Connection $connection */
        $connection = $this->container->get(Connection::class);
        $result = $connection->fetchAll($sql);
        foreach ($result as $row) {
            $connection->delete($row['pl_table'], [$row['pl_column'] => $row['pl_id']]);
        }
        $connection->exec('DROP TABLE IF EXISTS ec_packlist_data');
    }
}