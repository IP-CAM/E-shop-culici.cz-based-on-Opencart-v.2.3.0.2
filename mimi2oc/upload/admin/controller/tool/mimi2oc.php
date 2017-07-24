<?php
class ControllerToolMimi2oc extends Controller 
{
 
    public function index() 
    {
        $this->document->setTitle('Synchronizace s mimibazarem');
     
        $data['heading_title'] = 'Synchronizace s mimibazarem';
     
     
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true),
            'separator' => false
        );
        $data['breadcrumbs'][] = array(
            'text'      => 'Synchronizace s mimibazarem',
            'href'      => $this->url->link('tool/mimi2oc', 'token=' . $this->session->data['token'], true),
            'separator' => ' :: '
        );
        
        $data['diff_url'] = $this->url->link('tool/mimi2oc/diff', 'token=' . $this->session->data['token'], true);
          
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('tool/mimi2oc.tpl', $data));
    }
    
    public function diff()
    {
    	$this->document->setTitle('Synchronizace s mimibazarem');
    	 
    	$data['heading_title'] = 'Výsledek porovnání s mimibazarem';
    	 
    	$data['breadcrumbs'] = array();
    	$data['breadcrumbs'][] = array(
    			'text'      => $this->language->get('text_home'),
    			'href'      => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true),
    			'separator' => false
    	);
    	$data['breadcrumbs'][] = array(
    			'text'      => 'Porovnání s mimibazarem',
    			'href'      => $this->url->link('tool/mimi2oc/diff', 'token=' . $this->session->data['token'], true),
    			'separator' => ' :: '
    	);    	
    	
    	$data['sync_url'] = $this->url->link('tool/mimi2oc/sync', 'token=' . $this->session->data['token'], true);
    	
    	$this->load->model('tool/mimi2oc');
    	$diff = $this->model_tool_mimi2oc->diff();
    	
    	$this->session->data['mimi2oc_diff'] = $diff;
    	$data['diff'] = $diff;
    	
    	$data['header'] = $this->load->controller('common/header');
    	$data['column_left'] = $this->load->controller('common/column_left');
    	$data['footer'] = $this->load->controller('common/footer');
    	
    	$this->response->setOutput($this->load->view('tool/mimi2oc_diff.tpl', $data));
    }
    
    public function sync()
    {
    	$this->document->setTitle('Synchronizace s mimibazarem');
    	
    	$data['heading_title'] = 'Synchronizace s mimibazarem';
    	
    	$data['breadcrumbs'] = array();
    	$data['breadcrumbs'][] = array(
    			'text'      => $this->language->get('text_home'),
    			'href'      => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true),
    			'separator' => false
    	);
    	$data['breadcrumbs'][] = array(
    			'text'      => 'Porovnání s mimibazarem',
    			'href'      => $this->url->link('tool/mimi2oc/diff', 'token=' . $this->session->data['token'], true),
    			'separator' => ' :: '
    	);
    	 
    	$data['diff_url'] = $this->url->link('tool/mimi2oc/diff', 'token=' . $this->session->data['token'], true);
    	
    	$diff = isset($this->session->data['mimi2oc_diff']) ? $this->session->data['mimi2oc_diff'] : NULL;
    	unset($this->session->data['mimi2oc_diff']);
    	 
    	$this->load->model('tool/mimi2oc');
    	$sync = $this->model_tool_mimi2oc->sync($diff);
    	$data['sync'] = $sync;
    	 
    	$data['header'] = $this->load->controller('common/header');
    	$data['column_left'] = $this->load->controller('common/column_left');
    	$data['footer'] = $this->load->controller('common/footer');
    	 
    	$this->response->setOutput($this->load->view('tool/mimi2oc_sync.tpl', $data));    	
    }
}
