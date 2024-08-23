<?php
defined('MOODLE_INTERNAL') || die();

class block_logincounter extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_logincounter');
    }

    public function get_content() {
        global $USER, $DB;

        // Define the SQL query for counting logins
        $sql = "SELECT COUNT(*) AS logincount 
                FROM {logstore_standard_log} 
                WHERE action = :action 
                AND userid = :userid 
                AND timecreated > :timecreated";
        
        // Define the parameters for the query
        $params = [
            'action' => 'loggedin',
            'userid' => $USER->id,
            'timecreated' => 1234567890
        ];
        
        // Execute the query and fetch the count
        $login_count = $DB->count_records_sql($sql, $params);

        // Define the SQL query for fetching user details
        $user_sql = "SELECT u.id, u.username, u.lastname, u.firstname, u.lastlogin
                     FROM {user} u
                     WHERE u.id = :userid";

        // Execute the query to fetch user details
        $user_params = ['userid' => $USER->id];
        $user_record = $DB->get_record_sql($user_sql, $user_params);

        // Prepare the content
        $this->content = new stdClass();
        $this->content->text = get_string('login_count', 'block_logincounter', $login_count);
        
        if ($user_record) {
            $this->content->text .= '<br>' . get_string('userid', 'block_logincounter', $user_record->id);
            $this->content->text .= '<br>' . get_string('username', 'block_logincounter', $user_record->username);
            $this->content->text .= '<br>' . get_string('name', 'block_logincounter', $user_record->firstname . ' ' . $user_record->lastname);
            $this->content->text .= '<br>' . get_string('lastlogin', 'block_logincounter', userdate($user_record->lastlogin));
        } else {
            $this->content->text .= '<br>' . get_string('user_not_found', 'block_logincounter');
        }
        
        $this->content->footer = '';

        return $this->content;
    }

    public function applicable_formats() {
        return ['site' => true, 'my' => true];
    }
}

