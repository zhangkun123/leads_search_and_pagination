<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leads extends CI_Controller {

	protected $view_data = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Lead');
	}

	public function index()
	{
		$this->view_data['leads'] = $this->Lead->get_lead();
		$leads_count = $this->get_leads_count();
		$this->view_data['pages'] = ceil($leads_count / LEAD_LIMIT);

		$this->load->view('leads', $this->view_data);
	}

	protected function get_leads_count($data = NULL)
	{
		//count number of leads on initial load ELSE by name or by date search
		if($data == NULL)
			$leads = $this->Lead->leads_count();
		else if(isset($data['first_name']) OR isset($data['date']))
			$leads = $this->Lead->leads_count($data);

		return $leads['count'];
	}

	public function pagination($pages, $search_data)
	{
		$data['html'] = "";

		//get pagination buttons based on first_name search
		if(isset($search_data['first_name']))
		{
			foreach(range(1, $pages) as $page) 
			{
				$this->view_data = array(
					'search' => $search_data['first_name'],
					'page' => $page
				);

				$data['html'] .= $this->load->view('partials/lead_button', $this->view_data, TRUE);
			}			
		}
		//get pagination buttons based on date search
		else
		{
			$search = $search_data[0] . ',' . $search_data[1];
			foreach(range(1, $pages) as $page)
			{
				$this->view_data = array(
					'search' => $search,
					'page' => $page
				);

				$data['html'] .= $this->load->view('partials/lead_button', $this->view_data, TRUE);
			}
		}

		return $data['html'];
	}

	public function get_leads()
	{
		$post_data = $this->input->post();

		//get leads by pagination
		if($this->input->get('page_number'))
		{
			if($this->input->get('search') != "")
			{
				if(strpos($this->input->get('search'), ',') !== false)
				{
					$lead_data = array(
						"search" => explode(",", $this->input->get('search')),
						"page_number" => ($this->input->get('page_number') -1) * LEAD_LIMIT
					);
				}
				else
				{
					$lead_data = array(
						"search" => $this->input->get('search'),
						"page_number" => ($this->input->get('page_number') -1) * LEAD_LIMIT
					);					
				}
			}
			else
			{
				$lead_data = array(
					"search" => "",
					"page_number" => ($this->input->get('page_number') -1) * LEAD_LIMIT
				);
			}
		}

		//get leads by first_name
		if(isset($post_data['first_name']))
		{
			$lead_data['first_name'] = $post_data['first_name'];
			$leads_count = $this->get_leads_count($post_data);
			$pages = ceil($leads_count / LEAD_LIMIT);
			$data['pagination'] = $this->pagination($pages, $post_data);
		}

		//get leads by date
		if(isset($post_data['from']))
		{
			$lead_data['date'] = array(date('Y-m-d H:i:s', strtotime($post_data['from'])), date('Y-m-d H:i:s', strtotime($post_data['to'])));
			$leads_count = $this->get_leads_count($lead_data);
			$pages = ceil($leads_count / LEAD_LIMIT);
			$data['pagination'] = $this->pagination($pages, $lead_data['date']);
		}

		$leads = $this->Lead->get_lead($lead_data);
		$data['html'] = "";
		foreach($leads as $lead)
		{
			$data['html'] .= $this->load->view('partials/lead_info', $lead, TRUE);
		}

		echo json_encode($data);
	}
}

