<?php

use ILIAS\UI\Component\Table\DataRetrieval;
use ILIAS\Data\Factory;
use ILIAS\Data\DateFormat\DateFormat;
use ILIAS\UI\Implementation\Component\Table as T;
use ILIAS\UI\Implementation\Component\Table\Data;
use ILIAS\UI\Component\Table as I;
use ILIAS\Data\Range;
use ILIAS\Data\Order;
use ILIAS\UI\URLBuilder;

/**
 * Class ilParticipationCertificateResultTableNewGUI
 */
class ilParticipationCertificateResultTableNewGUI implements I\DataRetrieval
{
    protected Factory $df;
    protected DateFormat $current_user_date_format;

    protected ilParticipationCertificatePlugin $pl;

    protected ?string $ementoring = null;

    public function __construct()
    {
        global $DIC;
        $this->ui_factory = $DIC['ui.factory'];
        $this->df = new Factory();
        $this->current_user_date_format = $this->df->dateFormat()->withTime24(
            $DIC['ilUser']->getDateFormat()
        );
        $this->pl = ilParticipationCertificatePlugin::getInstance();

        $ementoring = ilParticipationCertificateConfig::getConfig('enable_ementoring', $_GET['ref_id']);
        if ($ementoring === null) {
            $ementoring = true;
        } else {
            $ementoring = boolval($ementoring);
        }
        $this->ementoring = $ementoring;
    }

    //the repo is capable of building its table-view (similar to forms from a repo)
    public function getTableForRepresentation(): Data
    {
        $actions = $this->getActions();

        return $this->ui_factory->table()->data(
            '',
            $this->getColumsForRepresentation(),
            $this
        )->withActions($actions);

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
        /*$cols = array();
        $cols['username'] = array( 'txt' => $this->pl->txt('order_by'), 'default' => false, 'width' => 'auto' );
        $cols['firstname'] = array( 'txt' => $this->pl->txt('config_type'), 'default' => true, 'width' => 'auto' );
        $cols['lastname'] = array( 'txt' => $this->pl->txt('title'), 'default' => true, 'width' => 'auto' );
        $cols['initial-test-completed'] = array( 'txt' => $this->pl->txt('parent_title'), 'default' => true, 'width' => 'auto' );

        return $cols;*/
        $cols = [];
        //$cols['usr_id'] = array( 'txt' => 'usr_id', 'default' => false, 'width' => 'auto', 'sort_field' => 'usr_id' );
        //access-dependent defaults via $write_access
        $cert_access = new ilParticipationCertificateAccess($_GET["ref_id"]);
        $write_access = $cert_access->hasCurrentUserWriteAccess();
        $cols['loginname'] = array(
            'txt' => $this->pl->txt('loginname'),
            'default' => $write_access,
            'width' => 'auto',
            'sort_field' => 'loginname'
        );
        $cols['firstname'] = array(
            'txt' => $this->pl->txt('cols_firstname'),
            'default' => true,
            'width' => 'auto',
            'sort_field' => 'firstname'
        );
        $cols['lastname'] = array(
            'txt' => $this->pl->txt('cols_lastname'),
            'default' => true,
            'width' => 'auto',
            'sort_field' => 'lastname'
        );
        $cols['initial_test_finished'] = array(
            'txt' => $this->pl->txt('cols_initial_test_finished'),
            'default' => true,
            'width' => 'auto',
            'sort_field' => 'initial_test_finished'
        );
        $cols['result_qualifing_tests'] = array(
            'txt' => $this->pl->txt('cols_result_qualifying'),
            'default' => true,
            'width' => 'auto',
            'sort_field' => 'result_qualifing_tests'
        );
        $cols['results_qualifing_tests'] = array(
            'txt' => $this->pl->txt('cols_results_qualifying'),
            'default' => false,
            'width' => 'auto',
            'sort_field' => 'result_qualifing_tests'
        );
        $cols['eMentoring_finished'] = array(
            'txt' => $this->pl->txt('cols_eMentoring_finished'),
            'default' => $this->ementoring,
            'width' => 'auto',
            'sort_field' => 'eMentoring_finished'
        );
        $cols['eMentoring_homework'] = array(
            'txt' => $this->pl->txt('cols_eMentoring_homework'),
            'default' => $this->ementoring,
            'width' => 'auto',
            'sort_field' => 'eMentoring_homework'
        );
        $cols['eMentoring_percentage'] = array(
            'txt' => $this->pl->txt('cols_eMentoring_percentage'),
            'default' => $this->ementoring,
            'width' => 'auto',
            'sort_field' => 'eMentoring_percentage'
        );

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
            'loginname' => $f->table()->column()->text($columns['loginname']['txt'])
                                  ->withIsSortable(false),
            'firstname' => $f->table()->column()->text($columns['firstname']['txt'])
                         ->withHighlight(true),
            'lastname' => $f->table()->column()->text($columns['lastname']['txt']),
            'initial_test_finished' => $f->table()->column()->text($columns['initial_test_finished']['txt'], $this->current_user_date_format),
            'result_qualifing_tests' => $f->table()->column()->text($columns['result_qualifing_tests']['txt']),
            'results_qualifing_tests' => $f->table()->column()->text($columns['results_qualifing_tests']['txt']),
            'eMentoring_finished' => $f->table()->column()->text($columns['eMentoring_finished']['txt']),
            'eMentoring_homework' => $f->table()->column()->text($columns['eMentoring_homework']['txt']),
            'eMentoring_percentage' => $f->table()->column()->text($columns['eMentoring_percentage']['txt']),
        ];
    }

    protected function records()
    {
        $global_configs = new ilParticipationCertificateConfigSets();
        $data = $global_configs->getAllConfigSets();

        $tableData = [];

        $selectableColumns = $this->getSelectableColumns();




        foreach ($data as $configSet) {
            $active = 'inactive';
            $configSetType = '';
            /*foreach ($selectableColumns as $columnKey => $value) {*/
            foreach ($configSet as $key => $value) {
                //if ($this->isColumnSelected($k)) { // TODO

                switch ($key) {
                    case 'order_by':

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
                        }
                        break;
                    default:

                        break;
                }
            }

            $tmp = [
                'configset_type' => $configSetType,
                'title' => $configSet['title'],
                'parent_title' => $configSet['parent_title'],
                'active' => $active
            ];

            $tableData[] = $tmp;
        }

        return $tableData;

    }

    private function getActions()
    {
        global $DIC;

        $f = $DIC['ui.factory'];

        $df = new \ILIAS\Data\Factory();

        /** this is the endpoint for actions, in this case the same page. */
        $here_uri = $df->uri($DIC->http()->request()->getUri()->__toString());

        /**
         * Actions' commands and the row-ids affected are relayed to the server via GET.
         * The URLBuilder orchestrates query-paramters (a.o. by assigning namespace)
         */
        $url_builder = new URLBuilder($here_uri);
        $query_params_namespace = ['config'];

        /**
         * We have to claim those parameters. In return, there is a token to modify
         * the value of the param; the tokens will work only with the given copy
         * of URLBuilder, so acquireParameters will return the builder as first entry,
         * followed by the tokens.
         */
        list($url_builder, $action_parameter_token, $row_id_token) =
            $url_builder->acquireParameters(
                $query_params_namespace,
                'action', //this is the actions's parameter name
                'id'   //this is the parameter name to be used for row-ids
            );

        $actions = [
            'edit' => $f->table()->action()->single(
                'Edit',
                $url_builder->withParameter($action_parameter_token, 'edit'),
                $row_id_token
            ),
            'copy' => $f->table()->action()->single(
                'Copy',
                $url_builder->withParameter($action_parameter_token, 'copy'),
                $row_id_token
            ),
            'delete' =>
                $f->table()->action()->standard(
                    'Delete',
                    $url_builder->withParameter($action_parameter_token, 'delete'),
                    $row_id_token
                ),
            'activate' =>
                $f->table()->action()->standard(
                    'Activate',
                    $url_builder->withParameter($action_parameter_token, 'activate'),
                    $row_id_token
                )
        ];

        return $actions;
    }
}
