<?php

class xaliStates
{

    /**
     * @param array $arr_usr_ids
     *
     * @return xaliState[]
     */
    public static function getData(array $arr_usr_ids = array(), int $group_ref_id)
    {
        global $DIC;

        $items = $DIC->repositoryTree()->getChildIds($group_ref_id);

        $arr_xali_status = [];
        foreach ($items as $ref_id) {
            if (ilObject::_lookupType($ref_id, true) != 'xali') {
                continue;
            }
            $object_id = ilObject::_lookupObjectId($ref_id);
            /**
             * @var xaliChecklist $checklist
             */
            $total_checklist = xaliChecklist::where(['obj_id' => $object_id])->count();

            foreach ($arr_usr_ids as $usr_id) {
                $xaliUserStatus = xaliUserStatus::getInstance($usr_id, $object_id);
                $arr_xali_status[$usr_id]['present'] = (int) $arr_xali_status[$usr_id]['present'] + $xaliUserStatus->getAttendanceStatuses(xaliChecklistEntry::STATUS_PRESENT);
                $arr_xali_status[$usr_id]['total'] = $arr_xali_status[$usr_id]['total'] + $total_checklist - $xaliUserStatus->getAttendanceStatuses(xaliChecklistEntry::STATUS_NOT_RELEVANT);
            }
        }


        foreach ($arr_xali_status as $usr_id => $xali_state) {
            $state = new xaliState();
            $state->setUsrId($usr_id);
            $state->setPassed($xali_state['present']);
            $state->setTotal($xali_state['total']);
            $data[$usr_id] = $state;
        }


        return $data;
    }
}