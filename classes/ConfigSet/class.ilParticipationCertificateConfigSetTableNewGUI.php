<?php

use ILIAS\Data\Factory;
use ILIAS\Data\DateFormat\DateFormat;
use ILIAS\UI\Implementation\Component\Table\Data;
use ILIAS\UI\Component\Table as I;
use ILIAS\Data\Range;
use ILIAS\Data\Order;
use ILIAS\UI\URLBuilder;
use ILIAS\Data\URI;
use ILIAS\UI\URLBuilderToken;


class ilParticipationCertificateConfigSetTableNewGUI implements I\DataRetrieval
{
    protected Factory $df;
    protected DateFormat $current_user_date_format;

    protected ilParticipationCertificatePlugin $pl;

    protected URLBuilderToken $action_parameter_token;

    protected URLBuilderToken $row_id_token;

    protected URLBuilderToken $config_type;

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
        global $DIC;

        $actions = $this->getActions();

        return $this->ui_factory->table()->data(
            '',
            $this->getColumsForRepresentation(),
            $this
        )->withActions($actions)/*
        ->withRequest($DIC->http()->request())*/;
    }

    public function getRows(
        I\DataRowBuilder $row_builder,
        array $visible_column_ids,
        Range $range,
        Order $order,
        ?array $filter_data,
        ?array $additional_parameters
    ): \Generator {
        $data = $this->doSelect($order, $range);

        foreach ($data as $idx => $record) {
            if($record['config_id'] === 0) {
                yield $row_builder->buildDataRow($record['config_id'], $record)
                                  ->withDisabledAction('copy')
                                  ->withDisabledAction('delete')
                                  ->withDisabledAction('activate');

            } else if($record['order_by'] != 1) {
                if ($record['active_status']) {
                    yield $row_builder->buildDataRow($record['config_id'], $record)
                                      ->withDisabledAction('deactivate');
                } else {
                    yield $row_builder->buildDataRow($record['config_id'], $record)
                                      ->withDisabledAction('activate');
                }
            } else {
                // TODO must limit the actions for the second entry
                yield $row_builder->buildDataRow($record['config_id'], $record);
            }
        }
    }

    private function getSelectableColumns(): array
    {
        $cols = array();
        $cols['order_by'] = array( 'txt' => $this->pl->txt('order_by'), 'default' => false, 'width' => 'auto' );
        $cols['configset_type'] = array( 'txt' => $this->pl->txt('config_type'), 'default' => '', 'width' => 'auto' );
        $cols['title'] = array( 'txt' => $this->pl->txt('title'), 'default' => '', 'width' => 'auto' );
        $cols['parent_title'] = array( 'txt' => $this->pl->txt('parent_title'), 'default' => '', 'width' => 'auto' );
        $cols['active'] = array( 'txt' => $this->pl->txt('active'), 'default' => false, 'width' => 'auto' );

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
            'order_by' => $f->table()->column()->text($columns['order_by']['txt'])
                          ->withIsSortable(false),
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
        global $DIC;
        $ui = $DIC->ui()->factory();

        $global_configs = new ilParticipationCertificateConfigSets();
        $data = $global_configs->getAllConfigSets();

        $tableData = [];

        $selectableColumns = $this->getSelectableColumns();

        foreach ($data as $configSet) {
            $active = 'inactive';
            $activeStatus = false;
            $configSetType = '';
            $configId = $configSet['conf_id'];
            $configType = $configSet['configset_type'];
            /*foreach ($selectableColumns as $columnKey => $value) {*/
            foreach ($configSet as $key => $value) {
                //if ($this->isColumnSelected($k)) { // TODO

                switch ($key) {
                    case 'order_by':
                        $value = intval($configSet[$key]) * 10;

                        if($configSet[$key] > 0) {
                           /* $configSet['order_by'] = $ui->input()->field()->text(
                                ''
                            )->withValue($value);*/
                            $configSet['order_by'] = true;

                        } else {
                            $configSet['order_by'] = '';
                        }

                        break;
                    case 'configset_type':
                        if ($configSet[$key] > 0) {
                            switch ($configSet['configset_type']) {
                                case ilParticipationCertificateConfig::CONFIG_SET_TYPE_GROUP:
                                    if (!ilParticipationCertificateGlobalConfigSet::find(
                                        $configSet['object_gl_conf_template_id']
                                    )) {
                                        $configSetType = '';
                                    }
                                    $arr_type[] = $this->pl->txt('configset_type_' . $configSet['configset_type']);
                                    $arr_type[] = $this->pl->txt(
                                        'object_config_type_' . $configSet['object_config_type']
                                    );
                                    $template = new ilParticipationCertificateGlobalConfigSet(
                                        $configSet['object_gl_conf_template_id']
                                    );
                                    $arr_type[] = $this->pl->txt('origin_template') . ": " . $template->getTitle();
                                    $configSetType = implode("<br/>", $arr_type);
                                    break;
                                default:
                                    $configSetType = $this->pl->txt('configset_type_' . $configSet['configset_type']);
                                    break;
                            }
                        } else {
                            $configSetType = '';
                        }

                        break;

                    case 'active':
                        if ((int)$configSet[$key] === 1) {
                            $active = 'active';
                            $activeStatus = true;
                        }
                        break;
                    default:

                        break;
                }
            }

            $tmp = [
                'config_id' => $configId,
                'config_type' => $configType,
                'order_by' => $configSet['order_by'],
                'configset_type' => $configSetType,
                'title' => $configSet['title'],
                'parent_title' => $configSet['parent_title'],
                'active' => $active,
                'active_status' => $activeStatus
            ];

            $tableData[] = $tmp;
        }

        return $tableData;
    }

    private function getActions()
    {
        global $DIC;

        $f = $DIC['ui.factory'];


        $uri = $this->buildURI(ilParticipationCertificateConfigGUI::CMD_ACTION);
        $url_builder = new URLBuilder($uri);
        [$url_builder, $this->action_parameter_token, $this->row_id_token, $this->config_type] =
            $url_builder->acquireParameters(
                ['config'],
                'action',
                'id',
                'type'
            );

        $actions = [
            'edit' => $f->table()->action()->single(
                'Edit',
                $url_builder->withParameter($this->action_parameter_token, 'edit'),
                $this->row_id_token/*,
                $this->config_type*/
            ),
            'copy' => $f->table()->action()->single(
                'Copy',
                $url_builder->withParameter($this->action_parameter_token, 'copy'),
                $this->row_id_token/*,
                $this->config_type*/
            ),
            'delete' =>
                $f->table()->action()->standard(
                    'Delete',
                    $url_builder->withParameter($this->action_parameter_token, 'delete'),
                    $this->row_id_token/*,
                    $this->config_type*/
                ),
            'activate' =>
                $f->table()->action()->standard(
                    'Activate',
                    $url_builder->withParameter($this->action_parameter_token, 'activate'),
                    $this->row_id_token/*,
                    $this->config_type*/
                )
        ];

        return $actions;
    }

    private function buildURI(
        string $command
    ): URI {
        global $DIC;

        return new URI(
            ILIAS_HTTP_PATH . '/' . $DIC->ctrl()->getLinkTargetByClass(
                \ilParticipationCertificateConfigGUI::class,
                $command
            )
        );
    }
}