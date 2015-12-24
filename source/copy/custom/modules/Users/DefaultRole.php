<?php
class DefaultRole
{
    public function addToUser(SugarBean $bean, $event, $arguments)
    {
        global $sugar_config;
        if(empty($sugar_config['dedicated_bean_role_default'])) {
            return;
        }
        // Only execute on new User record creation
        if (empty($bean->fetched_row['id'])) {
            // Fetch the default role
            $role = new ACLRole();
            $role->retrieve($sugar_config['dedicated_bean_role_default']);
            // Check if the user is already a member of the default role
            $in_role = $bean->check_role_membership($role->name);
            if (!$in_role) {
                // Add user to role, if he/she is not already a member
                $role->set_relationship(
                    'acl_roles_users', 
                    array('role_id' => $role->id, 'user_id' => $bean->id), 
                    false
                    );
            }
        }
    }
}
