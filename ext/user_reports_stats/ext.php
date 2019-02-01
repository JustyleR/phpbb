<?php

namespace justyler\user_reports_stats;

class ext extends \phpbb\extension\base  {
	
    public function enable_step($old_state) {
        
		if ($old_state === false) {
			
			$db = $this->container->get('dbal.conn');
			
			// Create the table if it doesnt exists
			$query1 = "
			CREATE TABLE IF NOT EXISTS `users_report_counts` (
			  `report_id` int(11) NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL,
			  `reports_count` int(11) NOT NULL DEFAULT '1',
			  `reports_closed` int(11) NOT NULL DEFAULT '0',
			  `reports_deleted` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY ( report_id )
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			";
			
			// Create the triggers
			$query2 = "
			CREATE OR REPLACE TRIGGER new_report 
				AFTER INSERT ON phpbb_reports
				FOR EACH ROW
			BEGIN
				IF ( SELECT EXISTS(SELECT id FROM users_report_counts r
									WHERE r.user_id = NEW.user_id) ) THEN
					UPDATE users_report_counts r
					SET r.reports_count = r.reports_count + 1
					WHERE r.user_id = NEW.user_id;
				ELSE
					INSERT INTO users_report_counts(user_id, reports_count, reports_closed, reports_deleted) VALUES (NEW.user_id, 1, 0, 0);
				END IF;
			END;
			";
			
			$query3 = "
			CREATE OR REPLACE TRIGGER closed_report 
				AFTER UPDATE ON phpbb_reports
				FOR EACH ROW
			BEGIN
				UPDATE users_report_counts r
				SET r.reports_closed = r.reports_closed + 1
				WHERE r.user_id = NEW.user_id;
			END;
			";
			
			$query4 = "
			CREATE OR REPLACE TRIGGER deleted_report 
				AFTER DELETE ON phpbb_reports
				FOR EACH ROW
			BEGIN
				UPDATE users_report_counts r
				SET r.reports_deleted = r.reports_deleted + 1
				WHERE r.user_id = OLD.user_id;
			END;
			";
			
			for($i = 1; $i <= 4; $i++) {
				$db->sql_query(${'query'.$i});
			}
        }

        return parent::enable_step($old_state);
    }
	
	public function disable_step($old_state) {
		
		if ($old_state === false) {
			$db = $this->container->get('dbal.conn');
			
			// Delete the table and the triggers
			$query1 = "DROP TABLE IF EXISTS users_report_counts;";
			$query2 = "DROP TRIGGER IF EXISTS new_report;";
			$query3 = "DROP TRIGGER IF EXISTS closed_report;";
			$query4 = "DROP TRIGGER IF EXISTS deleted_report;";
			
			for($i = 1; $i <= 4; $i++) {
				$db->sql_query(${'query'.$i});
			}
		}
		return parent::enable_step($old_state);
	}
}