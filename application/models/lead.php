<?php

class Lead extends CI_Model{

	public function get_lead($lead_data = NULL)
	{
		$sql = "SELECT * FROM leads LIMIT ? OFFSET ?";
		//initial leads
		if($lead_data == NULL)
			$where = array(LEAD_LIMIT, 0);
		//if user attempts to search
		else if(isset($lead_data['page_number']) && isset($lead_data['search']))
		{
			//initial load of leads search
			if($lead_data['search'] == "")
				$where = array(LEAD_LIMIT, $lead_data['page_number']);
			else
			{
				if(is_array($lead_data['search']))
				{
					$sql = "SELECT * FROM leads WHERE registered_datetime >= ? AND registered_datetime <= ? LIMIT ? OFFSET ?";
					$where = array($lead_data['search'][0], $lead_data['search'][1], LEAD_LIMIT, $lead_data['page_number']);
				}
				else
				{
					$sql = "SELECT * FROM leads WHERE first_name LIKE ? LIMIT ? OFFSET ?";
					$where = array('%'.$lead_data['search'].'%', LEAD_LIMIT, $lead_data['page_number']);						
				}
			}
		}

		//get leads using first_name
		else if(isset($lead_data['first_name']))
		{
			$sql = "SELECT * FROM leads WHERE first_name LIKE ? LIMIT ?";
			$where = array('%'.$lead_data['first_name'].'%', LEAD_LIMIT);
		}

		//get leads using date range
		else if(isset($lead_data['date']))
		{
			$sql = "SELECT * FROM leads WHERE registered_datetime >= ? AND registered_datetime <= ? LIMIT ?";
			$where = array($lead_data['date'][0], $lead_data['date'][1], LEAD_LIMIT);
		}

		return $this->db->query($sql, $where)->result_array();
	}

	public function leads_count($data = NULL)
	{
		if($data == NULL)
		{
			$sql = "SELECT count(leads_id) as count FROM leads";
			$where = "";
		}
		else if(isset($data['first_name']))
		{
			$sql = "SELECT count(leads_id) as count FROM leads WHERE first_name LIKE ?";
			$where = '%'.$data['first_name'].'%';
		}
		else if(isset($data['date']))
		{
			$sql = "SELECT count(leads_id) as count FROM leads WHERE registered_datetime >= ? AND registered_datetime <= ?";
			$where = array($data['date'][0], $data['date'][1]);
		}

		return $this->db->query($sql, $where)->row_array();
	}
}