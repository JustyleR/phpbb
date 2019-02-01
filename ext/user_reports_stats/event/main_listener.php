<?php

namespace justyler\user_reports_stats\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface {
    
	protected $db;
	protected $user;
	protected $template;
	protected $auth;
	protected $request;
	
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\user $user, \phpbb\template\template $template, \phpbb\auth\auth $auth, \phpbb\request\request $request) {
		$this->db = $db;
		$this->user = $user;
		$this->template = $template;
		$this->auth = $auth;
		$this->request = $request;
	}
	
	static public function getSubscribedEvents() {
        return array(
			'core.memberlist_view_profile'	=> 'memberlist_view_profile',
        );
    }
	
    public function memberlist_view_profile($event) {
		$user_id = $this->request->variable('u', '');
		
		// Get user reports
		$get = $this->db->sql_query("SELECT * FROM users_report_counts WHERE user_id = '". $user_id ."'");
		
		$row = $this->db->sql_fetchrow($get);
		if($row == NULL) {
			$reports	= 0;
			$closed		= 0;
			$deleted	= 0;
		} else {
			$reports	= $row['reports_count'];
			$closed		= $row['reports_closed'];
			$deleted	= $row['reports_deleted'];
		}
		
		if($this->auth->acl_gets('a_', 'm_') == 1) {
			$access = 1;
		} else { $access = 0; }
		
		$this->template->assign_vars(array(
			'REPORTS_COUNT'	=> $reports,
			'REPORTS_CLOSED' => $closed,
			'REPORTS_DELETED' => $deleted,
			'REPORTS_ACCESS' => $access
		));
    }
	
}