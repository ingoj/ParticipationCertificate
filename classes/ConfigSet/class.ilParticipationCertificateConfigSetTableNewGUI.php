<?php

use ILIAS\UI\Component\Table\DataRetrieval;
use ILIAS\Data\Factory;
use ILIAS\Data\DateFormat\DateFormat;
use ILIAS\UI\Implementation\Component\Table as T;
use ILIAS\UI\Implementation\Component\Table\Data;
use ILIAS\UI\Component\Table as I;
use ILIAS\Data\Range;
use ILIAS\Data\Order;

class ilParticipationCertificateConfigSetTableNewGUI implements I\DataRetrieval
{
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
        return $this->ui_factory->table()->data(
            '',
            $this->getColumsForRepresentation(),
            $this
        );
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
        $icons = [
            $this->ui_factory->symbol()->icon()->custom('templates/default/images/standard/icon_checked.svg', '', 'small'),
            $this->ui_factory->symbol()->icon()->custom('templates/default/images/standard/icon_unchecked.svg', '', 'small')
        ];
        foreach ($this->doSelect($order, $range) as $idx => $record) {
            yield $row_builder->buildDataRow($idx, $record);
        }
    }

    private function getSelectableColumns(): array
    {
        $cols = array();
        $cols['order_by'] = array( 'txt' => $this->pl->txt('order_by'), 'default' => false, 'width' => 'auto' );
        $cols['configset_type'] = array( 'txt' => $this->pl->txt('config_type'), 'default' => true, 'width' => 'auto' );
        $cols['title'] = array( 'txt' => $this->pl->txt('title'), 'default' => true, 'width' => 'auto' );
        $cols['parent_title'] = array( 'txt' => $this->pl->txt('parent_title'), 'default' => true, 'width' => 'auto' );
        $cols['active'] = array( 'txt' => $this->pl->txt('active'), 'default' => true, 'width' => 'auto' );

        return $cols;
    }

    public function getTotalRowCount(
        ?array $filter_data,
        ?array $additional_parameters
    ): ?int {
        return count($this->records());
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
        $columns = $this->getSelectableColumns();

        $f = $this->ui_factory;
        return  [
            'configset_type' => $f->table()->column()->text($columns['configset_type']['txt'])
                          ->withIsSortable(false),
            'title' => $f->table()->column()->text($columns['title']['txt'])
                         ->withHighlight(true),
            'parent_title' => $f->table()->column()->text($columns['parent_title']['txt']),
            'active' => $f->table()->column()->text($columns['active']['txt'], $this->current_user_date_format),
        ];
    }

    protected function records()
    {

        $global_configs = new ilParticipationCertificateConfigSets();
        $data = $global_configs->getAllConfigSets();

        $tableData = [];


        foreach ($this->getSelectableColumns() as $k => $v) {


        }
        foreach($data as $key => $configSet) {
            $configSetType = '';
            if ((int) $configSet['configset_type'] > 0) {

                switch ((int) $configSet['configset_type']) {
                    case ilParticipationCertificateConfig::CONFIG_SET_TYPE_GROUP:
                        if(!ilParticipationCertificateGlobalConfigSet::find($configSet['object_gl_conf_template_id'])) {
                            $configSetType = '';
                        }
                        $arr_type[] =  $this->pl->txt('configset_type_' . $configSet['configset_type']);
                        $arr_type[] = $this->pl->txt('object_config_type_' . $configSet['object_config_type']);
                        $template = new ilParticipationCertificateGlobalConfigSet($configSet['object_gl_conf_template_id']);
                        $arr_type[] = $this->pl->txt('origin_template') . ": " . $template->getTitle();
                        $configSetType = implode("<br/>", $arr_type);
                        break;
                    default:
                        $configSetType = $this->pl->txt('configset_type_' . $configSet['configset_type']);
                }
            } else {
                $configSetType = '';
            }

            $tmp = [
                'configset_type' => $configSetType,
                'title' => $configSet['title'],
                'parent_title' =>$configSet['parent_title'],
                'active' => $configSet['active']
            ];

            $tableData[] = $tmp;
        }

        return $tableData;
    }
}