<?php
class ControllerDesignSeoRegex extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('design/seo_regex');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/seo_regex');

		$this->getList();
	}

	public function add() {
		$this->load->language('design/seo_regex');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/seo_regex');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_design_seo_regex->addSeoRegex($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . (int)$this->request->get['page'];
			}

			$this->response->redirect($this->url->link('design/seo_regex', 'user_token=' . $this->session->data['user_token'] . $url));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('design/seo_regex');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/seo_regex');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_design_seo_regex->editSeoRegex($this->request->get['seo_regex_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . (int)$this->request->get['page'];
			}

			$this->response->redirect($this->url->link('design/seo_regex', 'user_token=' . $this->session->data['user_token'] . $url));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('design/seo_regex');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/seo_regex');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $seo_regex_id) {
				$this->model_design_seo_regex->deleteSeoRegex($seo_regex_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . (int)$this->request->get['page'];
			}

			$this->response->redirect($this->url->link('design/seo_regex', 'user_token=' . $this->session->data['user_token'] . $url));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . (int)$this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('design/seo_regex', 'user_token=' . $this->session->data['user_token'] . $url)
		);

		$data['add'] = $this->url->link('design/seo_regex/add', 'user_token=' . $this->session->data['user_token'] . $url);
		$data['delete'] = $this->url->link('design/seo_regex/delete', 'user_token=' . $this->session->data['user_token'] . $url);

		$data['seo_regexes'] = array();

		$filter_data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$seo_regex_total = $this->model_design_seo_regex->getTotalSeoRegexes($filter_data);

		$results = $this->model_design_seo_regex->getSeoRegexes($filter_data);

		foreach ($results as $result) {
			$data['seo_regexes'][] = array(
				'seo_regex_id' => $result['seo_regex_id'],
				'name'         => $result['name'],
				'regex'        => htmlspecialchars($result['regex'], ENT_COMPAT, 'UTF-8'),
				'edit'         => $this->url->link('design/seo_regex/edit', 'user_token=' . $this->session->data['user_token'] . '&seo_regex_id=' . $result['seo_regex_id'] . $url)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$pagination = new Pagination();
		$pagination->total = $seo_regex_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('design/seo_regex', 'user_token=' . $this->session->data['user_token'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($seo_regex_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($seo_regex_total - $this->config->get('config_limit_admin'))) ? $seo_regex_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $seo_regex_total, ceil($seo_regex_total / $this->config->get('config_limit_admin')));

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('design/seo_regex_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['seo_regex_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['regex'])) {
			$data['error_regex'] = $this->error['regex'];
		} else {
			$data['error_regex'] = '';
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . (int)$this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('design/seo_regex', 'user_token=' . $this->session->data['user_token'] . $url)
		);

		if (!isset($this->request->get['seo_regex_id'])) {
			$data['action'] = $this->url->link('design/seo_regex/add', 'user_token=' . $this->session->data['user_token'] . $url);
		} else {
			$data['action'] = $this->url->link('design/seo_regex/edit', 'user_token=' . $this->session->data['user_token'] . '&seo_regex_id=' . $this->request->get['seo_regex_id'] . $url);
		}

		$data['cancel'] = $this->url->link('design/seo_regex', 'user_token=' . $this->session->data['user_token'] . $url);

		if (isset($this->request->get['seo_regex_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$seo_regex_info = $this->model_design_seo_regex->getSeoRegex($this->request->get['seo_regex_id']);
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($seo_regex_info)) {
			$data['name'] = $seo_regex_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['regex'])) {
			$data['regex'] = $this->request->post['regex'];
		} elseif (!empty($seo_regex_info)) {
			$data['regex'] = htmlspecialchars($seo_regex_info['regex'], ENT_COMPAT, 'UTF-8');
		} else {
			$data['regex'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('design/seo_regex_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'design/seo_regex')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['name']) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->request->post['regex']) {
			$this->error['regex'] = $this->language->get('error_regex');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'design/seo_regex')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}