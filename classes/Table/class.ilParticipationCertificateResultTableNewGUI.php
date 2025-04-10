<?php

/**
 * Class ilParticipationCertificateResultGUI
 */
class ilParticipationCertificateResultTableNewGUI extends ilTable2GUI {

    protected Factory $df;
    protected DateFormat $current_user_date_format;

    protected ilParticipationCertificatePlugin $pl;

    public function __construct()
    {
        global $DIC;
        $this->ui_factory = $DIC['ui.factory'];
        $this->df = new Factory();
        $this->current_user_date_format = $this->df->dateFormat()->withTime24(
            $DIC['ilUser']->getDateFormat()
        );
        $this->pl = ilParticipationCertificatePlugin::getInstance();
    }

    //the repo is capable of building its table-view (similar to forms from a repo)
    public function getTableForRepresentation(): Data
    {

    }

    //implementation of DataRetrieval - accept params and yield rows
    public function getRows(
        I\DataRowBuilder $row_builder,
        array $visible_column_ids,
        Range $range,
        Order $order,
        ?array $filter_data,
        ?array $additional_parameters
    ): \Generator {

    }

    private function getSelectableColumns(): array
    {

    }

    public function getTotalRowCount(
        ?array $filter_data,
        ?array $additional_parameters
    ): ?int {

    }

    //do the actual reading - note, that e.g. order and range are easily converted to SQL
    protected function doSelect(Order $order, Range $range): array
    {
        $sql_order_part = $order->join('ORDER BY', fn(...$o) => implode(' ', $o));
        $sql_range_part = sprintf('LIMIT %2$s OFFSET %1$s', ...$range->unpack());
        return array_map(
            fn($rec) => array_merge($rec, ['sql_order' => $sql_order_part, 'sql_range' => $sql_range_part]),
            $this->records()
        );
    }

    /**
     * @return array
     */
    protected function getColumsForRepresentation(): array
    {

    }

    protected function records()
    {


    }

    private function getActions()
    {

    }
}
