<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();
		return;
	}
	
	function index()
	{
		$this->load->model('Ledger_model');
		$this->load->library('accountlist');
		$this->template->set('page_title', 'Welcome to Webzash');
		$this->template->set('add_css', array("css/tufte-graph.css"));
		$this->template->set('add_javascript', array("js/raphael.js", "js/jquery.enumerable.js", "js/jquery.tufte-graph.js"));

		/* Draft voucher count */
		$draft_q = $this->db->query("SELECT * FROM vouchers WHERE draft = 1");
		$data['draft_count'] = $draft_q->num_rows();

		/* Bank and Cash Ledger accounts */
		$bank_q = $this->db->query("SELECT * FROM ledgers WHERE type = ?", array('B'));
		if ($bank_q->num_rows() > 0)
		{
			foreach ($bank_q->result() as $row)
			{
				$data['bank_cash_account'][] = array(
					'id' => $row->id,
					'name' => $row->name,
					'balance' => $this->Ledger_model->get_ledger_balance($row->id),
				);
			}
		} else {
			$data['bank_cash_account'] = array();
		}

		/* Calculating total of Assets, Liabilities, Incomes, Expenses */
		$asset = new Accountlist();
		$asset->init(1);
		$data['asset_total'] = $asset->total;

		$liability = new Accountlist();
		$liability->init(2);
		$data['liability_total'] = $liability->total;

		$data['show_asset_liability'] = TRUE;
		if ($data['asset_total'] == 0 && $data['liability_total'] == 0)
			$data['show_asset_liability'] = FALSE;

		$income = new Accountlist();
		$income->init(3);
		$data['income_total'] = $income->total;

		$expense = new Accountlist();
		$expense->init(4);
		$data['expense_total'] = $expense->total;

		$data['show_income_expense'] = TRUE;
		if ($data['income_total'] == 0 && $data['expense_total'] == 0)
			$data['show_income_expense'] = FALSE;

		/* Getting Log Messages */
		$data['logs'] = $this->logger->read_recent_messages();
		$this->template->load('template', 'welcome', $data);
		return;
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
