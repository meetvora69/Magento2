<?php namespace Ktpl\Extendordergrid\Plugins;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as SalesOrderGridCollection;
class AddColumnsSalesOrderGridCollection
{
    private $messageManager;
    private $collection;

    public function __construct(MessageManager $messageManager,
        SalesOrderGridCollection $collection
    ) {

        $this->messageManager = $messageManager;
        $this->collection = $collection;
    }

    public function aroundGetReport(
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject,
        \Closure $proceed,
        $requestName
    ) {
        $result = $proceed($requestName);
        if ($requestName == 'sales_order_grid_data_source') {
            if ($result instanceof $this->collection
            ) {
                $select = $this->collection->getSelect();
                $select->join(
                    ["soi" => "sales_order_item"],
                    'main_table.entity_id = soi.order_id AND soi.product_type="simple"',
                    array('weight', 'product_type')
                )
                    ->distinct();

                $select->join(
                    ["soa" => "sales_order_address"],
                    'main_table.entity_id = soa.parent_id AND soa.address_type="shipping"',
                    array('email', 'country_id', 'postcode', 'city', 'telephone')
                )
                    ->distinct();
            }

        }
        return $this->collection;
    }
}