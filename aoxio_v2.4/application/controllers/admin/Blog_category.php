<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Blog_category extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
        //check auth
        if (!is_admin() && !is_user()) {
            redirect(base_url());
        }
    }


    public function index()
    {   
        if (is_admin()) {
            $business_id = 0 ;
        }

        if (is_user()) {
            $business_id = $this->business->uid ;
        }


        $data = array();
        $data['page_title'] = 'Blog Category';      
        $data['page'] = 'Blog';   
        $data['category'] = FALSE;
        $data['languages'] = $this->admin_model->select('language');
        $data['categories'] = $this->admin_model->get_blog_categories($business_id);
        $data['main_content'] = $this->load->view('admin/blog/category',$data,TRUE);
        $this->load->view('admin/index',$data);
    }


    public function add()
    {	
        if($_POST)
        {   
            $id = $this->input->post('id', true);

            if (user()->role == 'admin') {
                $business_id = 0;
            }

            if (user()->role == 'user') {
                $business_id = $this->business->uid;
            }

            //validate inputs
            $this->form_validation->set_rules('name', trans('name'), 'required');

            if ($this->form_validation->run() === false) {
                $this->session->set_flashdata('error', validation_errors());
                redirect(base_url('admin/blog_category'));
            } else {
               
                $data=array(
                    'name' => $this->input->post('name'),
                    'lang_id' => $this->input->post('language'),
                    'business_id' => $business_id,
                    'slug' => str_slug(trim($this->input->post('name'))),
                    'user_id' => $this->session->userdata('id'),
                    'status' => 1
                );
                
                //if id available info will be edited
                if ($id != '') {
                    $this->admin_model->edit_option($data, $id, 'blog_category');
                    $this->session->set_flashdata('msg', trans('updated-successfully')); 
                } else {
                    $id = $this->admin_model->insert($data, 'blog_category');
                    $this->session->set_flashdata('msg', trans('inserted-successfully')); 
                }
                redirect(base_url('admin/blog_category'));

            }
        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit';   
        $data['languages'] = $this->admin_model->select('language');
        $data['category'] = $this->admin_model->select_option($id, 'blog_category');
        $data['main_content'] = $this->load->view('admin/blog/category',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    
    public function active($id) 
    {
        $data = array(
            'status' => 1
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'blog_category');
        $this->session->set_flashdata('msg', trans('activate-successfully')); 
        redirect(base_url('admin/blog_category'));
    }

    public function deactive($id) 
    {
        $data = array(
            'status' => 0
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'blog_category');
        $this->session->set_flashdata('msg', trans('deactivate-successfully')); 
        redirect(base_url('admin/blog_category'));
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'blog_category'); 
        echo json_encode(array('st' => 1));
    }

}
	

